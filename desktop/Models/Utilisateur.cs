namespace SkillSwap.Desktop.Models;

/// <summary>
/// Utilisateur — sous-type de Compte (Tâche 8 / Tâche 12).
/// </summary>
public class Utilisateur
{
    public int IdCompte { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string Prenom { get; set; } = string.Empty;
    public string? PhotoProfil { get; set; }
    public string? Biographie { get; set; }
    public string? Ville { get; set; }
    public string? LanguesParlees { get; set; }
    public int SoldePoints { get; set; }
    public decimal? NoteMoyenne { get; set; }
    public string VisibiliteProfil { get; set; } = "PUBLIQUE";

    // Données jointes depuis la table compte (pour l'affichage en liste)
    public string Email { get; set; } = string.Empty;
    public string Statut { get; set; } = "ACTIF";
    public DateTime DateCreation { get; set; }

    public string NomComplet => $"{Prenom} {Nom}";
    public string NoteAffichee => NoteMoyenne.HasValue ? $"{NoteMoyenne.Value:0.0} / 5" : "—";
    public bool EstActif => Statut == "ACTIF";
}
