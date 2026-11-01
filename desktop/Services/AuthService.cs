using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Services;

/// <summary>
/// AuthService — authentification de l'administrateur sur l'application
/// bureautique (UC-20, FR-021, FR-040).
///
/// Conformément à FR-040 et à la séparation des accès définie à la Tâche 4,
/// seuls les comptes de type ADMIN peuvent ouvrir une session ici : un
/// compte utilisateur valide est explicitement refusé.
/// </summary>
public class AuthService
{
    private readonly CompteRepository _compteRepository = new();

    public record ResultatConnexion(bool Succes, string Message, Administrateur? Administrateur);

    public ResultatConnexion Authentifier(string email, string motDePasse)
    {
        if (string.IsNullOrWhiteSpace(email) || string.IsNullOrWhiteSpace(motDePasse))
        {
            return new ResultatConnexion(false, "Veuillez saisir votre adresse e-mail et votre mot de passe.", null);
        }

        var compte = _compteRepository.FindByEmail(email.Trim());

        // Message volontairement générique : ne jamais indiquer lequel des deux
        // champs est erroné (FR-036).
        if (compte == null || !PasswordHasher.Verify(motDePasse, compte.MotDePasseHash))
        {
            return new ResultatConnexion(false, "Adresse e-mail ou mot de passe incorrect.", null);
        }

        if (!compte.EstAdministrateur)
        {
            return new ResultatConnexion(false,
                "Cet espace est réservé aux administrateurs de la plateforme.", null);
        }

        if (!compte.EstActif)
        {
            return new ResultatConnexion(false, "Ce compte administrateur est actuellement désactivé.", null);
        }

        var administrateur = _compteRepository.FindAdministrateur(compte.IdCompte);
        if (administrateur == null)
        {
            return new ResultatConnexion(false,
                "Le profil administrateur associé à ce compte est introuvable.", null);
        }

        SessionAdministrateur.Ouvrir(administrateur);
        return new ResultatConnexion(true, "Connexion réussie.", administrateur);
    }

    public void Deconnecter() => SessionAdministrateur.Fermer();
}
