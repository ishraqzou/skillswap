<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Seance;
use App\Models\Notification;
use App\Services\PointService;

class SeanceController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $seanceModel = new Seance();

        $this->render('seances/index', [
            'pageTitle' => 'Mes séances',
            'seances'   => $seanceModel->pourUtilisateur(Auth::id()),
            'flash'     => $this->getFlash(),
        ], 'app');
    }

    public function proposerCreneau($idSeance): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $seanceModel = new Seance();
        $seance = $seanceModel->find($idSeance);
        if (!$this->estParticipant($seance)) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $dateHeure = $this->input('dateHeure');
        $duree = (int) $this->input('duree', 60);

        if ($seanceModel->chevauchementExiste($seance['idParticipantA'], $dateHeure, $duree)
            || $seanceModel->chevauchementExiste($seance['idParticipantB'], $dateHeure, $duree)) {
            $this->setFlash('error', 'Ce créneau chevauche une séance déjà confirmée (BR-024). Merci de choisir une autre date.');
            $this->redirect('/seances');
        }

        $seanceModel->update($idSeance, [
            'dateHeure'    => $dateHeure,
            'duree'        => $duree,
            'lieuModalite' => $this->input('lieuModalite'),
            'statut'       => 'CONFIRMEE',
        ]);

        $autreParticipant = (int) $seance['idParticipantA'] === (int) Auth::id()
            ? $seance['idParticipantB'] : $seance['idParticipantA'];
        (new Notification())->creer($autreParticipant, 'SEANCE', 'Une séance a été planifiée / mise à jour.');

        $this->setFlash('success', 'Séance confirmée.');
        $this->redirect('/seances');
    }

    public function terminer($idSeance): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $seanceModel = new Seance();
        $seance = $seanceModel->find($idSeance);
        if (!$this->estParticipant($seance)) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }
        if ($seance['statut'] === 'ANNULEE') {
            $this->setFlash('error', 'Cette séance a été annulée ; aucun point ne peut être attribué (BR-027).');
            $this->redirect('/seances');
        }

        $estComplete = $seanceModel->confirmerParticipant($idSeance, Auth::id());

        if ($estComplete) {
            (new PointService())->attribuerPointsSeance($idSeance, $seance['idParticipantA'], $seance['idParticipantB']);
            $this->setFlash('success', 'Séance marquée comme terminée par les deux participants : points attribués !');
        } else {
            $this->setFlash('success', 'Votre confirmation a été enregistrée. En attente de la confirmation de l\'autre participant.');
        }

        $this->redirect('/seances');
    }

    public function annuler($idSeance): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $seanceModel = new Seance();
        $seance = $seanceModel->find($idSeance);
        if (!$this->estParticipant($seance)) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $seanceModel->update($idSeance, ['statut' => 'ANNULEE']);
        $this->setFlash('success', 'Séance annulée.');
        $this->redirect('/seances');
    }

    private function estParticipant(?array $seance): bool
    {
        if (!$seance) {
            return false;
        }
        return (int) $seance['idParticipantA'] === (int) Auth::id()
            || (int) $seance['idParticipantB'] === (int) Auth::id();
    }
}
