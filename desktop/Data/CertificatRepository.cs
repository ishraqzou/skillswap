using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// CertificatRepository — gestion des certificats (UC-27, BR-047 à BR-054).
/// </summary>
public class CertificatRepository
{
    private const string BaseSelect = @"
        SELECT ce.idCertificat, ce.identifiantUnique, ce.competenceAcquise, ce.dateDelivrance,
               ce.codeVerification, ce.signatureNumerique, ce.statut, ce.idSeance,
               ce.idApprenant, ce.idFormateur, ce.idAdministrateur, ce.idCertificatPrecedent,
               CONCAT(ua.prenom, ' ', ua.nom) AS apprenantNomComplet,
               CONCAT(uf.prenom, ' ', uf.nom) AS formateurNomComplet
        FROM certificat ce
        INNER JOIN utilisateur ua ON ua.idCompte = ce.idApprenant
        INNER JOIN utilisateur uf ON uf.idCompte = ce.idFormateur";

    private static Certificat Map(System.Data.IDataRecord r) => new()
    {
        IdCertificat          = DatabaseHelper.GetInt(r, "idCertificat"),
        IdentifiantUnique     = DatabaseHelper.GetString(r, "identifiantUnique"),
        CompetenceAcquise     = DatabaseHelper.GetString(r, "competenceAcquise"),
        DateDelivrance        = DatabaseHelper.GetDateTime(r, "dateDelivrance"),
        CodeVerification      = DatabaseHelper.GetString(r, "codeVerification"),
        SignatureNumerique    = DatabaseHelper.GetString(r, "signatureNumerique"),
        Statut                = DatabaseHelper.GetString(r, "statut"),
        IdSeance              = DatabaseHelper.GetInt(r, "idSeance"),
        IdApprenant           = DatabaseHelper.GetInt(r, "idApprenant"),
        IdFormateur           = DatabaseHelper.GetInt(r, "idFormateur"),
        IdAdministrateur      = DatabaseHelper.GetNullableInt(r, "idAdministrateur"),
        IdCertificatPrecedent = DatabaseHelper.GetNullableInt(r, "idCertificatPrecedent"),
        ApprenantNomComplet   = DatabaseHelper.GetString(r, "apprenantNomComplet"),
        FormateurNomComplet   = DatabaseHelper.GetString(r, "formateurNomComplet"),
    };

    public List<Certificat> GetAll()
        => DatabaseHelper.Query(BaseSelect + " ORDER BY ce.dateDelivrance DESC", null, Map);

    public List<Certificat> Search(string terme)
        => DatabaseHelper.Query(
            BaseSelect + @" WHERE ce.identifiantUnique LIKE @t OR ce.competenceAcquise LIKE @t
                               OR ce.codeVerification LIKE @t
                            ORDER BY ce.dateDelivrance DESC",
            p => p.AddWithValue("@t", "%" + terme + "%"), Map);

    public Certificat? FindById(int idCertificat)
        => DatabaseHelper.QuerySingle(BaseSelect + " WHERE ce.idCertificat = @id",
            p => p.AddWithValue("@id", idCertificat), Map);

    /// <summary>Révoque un certificat en traçant l'administrateur décisionnaire (BR-052).</summary>
    public int Revoquer(int idCertificat, int idAdministrateur)
    {
        const string sql = @"UPDATE certificat
                             SET statut = 'REVOQUE', idAdministrateur = @idAdmin
                             WHERE idCertificat = @id AND statut = 'ACTIF'";
        return DatabaseHelper.Execute(sql, p =>
        {
            p.AddWithValue("@idAdmin", idAdministrateur);
            p.AddWithValue("@id", idCertificat);
        });
    }

    /// <summary>
    /// Régénère un certificat : l'ancien est marqué « REMPLACE » et un nouveau
    /// certificat le référence, dans une seule transaction (BR-054, NFR-018).
    /// </summary>
    public int Regenerer(Certificat ancien, int idAdministrateur,
                         string nouvelIdentifiant, string nouveauCode, string nouvelleSignature)
    {
        int nouvelId = 0;

        DatabaseHelper.ExecuteTransaction((connection, transaction) =>
        {
            using (var cmd = new MySql.Data.MySqlClient.MySqlCommand(
                "UPDATE certificat SET statut = 'REMPLACE', idAdministrateur = @idAdmin WHERE idCertificat = @id",
                connection, transaction))
            {
                cmd.Parameters.AddWithValue("@idAdmin", idAdministrateur);
                cmd.Parameters.AddWithValue("@id", ancien.IdCertificat);
                cmd.ExecuteNonQuery();
            }

            using (var cmd = new MySql.Data.MySqlClient.MySqlCommand(
                @"INSERT INTO certificat
                    (identifiantUnique, competenceAcquise, dateDelivrance, codeVerification,
                     signatureNumerique, statut, idSeance, idApprenant, idFormateur,
                     idAdministrateur, idCertificatPrecedent)
                  VALUES
                    (@ident, @competence, NOW(), @code, @signature, 'ACTIF',
                     @idSeance, @idApprenant, @idFormateur, @idAdmin, @idPrecedent)",
                connection, transaction))
            {
                cmd.Parameters.AddWithValue("@ident", nouvelIdentifiant);
                cmd.Parameters.AddWithValue("@competence", ancien.CompetenceAcquise);
                cmd.Parameters.AddWithValue("@code", nouveauCode);
                cmd.Parameters.AddWithValue("@signature", nouvelleSignature);
                cmd.Parameters.AddWithValue("@idSeance", ancien.IdSeance);
                cmd.Parameters.AddWithValue("@idApprenant", ancien.IdApprenant);
                cmd.Parameters.AddWithValue("@idFormateur", ancien.IdFormateur);
                cmd.Parameters.AddWithValue("@idAdmin", idAdministrateur);
                cmd.Parameters.AddWithValue("@idPrecedent", ancien.IdCertificat);
                cmd.ExecuteNonQuery();
                nouvelId = (int)cmd.LastInsertedId;
            }
        });

        return nouvelId;
    }
}
