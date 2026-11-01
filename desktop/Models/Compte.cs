namespace SkillSwap.Desktop.Models;

/// <summary>
/// Compte — table racine d'authentification (Tâche 12).
/// </summary>
public class Compte
{
    public int IdCompte { get; set; }
    public string Email { get; set; } = string.Empty;
    public string MotDePasseHash { get; set; } = string.Empty;
    public DateTime DateCreation { get; set; }
    public string Statut { get; set; } = "ACTIF";
    public string TypeCompte { get; set; } = "UTILISATEUR";

    public bool EstActif => Statut == "ACTIF";
    public bool EstAdministrateur => TypeCompte == "ADMIN";
}
