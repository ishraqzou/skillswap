using System.Collections.ObjectModel;
using System.Windows.Input;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// DashboardViewModel — tableau de bord de supervision (UC-28, FR-029).
/// </summary>
public class DashboardViewModel : ViewModelBase
{
    private readonly StatistiquesRepository _statistiquesRepository = new();
    private readonly SignalementRepository _signalementRepository = new();

    private StatistiquesGlobales _statistiques = new();
    private string _messageErreur = string.Empty;

    public StatistiquesGlobales Statistiques
    {
        get => _statistiques;
        set => SetProperty(ref _statistiques, value);
    }

    public ObservableCollection<Signalement> SignalementsRecents { get; } = new();

    public string MessageErreur
    {
        get => _messageErreur;
        set { SetProperty(ref _messageErreur, value); OnPropertyChanged(nameof(ADesErreurs)); }
    }

    public bool ADesErreurs => !string.IsNullOrEmpty(MessageErreur);

    public ICommand RafraichirCommand { get; }

    public DashboardViewModel()
    {
        RafraichirCommand = new RelayCommand(Charger);
        Charger();
    }

    public void Charger()
    {
        try
        {
            MessageErreur = string.Empty;
            Statistiques = _statistiquesRepository.GetStatistiques();

            SignalementsRecents.Clear();
            foreach (var signalement in _signalementRepository.GetEnAttente().Take(5))
            {
                SignalementsRecents.Add(signalement);
            }
        }
        catch (Exception ex)
        {
            MessageErreur = "Impossible de charger le tableau de bord : " + ex.Message;
        }
    }
}
