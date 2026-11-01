namespace SkillSwap.Desktop.Models;

/// <summary>
/// LignePoint — ligne d'historique du système de points (BR-039 à BR-046).
/// Nommée LignePoint (et non Point) pour éviter toute confusion avec
/// System.Windows.Point du framework WPF.
/// </summary>
public class LignePoint
{
    public int IdPoint { get; set; }
    public int Montant { get; set; }
    public string Type { get; set; } = "ATTRIBUTION";
    public DateTime DateCreation { get; set; }
    public string? Motif { get; set; }
    public int IdUtilisateur { get; set; }
    public int? IdSeance { get; set; }
    public int? IdAdministrateur { get; set; }

    public string UtilisateurNomComplet { get; set; } = string.Empty;

    public string TypeAffiche => Type == "ATTRIBUTION" ? "Séance complétée" : "Ajustement administratif";
    public string MontantAffiche => Montant >= 0 ? $"+{Montant}" : Montant.ToString();
}
