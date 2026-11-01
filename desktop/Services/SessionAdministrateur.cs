using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Services;

/// <summary>
/// SessionAdministrateur — conserve l'administrateur authentifié pour la
/// durée d'exécution de l'application (UC-20).
/// Toute action sensible référence cet identifiant à des fins de
/// traçabilité (BR-046, BR-052, BR-060).
/// </summary>
public static class SessionAdministrateur
{
    public static Administrateur? Courant { get; private set; }

    public static bool EstConnecte => Courant != null;

    public static int IdCourant =>
        Courant?.IdCompte
        ?? throw new InvalidOperationException("Aucun administrateur n'est authentifié.");

    public static void Ouvrir(Administrateur administrateur) => Courant = administrateur;

    public static void Fermer() => Courant = null;
}
