namespace SkillSwap.Desktop.Models;

/// <summary>
/// Administrateur — sous-type de Compte (Tâche 8 / Tâche 12).
/// </summary>
public class Administrateur
{
    public int IdCompte { get; set; }
    public string NomComplet { get; set; } = string.Empty;
    public string NiveauAcces { get; set; } = "STANDARD";
    public string Email { get; set; } = string.Empty;
}
