namespace SkillSwap.Desktop.Models;

/// <summary>
/// RepartitionCategorie — nombre de compétences actives par catégorie,
/// utilisé par le module statistiques (UC-29).
/// </summary>
public class RepartitionCategorie
{
    public string Categorie { get; set; } = string.Empty;
    public int NbCompetences { get; set; }
    public double Pourcentage { get; set; }
}
