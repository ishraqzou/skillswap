namespace SkillSwap.Desktop.Models;

/// <summary>
/// Certificat — attestation de compétence acquise (BR-047 à BR-054).
/// </summary>
public class Certificat
{
    public int IdCertificat { get; set; }
    public string IdentifiantUnique { get; set; } = string.Empty;
    public string CompetenceAcquise { get; set; } = string.Empty;
    public DateTime DateDelivrance { get; set; }
    public string CodeVerification { get; set; } = string.Empty;
    public string SignatureNumerique { get; set; } = string.Empty;
    public string Statut { get; set; } = "ACTIF";
    public int IdSeance { get; set; }
    public int IdApprenant { get; set; }
    public int IdFormateur { get; set; }
    public int? IdAdministrateur { get; set; }
    public int? IdCertificatPrecedent { get; set; }

    public string ApprenantNomComplet { get; set; } = string.Empty;
    public string FormateurNomComplet { get; set; } = string.Empty;

    public bool EstActif => Statut == "ACTIF";
}
