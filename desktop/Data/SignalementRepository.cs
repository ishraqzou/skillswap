using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// SignalementRepository — traitement des signalements (UC-25, BR-059 à BR-061).
/// </summary>
public class SignalementRepository
{
    private const string BaseSelect = @"
        SELECT s.idSignalement, s.motif, s.dateCreation, s.statut, s.decision,
               s.dateTraitement, s.cibleType, s.cibleId, s.idAuteur, s.idAdministrateur,
               CONCAT(u.prenom, ' ', u.nom) AS auteurNomComplet
        FROM signalement s
        INNER JOIN utilisateur u ON u.idCompte = s.idAuteur";

    private static Signalement Map(System.Data.IDataRecord r) => new()
    {
        IdSignalement    = DatabaseHelper.GetInt(r, "idSignalement"),
        Motif            = DatabaseHelper.GetString(r, "motif"),
        DateCreation     = DatabaseHelper.GetDateTime(r, "dateCreation"),
        Statut           = DatabaseHelper.GetString(r, "statut"),
        Decision         = DatabaseHelper.GetNullableString(r, "decision"),
        DateTraitement   = DatabaseHelper.GetNullableDateTime(r, "dateTraitement"),
        CibleType        = DatabaseHelper.GetString(r, "cibleType"),
        CibleId          = DatabaseHelper.GetInt(r, "cibleId"),
        IdAuteur         = DatabaseHelper.GetInt(r, "idAuteur"),
        IdAdministrateur = DatabaseHelper.GetNullableInt(r, "idAdministrateur"),
        AuteurNomComplet = DatabaseHelper.GetString(r, "auteurNomComplet"),
    };

    public List<Signalement> GetEnAttente()
        => DatabaseHelper.Query(BaseSelect + " WHERE s.statut = 'EN_ATTENTE' ORDER BY s.dateCreation ASC",
            null, Map);

    public List<Signalement> GetAll()
        => DatabaseHelper.Query(BaseSelect + " ORDER BY s.dateCreation DESC", null, Map);

    /// <summary>
    /// Clôture un signalement en journalisant la décision et son auteur (BR-060, BR-061).
    /// </summary>
    public int Traiter(int idSignalement, int idAdministrateur, string decision)
    {
        const string sql = @"UPDATE signalement
                             SET statut = 'CLOTURE',
                                 decision = @decision,
                                 dateTraitement = NOW(),
                                 idAdministrateur = @idAdmin
                             WHERE idSignalement = @id";

        return DatabaseHelper.Execute(sql, p =>
        {
            p.AddWithValue("@decision", decision);
            p.AddWithValue("@idAdmin", idAdministrateur);
            p.AddWithValue("@id", idSignalement);
        });
    }

    /// <summary>Archive la compétence ciblée par un signalement fondé (BR-016).</summary>
    public int ArchiverCompetenceCible(int idCompetence)
    {
        const string sql = "UPDATE competence SET statut = 'ARCHIVEE' WHERE idCompetence = @id";
        return DatabaseHelper.Execute(sql, p => p.AddWithValue("@id", idCompetence));
    }
}
