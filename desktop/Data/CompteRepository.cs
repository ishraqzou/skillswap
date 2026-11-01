using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// CompteRepository — accès aux comptes et à l'authentification administrateur
/// (UC-20, FR-021, FR-040).
/// </summary>
public class CompteRepository
{
    public Compte? FindByEmail(string email)
    {
        const string sql = @"SELECT idCompte, email, motDePasseHash, dateCreation, statut, typeCompte
                             FROM compte WHERE email = @email";

        return DatabaseHelper.QuerySingle(sql,
            p => p.AddWithValue("@email", email),
            r => new Compte
            {
                IdCompte       = DatabaseHelper.GetInt(r, "idCompte"),
                Email          = DatabaseHelper.GetString(r, "email"),
                MotDePasseHash = DatabaseHelper.GetString(r, "motDePasseHash"),
                DateCreation   = DatabaseHelper.GetDateTime(r, "dateCreation"),
                Statut         = DatabaseHelper.GetString(r, "statut"),
                TypeCompte     = DatabaseHelper.GetString(r, "typeCompte"),
            });
    }

    public Administrateur? FindAdministrateur(int idCompte)
    {
        const string sql = @"SELECT a.idCompte, a.nomComplet, a.niveauAcces, c.email
                             FROM administrateur a
                             INNER JOIN compte c ON c.idCompte = a.idCompte
                             WHERE a.idCompte = @id";

        return DatabaseHelper.QuerySingle(sql,
            p => p.AddWithValue("@id", idCompte),
            r => new Administrateur
            {
                IdCompte    = DatabaseHelper.GetInt(r, "idCompte"),
                NomComplet  = DatabaseHelper.GetString(r, "nomComplet"),
                NiveauAcces = DatabaseHelper.GetString(r, "niveauAcces"),
                Email       = DatabaseHelper.GetString(r, "email"),
            });
    }

    /// <summary>Change le statut d'un compte (UC-22, BR-062).</summary>
    public int UpdateStatut(int idCompte, string statut)
    {
        const string sql = "UPDATE compte SET statut = @statut WHERE idCompte = @id";
        return DatabaseHelper.Execute(sql, p =>
        {
            p.AddWithValue("@statut", statut);
            p.AddWithValue("@id", idCompte);
        });
    }
}
