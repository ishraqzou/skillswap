using System.Collections.ObjectModel;
using System.Windows.Input;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;
using SkillSwap.Desktop.Services;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// ReportsViewModel — instruction et clôture des signalements (UC-25).
/// </summary>
public class ReportsViewModel : ViewModelBase
{
    private readonly SignalementRepository _signalementRepository = new();
    private readonly ModerationService _moderationService = new();

    private Signalement? _signalementSelectionne;
    private string _motifDecision = string.Empty;
    private bool _afficherTraites;
    private string _message = string.Empty;
    private bool _messageEstErreur;

    public ObservableCollection<Signalement> Signalements { get; } = new();

    public Signalement? SignalementSelectionne
    {
        get => _signalementSelectionne;
        set
        {
            if (SetProperty(ref _signalementSelectionne, value))
            {
                OnPropertyChanged(nameof(UnSignalementEstSelectionne));
            }
        }
    }

    public bool UnSignalementEstSelectionne => SignalementSelectionne != null;

    public string MotifDecision
    {
        get => _motifDecision;
        set => SetProperty(ref _motifDecision, value);
    }

    public bool AfficherTraites
    {
        get => _afficherTraites;
        set { if (SetProperty(ref _afficherTraites, value)) Charger(); }
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

    public ICommand RafraichirCommand { get; }
    public ICommand CloturerFondeCommand { get; }
    public ICommand CloturerNonFondeCommand { get; }

    public ReportsViewModel()
    {
        RafraichirCommand       = new RelayCommand(Charger);
        CloturerFondeCommand    = new RelayCommand(() => Traiter(true),  () => UnSignalementEstSelectionne);
        CloturerNonFondeCommand = new RelayCommand(() => Traiter(false), () => UnSignalementEstSelectionne);
        Charger();
    }

    public void Charger()
    {
        try
        {
            Signalements.Clear();
            var source = AfficherTraites
                ? _signalementRepository.GetAll()
                : _signalementRepository.GetEnAttente();

            foreach (var signalement in source)
            {
                Signalements.Add(signalement);
            }
            Message = string.Empty;
        }
        catch (Exception ex)
        {
            MessageEstErreur = true;
            Message = "Chargement impossible : " + ex.Message;
        }
    }

    private void Traiter(bool estFonde)
    {
        if (SignalementSelectionne == null) return;

        if (!SignalementSelectionne.EstEnAttente)
        {
            MessageEstErreur = true;
            Message = "Ce signalement a déjà été clôturé.";
            return;
        }

        if (_moderationService.TraiterSignalement(
                SignalementSelectionne, estFonde, MotifDecision, out var message))
        {
            MessageEstErreur = false;
            Message = message;
            MotifDecision = string.Empty;
            Charger();
        }
        else
        {
            MessageEstErreur = true;
            Message = message;
        }
    }
}
