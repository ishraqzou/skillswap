using System.IO;
using System.Collections.ObjectModel;
using System.Windows.Input;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// StatisticsViewModel — analyse de l'activité et export (UC-29, FR-122 à FR-129).
/// </summary>
public class StatisticsViewModel : ViewModelBase
{
    private readonly StatistiquesRepository _statistiquesRepository = new();

    private StatistiquesGlobales _statistiques = new();
    private string _message = string.Empty;
    private bool _messageEstErreur;

    public StatistiquesGlobales Statistiques
    {
        get => _statistiques;
        set => SetProperty(ref _statistiques, value);
    }

    public ObservableCollection<RepartitionCategorie> Repartition { get; } = new();

    public string Message
    {
        get => _message;
        set { SetProperty(ref _message, value); OnPropertyChanged(nameof(ADesMessages)); }
    }

    public bool MessageEstErreur
    {
        get => _messageEstErreur;
        set => SetProperty(ref _messageEstErreur, value);
    }

    public bool ADesMessages => !string.IsNullOrEmpty(Message);

    public ICommand RafraichirCommand { get; }
    public ICommand ExporterCsvCommand { get; }

    public StatisticsViewModel()
    {
        RafraichirCommand  = new RelayCommand(Charger);
        ExporterCsvCommand = new RelayCommand(ExporterCsv);
        Charger();
    }

    public void Charger()
    {
        try
        {
            Statistiques = _statistiquesRepository.GetStatistiques();

            Repartition.Clear();
            foreach (var ligne in _statistiquesRepository.GetRepartitionParCategorie())
            {
                Repartition.Add(ligne);
            }
            Message = string.Empty;
        }
        catch (Exception ex)
        {
            MessageEstErreur = true;
            Message = "Chargement des statistiques impossible : " + ex.Message;
        }
    }

    /// <summary>Exporte les indicateurs au format CSV (FR-128).</summary>
    private void ExporterCsv()
    {
        try
        {
            var dialogue = new Microsoft.Win32.SaveFileDialog
            {
                FileName = $"skillswap_statistiques_{DateTime.Now:yyyyMMdd}",
                DefaultExt = ".csv",
                Filter = "Fichier CSV (*.csv)|*.csv",
            };

            if (dialogue.ShowDialog() != true)
            {
                return;
            }

            var lignes = new List<string>
            {
                "Indicateur;Valeur",
                $"Utilisateurs inscrits;{Statistiques.NbUtilisateurs}",
                $"Utilisateurs suspendus;{Statistiques.NbUtilisateursSuspendus}",
                $"Competences actives;{Statistiques.NbCompetencesActives}",
                $"Demandes en attente;{Statistiques.NbDemandesEnAttente}",
                $"Demandes acceptees;{Statistiques.NbDemandesAcceptees}",
                $"Seances terminees;{Statistiques.NbSeancesTerminees}",
                $"Certificats actifs;{Statistiques.NbCertificatsActifs}",
                $"Certificats revoques;{Statistiques.NbCertificatsRevoques}",
                $"Signalements en attente;{Statistiques.NbSignalementsEnAttente}",
                $"Total points attribues;{Statistiques.TotalPointsAttribues}",
                string.Empty,
                "Categorie;Nombre de competences;Pourcentage",
            };

            lignes.AddRange(Repartition.Select(
                r => $"{r.Categorie};{r.NbCompetences};{r.Pourcentage}"));

            File.WriteAllLines(dialogue.FileName, lignes, System.Text.Encoding.UTF8);

            MessageEstErreur = false;
            Message = "Statistiques exportées vers : " + dialogue.FileName;
        }
        catch (Exception ex)
        {
            MessageEstErreur = true;
            Message = "Export impossible : " + ex.Message;
        }
    }
}
