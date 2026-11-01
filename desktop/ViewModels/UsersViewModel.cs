using System.Collections.ObjectModel;
using System.Windows.Input;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;
using SkillSwap.Desktop.Services;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// UsersViewModel — gestion des comptes utilisateurs et ajustement manuel
/// des points (UC-21, UC-22, UC-26).
/// </summary>
public class UsersViewModel : ViewModelBase
{
    private readonly UtilisateurRepository _utilisateurRepository = new();
    private readonly ModerationService _moderationService = new();
    private readonly PointService _pointService = new();

    private string _recherche = string.Empty;
    private Utilisateur? _utilisateurSelectionne;
    private string _montantAjustement = string.Empty;
    private string _motifAjustement = string.Empty;
    private string _message = string.Empty;
    private bool _messageEstErreur;

    public ObservableCollection<Utilisateur> Utilisateurs { get; } = new();
    public ObservableCollection<LignePoint> HistoriquePoints { get; } = new();

    public string Recherche
    {
        get => _recherche;
        set => SetProperty(ref _recherche, value);
    }

    public Utilisateur? UtilisateurSelectionne
    {
        get => _utilisateurSelectionne;
        set
        {
            if (SetProperty(ref _utilisateurSelectionne, value))
            {
                OnPropertyChanged(nameof(UnUtilisateurEstSelectionne));
                ChargerHistorique();
            }
        }
    }

    public bool UnUtilisateurEstSelectionne => UtilisateurSelectionne != null;

    public string MontantAjustement
    {
        get => _montantAjustement;
        set => SetProperty(ref _montantAjustement, value);
    }

    public string MotifAjustement
    {
        get => _motifAjustement;
        set => SetProperty(ref _motifAjustement, value);
    }

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

    public ICommand RechercherCommand { get; }
    public ICommand RafraichirCommand { get; }
    public ICommand BasculerStatutCommand { get; }
    public ICommand AjusterPointsCommand { get; }

    public UsersViewModel()
    {
        RechercherCommand     = new RelayCommand(Rechercher);
        RafraichirCommand     = new RelayCommand(Charger);
        BasculerStatutCommand = new RelayCommand(BasculerStatut, () => UnUtilisateurEstSelectionne);
        AjusterPointsCommand  = new RelayCommand(AjusterPoints, () => UnUtilisateurEstSelectionne);
        Charger();
    }

    public void Charger()
    {
        try
        {
            Utilisateurs.Clear();
            foreach (var utilisateur in _utilisateurRepository.GetAll())
            {
                Utilisateurs.Add(utilisateur);
            }
            Message = string.Empty;
        }
        catch (Exception ex)
        {
            AfficherErreur("Chargement impossible : " + ex.Message);
        }
    }

    private void Rechercher()
    {
        try
        {
            Utilisateurs.Clear();
            var resultats = string.IsNullOrWhiteSpace(Recherche)
                ? _utilisateurRepository.GetAll()
                : _utilisateurRepository.Search(Recherche.Trim());

            foreach (var utilisateur in resultats)
            {
                Utilisateurs.Add(utilisateur);
            }
        }
        catch (Exception ex)
        {
            AfficherErreur("Recherche impossible : " + ex.Message);
        }
    }

    private void ChargerHistorique()
    {
        HistoriquePoints.Clear();
        if (UtilisateurSelectionne == null)
        {
            return;
        }

        try
        {
            foreach (var ligne in _pointService.GetHistorique(UtilisateurSelectionne.IdCompte))
            {
                HistoriquePoints.Add(ligne);
            }
        }
        catch (Exception ex)
        {
            AfficherErreur("Historique indisponible : " + ex.Message);
        }
    }

    private void BasculerStatut()
    {
        if (UtilisateurSelectionne == null) return;

        if (_moderationService.BasculerStatutCompte(UtilisateurSelectionne, out var message))
        {
            AfficherSucces(message);
            var idSelection = UtilisateurSelectionne.IdCompte;
            Charger();
            UtilisateurSelectionne = Utilisateurs.FirstOrDefault(u => u.IdCompte == idSelection);
        }
        else
        {
            AfficherErreur(message);
        }
    }

    private void AjusterPoints()
    {
        if (UtilisateurSelectionne == null) return;

        if (!int.TryParse(MontantAjustement, out var montant))
        {
            AfficherErreur("Le montant doit être un nombre entier (positif ou négatif).");
            return;
        }

        if (_pointService.AjusterManuel(UtilisateurSelectionne, montant, MotifAjustement, out var message))
        {
            AfficherSucces(message);
            MontantAjustement = string.Empty;
            MotifAjustement = string.Empty;

            var idSelection = UtilisateurSelectionne.IdCompte;
            Charger();
            UtilisateurSelectionne = Utilisateurs.FirstOrDefault(u => u.IdCompte == idSelection);
        }
        else
        {
            AfficherErreur(message);
        }
    }

    private void AfficherSucces(string message)
    {
        MessageEstErreur = false;
        Message = message;
    }

    private void AfficherErreur(string message)
    {
        MessageEstErreur = true;
        Message = message;
    }
}
