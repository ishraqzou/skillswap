<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Avis;
use App\Models\Seance;
use App\Models\Utilisateur;
use App\Models\Notification;
use App\Services\CertificateService;

class AvisController extends Controller
{
    public function create($idSeance): void
    {
        Auth::requireLogin();
        $seanceModel = new Seance();
        $seance = $seanceModel->find($idSeance);

        if (!$seance || $seance['statut'] !== 'TERMINEE') {
            $this->setFlash('error', 'Cette séance n\'est pas encore terminée.');
            $this->redirect('/seances');
        }

        $this->render('avis/create', [
            'pageTitle' => 'Laisser un avis',
            'seance'    => $seance,
        ], 'app');
    }

    public function store($idSeance): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $seanceModel = new Seance();
        $seance = $seanceModel->find($idSeance);
        $idAuteur = Auth::id();

        if (!$seance || $seance['statut'] !== 'TERMINEE') {
            $this->setFlash('error', 'Cette séance n\'est pas encore terminée.');
            $this->redirect('/seances');
        }

        $estParticipant = (int) $seance['idParticipantA'] === (int) $idAuteur
            || (int) $seance['idParticipantB'] === (int) $idAuteur;
        if (!$estParticipant) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $avisModel = new Avis();

        if ($avisModel->avisExiste($idSeance, $idAuteur)) {
            $this->setFlash('error', 'Vous avez déjà soumis un avis pour cette séance (BR-035).');
            $this->redirect('/seances');
        }

        $idEvalue = (int) $seance['idParticipantA'] === (int) $idAuteur
            ? $seance['idParticipantB'] : $seance['idParticipantA'];

        $note = max(1, min(5, (int) $this->input('note')));

        $avisModel->insert([
            'note'        => $note,
            'commentaire' => $this->input('commentaire'),
            'statut'      => 'VISIBLE',
            'idSeance'    => $idSeance,
            'idAuteur'    => $idAuteur,
            'idEvalue'    => $idEvalue,
        ]);

        (new Utilisateur())->recalculerNoteMoyenne($idEvalue);

        $idCertificat = (new CertificateService())->verifierEtGenererSiEligible($idSeance);

        (new Notification())->creer($idEvalue, 'AVIS', 'Vous avez reçu un nouvel avis.');

        $message = $idCertificat
            ? 'Avis enregistré. Toutes les conditions étaient réunies : un certificat a été délivré !'
            : 'Avis enregistré avec succès.';
        $this->setFlash('success', $message);
        $this->redirect('/seances');
    }
}
