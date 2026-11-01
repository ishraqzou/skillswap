using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Data;

/// <summary>
/// UtilisateurRepository — consultation et gestion des comptes utilisateurs
/// (UC-21, UC-22).
/// </summary>
public class UtilisateurRepository
{
    private const string BaseSelect = @"
        SELECT u.idCompte, u.nom, u.prenom, u.photoProfil, u.biographie, u.ville,
               u.languesParlees, u.soldePoints, u.noteMoyenne, u.visibiliteProfil,
               c.email, c.statut, c.dateCreation
        FROM utilisateur u
        INNER JOIN compte c ON c.idCompte = u.idCompte";

    private static Utilisateur Map(System.Data.IDataRecord r) => new()
    {
        IdCompte         = DatabaseHelper.GetInt(r, "idCompte"),
        Nom              = DatabaseHelper.GetString(r, "nom"),
        Prenom           = DatabaseHelper.GetString(r, "prenom"),
        PhotoProfil      = DatabaseHelper.GetNullableString(r, "photoProfil"),
        Biographie       = DatabaseHelper.GetNullableString(r, "biographie"),
        Ville            = DatabaseHelper.GetNullableString(r, "ville"),
        LanguesParlees   = DatabaseHelper.GetNullableString(r, "languesParlees"),
        SoldePoints      = DatabaseHelper.GetInt(r, "soldePoints"),
        NoteMoyenne      = DatabaseHelper.GetNullableDecimal(r, "noteMoyenne"),
        VisibiliteProfil = DatabaseHelper.GetString(r, "visibiliteProfil"),
        Email            = DatabaseHelper.GetString(r, "email"),
        Statut           = DatabaseHelper.GetString(r, "statut"),
        DateCreation     = DatabaseHelper.GetDateTime(r, "dateCreation"),
    };

    public List<Utilisateur> GetAll()
        => DatabaseHelper.Query(BaseSelect + " ORDER BY c.dateCreation DESC", null, Map);

    /// <summary>Recherche par nom, prénom, e-mail ou ville (UC-21).</summary>
    public List<Utilisateur> Search(string terme)
    {
        const string filtre = @" WHERE u.nom LIKE @t OR u.prenom LIKE @t
                                    OR c.email LIKE @t OR u.ville LIKE @t";
        return DatabaseHelper.Query(BaseSelect + filtre + " ORDER BY c.dateCreation DESC",
            p => p.AddWithValue("@t", "%" + terme + "%"), Map);
    }

    public Utilisateur? FindById(int idCompte)
        => DatabaseHelper.QuerySingle(BaseSelect + " WHERE u.idCompte = @id",
            p => p.AddWithValue("@id", idCompte), Map);
}
