using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// StatistiquesRepository — indicateurs agrégés de la plateforme
/// (UC-28, UC-29, FR-122 à FR-129).
/// Conformément à BR-065, seules des données agrégées sont manipulées ici.
/// </summary>
public class StatistiquesRepository
{
    public StatistiquesGlobales GetStatistiques() => new()
    {
        NbUtilisateurs =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM compte WHERE typeCompte = 'UTILISATEUR'"),
        NbUtilisateursSuspendus =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM compte WHERE typeCompte = 'UTILISATEUR' AND statut = 'SUSPENDU'"),
        NbCompetencesActives =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM competence WHERE statut = 'ACTIVE'"),
        NbDemandesEnAttente =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM demande_echange WHERE statut = 'EN_ATTENTE'"),
        NbDemandesAcceptees =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM demande_echange WHERE statut = 'ACCEPTEE'"),
        NbSeancesTerminees =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM seance WHERE statut = 'TERMINEE'"),
        NbCertificatsActifs =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM certificat WHERE statut = 'ACTIF'"),
        NbCertificatsRevoques =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM certificat WHERE statut = 'REVOQUE'"),
        NbSignalementsEnAttente =
            DatabaseHelper.ExecuteScalarInt("SELECT COUNT(*) FROM signalement WHERE statut = 'EN_ATTENTE'"),
        TotalPointsAttribues =
            DatabaseHelper.ExecuteScalarInt("SELECT COALESCE(SUM(montant), 0) FROM point"),
    };

    /// <summary>Répartition des compétences actives par catégorie (UC-29).</summary>
    public List<RepartitionCategorie> GetRepartitionParCategorie()
    {
        const string sql = @"
            SELECT ca.nom AS categorie, COUNT(co.idCompetence) AS nbCompetences
            FROM categorie ca
            LEFT JOIN competence co ON co.idCategorie = ca.idCategorie AND co.statut = 'ACTIVE'
            GROUP BY ca.idCategorie, ca.nom
            ORDER BY nbCompetences DESC";

        var lignes = DatabaseHelper.Query(sql, null, r => new RepartitionCategorie
        {
            Categorie     = DatabaseHelper.GetString(r, "categorie"),
            NbCompetences = DatabaseHelper.GetInt(r, "nbCompetences"),
        });

        int total = lignes.Sum(l => l.NbCompetences);
        foreach (var ligne in lignes)
        {
            ligne.Pourcentage = total == 0 ? 0 : Math.Round(ligne.NbCompetences * 100.0 / total, 1);
        }
        return lignes;
    }
}
