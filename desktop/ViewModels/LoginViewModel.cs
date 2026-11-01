using System.Windows.Input;
using SkillSwap.Desktop.Services;

namespace SkillSwap.Desktop.ViewModels;

/// <summary>
/// LoginViewModel — écran d'authentification administrateur (UC-20).
/// </summary>
public class LoginViewModel : ViewModelBase
{
    private readonly AuthService _authService = new();

    private string _email = string.Empty;
    private string _messageErreur = string.Empty;
    private bool _enCours;

    public string Email
    {
        get => _email;
        set => SetProperty(ref _email, value);
    }

    public string MessageErreur
    {
        get => _messageErreur;
        set { SetProperty(ref _messageErreur, value); OnPropertyChanged(nameof(ADesErreurs)); }
    }

    public bool ADesErreurs => !string.IsNullOrEmpty(MessageErreur);

    public bool EnCours
    {
        get => _enCours;
        set => SetProperty(ref _enCours, value);
    }

    public ICommand ConnexionCommand { get; }

    /// <summary>Déclenché lorsque l'authentification a réussi.</summary>
    public event EventHandler? ConnexionReussie;

    public LoginViewModel()
    {
        // Le mot de passe n'est jamais stocké dans une propriété liée :
        // il est transmis directement depuis le PasswordBox (NFR-053).
        ConnexionCommand = new RelayCommand(parametre => Connecter(parametre as string ?? string.Empty));
    }

    private void Connecter(string motDePasse)
    {
        MessageErreur = string.Empty;
        EnCours = true;

        try
        {
            var resultat = _authService.Authentifier(Email, motDePasse);
            if (resultat.Succes)
            {
                ConnexionReussie?.Invoke(this, EventArgs.Empty);
            }
            else
            {
                MessageErreur = resultat.Message;
            }
        }
        catch (Exception ex)
        {
            MessageErreur = "Connexion impossible : " + ex.Message;
        }
        finally
        {
            EnCours = false;
        }
    }
}
