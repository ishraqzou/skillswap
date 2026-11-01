using System.Windows.Input;
using SkillSwap.Desktop.Services;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// MainViewModel — fenêtre principale de l'espace d'administration.
/// Orchestre la navigation entre les différents modules (UC-21 à UC-29).
/// </summary>
public class MainViewModel : ViewModelBase
{
    private readonly AuthService _authService = new();

    private ViewModelBase? _vueCourante;
    private string _titreSection = "Tableau de bord";

    public ViewModelBase? VueCourante
    {
        get => _vueCourante;
        set => SetProperty(ref _vueCourante, value);
    }

    public string TitreSection
    {
        get => _titreSection;
        set => SetProperty(ref _titreSection, value);
    }

    public string NomAdministrateur =>
        SessionAdministrateur.Courant?.NomComplet ?? "Administrateur";

    public string InitialesAdministrateur
    {
        get
        {
            var nom = NomAdministrateur.Trim();
            if (string.IsNullOrEmpty(nom)) return "AD";

            var parties = nom.Split(' ', StringSplitOptions.RemoveEmptyEntries);
            return parties.Length >= 2
                ? $"{char.ToUpper(parties[0][0])}{char.ToUpper(parties[1][0])}"
                : nom[..Math.Min(2, nom.Length)].ToUpper();
        }
    }

    public ICommand AfficherDashboardCommand { get; }
    public ICommand AfficherUtilisateursCommand { get; }
    public ICommand AfficherSignalementsCommand { get; }
    public ICommand AfficherCertificatsCommand { get; }
    public ICommand AfficherStatistiquesCommand { get; }
    public ICommand DeconnexionCommand { get; }

    /// <summary>Déclenché à la déconnexion, pour revenir à l'écran de connexion.</summary>
    public event EventHandler? DeconnexionDemandee;

    public MainViewModel()
    {
        AfficherDashboardCommand   = new RelayCommand(() => Naviguer(new DashboardViewModel(),    "Tableau de bord"));
        AfficherUtilisateursCommand= new RelayCommand(() => Naviguer(new UsersViewModel(),        "Gestion des utilisateurs"));
        AfficherSignalementsCommand= new RelayCommand(() => Naviguer(new ReportsViewModel(),      "Signalements"));
        AfficherCertificatsCommand = new RelayCommand(() => Naviguer(new CertificatesViewModel(), "Gestion des certificats"));
        AfficherStatistiquesCommand= new RelayCommand(() => Naviguer(new StatisticsViewModel(),   "Statistiques"));
        DeconnexionCommand         = new RelayCommand(Deconnecter);

        Naviguer(new DashboardViewModel(), "Tableau de bord");
    }

    private void Naviguer(ViewModelBase viewModel, string titre)
    {
        VueCourante = viewModel;
        TitreSection = titre;
    }

    private void Deconnecter()
    {
        _authService.Deconnecter();
        DeconnexionDemandee?.Invoke(this, EventArgs.Empty);
    }
}
