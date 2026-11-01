using SkillSwap.Desktop.Data;
using SkillSwap.Desktop.Models;

namespace SkillSwap.Desktop.Services;

/// <summary>
/// PointService — ajustement manuel du solde de points (UC-26).
///
/// Rappel des règles appliquées :
///  - BR-042 : un motif est obligatoire pour tout ajustement manuel ;
///  - BR-044 : l'ajustement est réservé aux cas exceptionnels ;
///  - BR-045 : le solde ne peut jamais devenir négatif ;
///  - BR-046 : l'opération est journalisée (administrateur, date, motif).
///
/// L'attribution automatique de 10 points par séance complétée (BR-039)
/// relève exclusivement de l'application web et n'est jamais déclenchée ici.
/// </summary>
public class PointService
{
    private readonly PointRepository _pointRepository = new();

    public bool AjusterManuel(Utilisateur utilisateur, int montant, string motif, out string message)
    {
        if (montant == 0)
        {
            message = "Le montant de l'ajustement ne peut pas être nul.";
            return false;
        }

        if (string.IsNullOrWhiteSpace(motif))
        {
            message = "Un motif est obligatoire pour tout ajustement manuel de points (BR-042).";
            return false;
        }

        try
        {
            _pointRepository.AjusterManuel(
                utilisateur.IdCompte, montant, motif.Trim(), SessionAdministrateur.IdCourant);

            message = $"Ajustement de {(montant >= 0 ? "+" : "")}{montant} point(s) appliqué à "
                    + $"{utilisateur.NomComplet} et journalisé.";
            return true;
        }
        catch (Exception ex)
        {
            message = "L'ajustement n'a pas pu être appliqué : " + ex.Message;
            return false;
        }
    }

    public List<LignePoint> GetHistorique(int idUtilisateur)
        => _pointRepository.GetHistorique(idUtilisateur);

    public List<LignePoint> GetAjustementsManuels()
        => _pointRepository.GetAjustementsManuels();
}
