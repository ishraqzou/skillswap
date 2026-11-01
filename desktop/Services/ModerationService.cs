using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Services;

/// <summary>
/// ModerationService — traitement des signalements et suspension de comptes
/// (UC-22, UC-25, BR-016, BR-060 à BR-062).
/// </summary>
public class ModerationService
{
    private readonly SignalementRepository _signalementRepository = new();
    private readonly CompteRepository _compteRepository = new();

    /// <summary>
    /// Clôture un signalement. Si celui-ci est jugé fondé et vise une
    /// compétence, celle-ci est archivée (BR-016). La décision et son auteur
    /// sont systématiquement journalisés (BR-060, BR-061).
    /// </summary>
    public bool TraiterSignalement(Signalement signalement, bool estFonde, string motifDecision,
                                   out string message)
    {
        if (string.IsNullOrWhiteSpace(motifDecision))
        {
            message = "Une décision motivée est obligatoire pour clôturer un signalement (BR-060).";
            return false;
        }

        if (estFonde && signalement.CibleType == "COMPETENCE")
        {
            _signalementRepository.ArchiverCompetenceCible(signalement.CibleId);
        }

        var prefixe = estFonde ? "[FONDÉ] " : "[NON FONDÉ] ";
        int lignes = _signalementRepository.Traiter(
            signalement.IdSignalement, SessionAdministrateur.IdCourant, prefixe + motifDecision);

        message = lignes > 0
            ? "Signalement clôturé et décision journalisée."
            : "Le signalement n'a pas pu être clôturé.";
        return lignes > 0;
    }

    /// <summary>Bascule le statut d'un compte entre ACTIF et SUSPENDU (UC-22, BR-062).</summary>
    public bool BasculerStatutCompte(Utilisateur utilisateur, out string message)
    {
        var nouveauStatut = utilisateur.EstActif ? "SUSPENDU" : "ACTIF";
        int lignes = _compteRepository.UpdateStatut(utilisateur.IdCompte, nouveauStatut);

        message = lignes > 0
            ? $"Le compte de {utilisateur.NomComplet} est désormais « {nouveauStatut} »."
            : "Le statut du compte n'a pas pu être modifié.";
        return lignes > 0;
    }
}
