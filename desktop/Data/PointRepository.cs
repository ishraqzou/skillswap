using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// PointRepository — historique et ajustement manuel des points
/// (UC-26, BR-039 à BR-046).
/// </summary>
public class PointRepository
{
    private const string BaseSelect = @"
        SELECT p.idPoint, p.montant, p.type, p.dateCreation, p.motif,
               p.idUtilisateur, p.idSeance, p.idAdministrateur,
               CONCAT(u.prenom, ' ', u.nom) AS utilisateurNomComplet
        FROM point p
        INNER JOIN utilisateur u ON u.idCompte = p.idUtilisateur";

    private static LignePoint Map(System.Data.IDataRecord r) => new()
    {
        IdPoint               = DatabaseHelper.GetInt(r, "idPoint"),
        Montant               = DatabaseHelper.GetInt(r, "montant"),
        Type                  = DatabaseHelper.GetString(r, "type"),
        DateCreation          = DatabaseHelper.GetDateTime(r, "dateCreation"),
        Motif                 = DatabaseHelper.GetNullableString(r, "motif"),
        IdUtilisateur         = DatabaseHelper.GetInt(r, "idUtilisateur"),
        IdSeance              = DatabaseHelper.GetNullableInt(r, "idSeance"),
        IdAdministrateur      = DatabaseHelper.GetNullableInt(r, "idAdministrateur"),
        UtilisateurNomComplet = DatabaseHelper.GetString(r, "utilisateurNomComplet"),
    };

    public List<LignePoint> GetHistorique(int idUtilisateur)
        => DatabaseHelper.Query(BaseSelect + " WHERE p.idUtilisateur = @id ORDER BY p.dateCreation DESC",
            p => p.AddWithValue("@id", idUtilisateur), Map);

    /// <summary>Liste les ajustements manuels, à des fins d'audit (BR-046).</summary>
    public List<LignePoint> GetAjustementsManuels()
        => DatabaseHelper.Query(
            BaseSelect + " WHERE p.type = 'AJUSTEMENT' ORDER BY p.dateCreation DESC", null, Map);

    /// <summary>
    /// Enregistre un ajustement manuel de points et met à jour le solde,
    /// dans une transaction unique (BR-042, BR-044, BR-045, BR-046).
    /// Le solde est plafonné à zéro via GREATEST(0, ...) conformément à BR-045.
    /// </summary>
    public void AjusterManuel(int idUtilisateur, int montant, string motif, int idAdministrateur)
    {
        if (string.IsNullOrWhiteSpace(motif))
        {
            throw new ArgumentException(
                "Un motif est obligatoire pour tout ajustement manuel de points (BR-042).",
                nameof(motif));
        }

        DatabaseHelper.ExecuteTransaction((connection, transaction) =>
        {
            using (var cmd = new MySql.Data.MySqlClient.MySqlCommand(
                @"INSERT INTO point (montant, type, dateCreation, motif, idUtilisateur, idSeance, idAdministrateur)
                  VALUES (@montant, 'AJUSTEMENT', NOW(), @motif, @idUtilisateur, NULL, @idAdmin)",
                connection, transaction))
            {
                cmd.Parameters.AddWithValue("@montant", montant);
                cmd.Parameters.AddWithValue("@motif", motif);
                cmd.Parameters.AddWithValue("@idUtilisateur", idUtilisateur);
                cmd.Parameters.AddWithValue("@idAdmin", idAdministrateur);
                cmd.ExecuteNonQuery();
            }

            using (var cmd = new MySql.Data.MySqlClient.MySqlCommand(
                @"UPDATE utilisateur
                  SET soldePoints = GREATEST(0, soldePoints + @montant)
                  WHERE idCompte = @idUtilisateur",
                connection, transaction))
            {
                cmd.Parameters.AddWithValue("@montant", montant);
                cmd.Parameters.AddWithValue("@idUtilisateur", idUtilisateur);
                cmd.ExecuteNonQuery();
            }
        });
    }
}
