using System.Security.Cryptography;
using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Services;

/// <summary>
/// CertificatService — révocation et régénération des certificats
/// (UC-27, BR-052 à BR-054).
/// La génération initiale d'un certificat reste du ressort de l'application
/// web (BR-047) ; l'application bureautique n'intervient que dans les cas
/// exceptionnels explicitement prévus.
/// </summary>
public class CertificatService
{
    private readonly CertificatRepository _certificatRepository = new();

    public bool Revoquer(int idCertificat, out string message)
    {
        var certificat = _certificatRepository.FindById(idCertificat);
        if (certificat == null)
        {
            message = "Certificat introuvable.";
            return false;
        }
        if (!certificat.EstActif)
        {
            message = "Seul un certificat actif peut être révoqué.";
            return false;
        }

        int lignes = _certificatRepository.Revoquer(idCertificat, SessionAdministrateur.IdCourant);
        message = lignes > 0
            ? "Certificat révoqué. La décision a été journalisée."
            : "La révocation n'a pas pu être appliquée.";
        return lignes > 0;
    }

    public bool Regenerer(int idCertificat, out string message)
    {
        var ancien = _certificatRepository.FindById(idCertificat);
        if (ancien == null)
        {
            message = "Certificat introuvable.";
            return false;
        }

        var identifiant = GenererIdentifiantUnique();
        var code = GenererCodeVerification();
        var signature = GenererSignature(identifiant, code);

        int nouvelId = _certificatRepository.Regenerer(
            ancien, SessionAdministrateur.IdCourant, identifiant, code, signature);

        message = nouvelId > 0
            ? $"Nouveau certificat généré ({identifiant}). L'ancien a été marqué comme remplacé."
            : "La régénération n'a pas pu être effectuée.";
        return nouvelId > 0;
    }

    // --- Génération des identifiants, alignée sur la logique de l'application web ---
    private static string GenererIdentifiantUnique()
        => $"SKW-{DateTime.Now:yyyy}-{Convert.ToHexString(RandomNumberGenerator.GetBytes(4))}";

    private static string GenererCodeVerification()
        => Convert.ToHexString(RandomNumberGenerator.GetBytes(6));

    private static string GenererSignature(string identifiant, string code)
    {
        var donnees = System.Text.Encoding.UTF8.GetBytes(
            identifiant + code + DateTime.UtcNow.Ticks);
        return Convert.ToHexString(SHA256.HashData(donnees)).ToLowerInvariant();
    }
}
