<?php

namespace App\Services;

use App\Models\Point;
use App\Models\Utilisateur;
use App\Models\Badge;
use App\Models\Notification;

/**
 * PointService - Centralise la logique du système de points (BR-039 à BR-046).
 */
class PointService
{
    private Point $pointModel;
    private Utilisateur $utilisateurModel;
    private Badge $badgeModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->pointModel = new Point();
        $this->utilisateurModel = new Utilisateur();
        $this->badgeModel = new Badge();
        $this->notificationModel = new Notification();
    }

    public function attribuerPointsSeance($idSeance, $idParticipantA, $idParticipantB): void
    {
        foreach ([$idParticipantA, $idParticipantB] as $idUtilisateur) {
            $this->pointModel->insert([
                'montant'       => POINTS_PER_SESSION,
                'type'          => 'ATTRIBUTION',
                'idUtilisateur' => $idUtilisateur,
                'idSeance'      => $idSeance,
                'motif'         => null,
            ]);
            $this->utilisateurModel->ajusterSolde($idUtilisateur, POINTS_PER_SESSION);
            $this->verifierBadges($idUtilisateur);
            $this->notificationModel->creer(
                $idUtilisateur,
                'POINTS',
                'Vous avez reçu ' . POINTS_PER_SESSION . ' points pour une séance complétée.'
            );
        }
    }

    public function verifierBadges($idUtilisateur): void
    {
        $utilisateur = $this->utilisateurModel->find($idUtilisateur);
        if (!$utilisateur) {
            return;
        }
        $solde = (int) $utilisateur['soldePoints'];

        foreach ($this->badgeModel->tousOrdonnesParSeuil() as $badge) {
            if ($solde >= (int) $badge['seuilPoints'] && !$this->badgeModel->estObtenu($idUtilisateur, $badge['idBadge'])) {
                $this->badgeModel->attribuer($idUtilisateur, $badge['idBadge']);
                $this->notificationModel->creer(
                    $idUtilisateur,
                    'BADGE',
                    'Félicitations, vous avez débloqué le badge « ' . $badge['nom'] . ' » !'
                );
            }
        }
    }

    public function ajusterManuel($idUtilisateur, int $montant, string $motif, $idAdministrateur): void
    {
        if (trim($motif) === '') {
            throw new \InvalidArgumentException('Un motif est obligatoire pour tout ajustement manuel (BR-042).');
        }

        $this->pointModel->insert([
            'montant'          => $montant,
            'type'             => 'AJUSTEMENT',
            'motif'            => $motif,
            'idUtilisateur'    => $idUtilisateur,
            'idAdministrateur' => $idAdministrateur,
            'idSeance'         => null,
        ]);
        $this->utilisateurModel->ajusterSolde($idUtilisateur, $montant);
    }
}
