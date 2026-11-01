namespace SkillSwap.Desktop.Models;

/// <summary>
/// Signalement — déclaration soumise à modération (BR-059 à BR-064).
/// </summary>
public class Signalement
{
    public int IdSignalement { get; set; }
    public string Motif { get; set; } = string.Empty;
    public DateTime DateCreation { get; set; }
    public string Statut { get; set; } = "EN_ATTENTE";
    public string? Decision { get; set; }
    public DateTime? DateTraitement { get; set; }
    public string CibleType { get; set; } = string.Empty;
    public int CibleId { get; set; }
    public int IdAuteur { get; set; }
    public int? IdAdministrateur { get; set; }

    public string AuteurNomComplet { get; set; } = string.Empty;

    public string CibleAffichee => $"{CibleType} #{CibleId}";
    public bool EstEnAttente => Statut == "EN_ATTENTE";
}
