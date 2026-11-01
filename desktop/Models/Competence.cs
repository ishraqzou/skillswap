namespace SkillSwap.Desktop.Models;

/// <summary>
/// Competence — compétence offerte ou demandée (BR-011 à BR-016).
/// </summary>
public class Competence
{
    public int IdCompetence { get; set; }
    public string Titre { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public string Type { get; set; } = "OFFERTE";
    public string? Niveau { get; set; }
    public string? MotsCles { get; set; }
    public string Statut { get; set; } = "ACTIVE";
    public DateTime DateCreation { get; set; }
    public int IdCompte { get; set; }
    public int IdCategorie { get; set; }

    public string CategorieNom { get; set; } = string.Empty;
    public string AuteurNomComplet { get; set; } = string.Empty;

    public string TypeAffiche => Type == "OFFERTE" ? "Offerte" : "Demandée";
}
