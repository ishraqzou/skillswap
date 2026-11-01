namespace SkillSwap.Desktop.Services;

/// <summary>
/// PasswordHasher — vérification des mots de passe hachés en bcrypt par
/// l'application web (Tâche 18, BR-002).
///
/// Les deux applications partageant la même table "compte", l'application
/// bureautique doit pouvoir valider les empreintes générées côté PHP par
/// password_hash() (préfixe $2y$). La bibliothèque BCrypt.Net-Next est
/// utilisée à cette fin plutôt qu'une implémentation maison : le code
/// cryptographique ne doit jamais être réécrit à la main.
/// </summary>
public static class PasswordHasher
{
    /// <summary>Vérifie qu'un mot de passe en clair correspond à une empreinte bcrypt.</summary>
    public static bool Verify(string motDePasse, string hashStocke)
    {
        if (string.IsNullOrEmpty(motDePasse) || string.IsNullOrEmpty(hashStocke))
        {
            return false;
        }

        try
        {
            return BCrypt.Net.BCrypt.Verify(motDePasse, hashStocke);
        }
        catch (BCrypt.Net.SaltParseException)
        {
            // Empreinte malformée : considérée comme non concordante,
            // sans faire échouer l'application (NFR-087).
            return false;
        }
    }
}
