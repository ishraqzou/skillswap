namespace SkillSwap.Desktop.Models;

/// <summary>
/// StatistiquesGlobales — indicateurs agrégés de la plateforme
/// (UC-29, FR-122 à FR-129, BR-065 à BR-067).
/// </summary>
public class StatistiquesGlobales
{
    public int NbUtilisateurs { get; set; }
    public int NbUtilisateursSuspendus { get; set; }
    public int NbCompetencesActives { get; set; }
    public int NbDemandesEnAttente { get; set; }
    public int NbDemandesAcceptees { get; set; }
    public int NbSeancesTerminees { get; set; }
    public int NbCertificatsActifs { get; set; }
    public int NbCertificatsRevoques { get; set; }
    public int NbSignalementsEnAttente { get; set; }
    public int TotalPointsAttribues { get; set; }
}
