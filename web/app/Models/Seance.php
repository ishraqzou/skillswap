<?php

namespace App\Models;

use App\Core\Model;

/**
 * Seance - NOTE : confirmationParticipantA/B ajoutées via
 * database/schema_additions.sql pour appliquer précisément BR-026.
 */
class Seance extends Model
{
    protected string $table = 'seance';
    protected string $primaryKey = 'idSeance';

    public function chevauchementExiste($idUtilisateur, string $dateHeure, int $dureeMinutes): bool
    {
        $rows = $this->query(
            "SELECT idSeance FROM seance
             WHERE (idParticipantA = :uA OR idParticipantB = :uB)
               AND statut = 'CONFIRMEE'
               AND ABS(TIMESTAMPDIFF(MINUTE, dateHeure, :date)) < :duree",
            ['uA' => $idUtilisateur, 'uB' => $idUtilisateur, 'date' => $dateHeure, 'duree' => $dureeMinutes]
        );
        return count($rows) > 0;
    }

    public function findDetaillee($idSeance): ?array
    {
        $rows = $this->query(
            "SELECT s.*, ua.nom AS participantANom, ua.prenom AS participantAPrenom,
                    ub.nom AS participantBNom, ub.prenom AS participantBPrenom,
                    de.idCompetenceProposee, de.idCompetenceSouhaitee
             FROM seance s
             INNER JOIN utilisateur ua ON ua.idCompte = s.idParticipantA
             INNER JOIN utilisateur ub ON ub.idCompte = s.idParticipantB
             INNER JOIN demande_echange de ON de.idDemande = s.idDemande
             WHERE s.idSeance = :id",
            ['id' => $idSeance]
        );
        return $rows[0] ?? null;
    }

    public function pourUtilisateur($idCompte): array
    {
        return $this->query(
            "SELECT s.*, ua.nom AS participantANom, ua.prenom AS participantAPrenom,
                    ub.nom AS participantBNom, ub.prenom AS participantBPrenom
             FROM seance s
             INNER JOIN utilisateur ua ON ua.idCompte = s.idParticipantA
             INNER JOIN utilisateur ub ON ub.idCompte = s.idParticipantB
             WHERE s.idParticipantA = :idA OR s.idParticipantB = :idB
             ORDER BY s.dateHeure DESC",
            ['idA' => $idCompte, 'idB' => $idCompte]
        );
    }

    public function confirmerParticipant($idSeance, $idParticipant): bool
    {
        $seance = $this->find($idSeance);
        if (!$seance) {
            return false;
        }

        $colonne = ((int) $seance['idParticipantA'] === (int) $idParticipant)
            ? 'confirmationParticipantA'
            : 'confirmationParticipantB';

        $this->update($idSeance, [$colonne => date('Y-m-d H:i:s')]);

        $seance = $this->find($idSeance);
        $doubleConfirmation = !empty($seance['confirmationParticipantA']) && !empty($seance['confirmationParticipantB']);

        if ($doubleConfirmation && $seance['statut'] !== 'TERMINEE') {
            $this->update($idSeance, ['statut' => 'TERMINEE']);
            return true;
        }
        return false;
    }
}
