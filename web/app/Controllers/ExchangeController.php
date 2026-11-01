<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\DemandeEchange;
use App\Models\Competence;
use App\Models\Seance;
use App\Models\Notification;
use App\Models\Conversation;

class ExchangeController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $demandeModel = new DemandeEchange();

        $this->render('exchanges/index', [
            'pageTitle' => 'Demandes d\'échange',
            'envoyees'  => $demandeModel->envoyees(Auth::id()),
            'recues'    => $demandeModel->recues(Auth::id()),
            'flash'     => $this->getFlash(),
        ], 'app');
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $idDestinataire = (int) $this->input('idDestinataire');
        $idCompetenceSouhaitee = (int) $this->input('idCompetenceSouhaitee');
        $idCompetenceProposee = (int) $this->input('idCompetenceProposee');

        if ($idDestinataire === (int) Auth::id()) {
            $this->setFlash('error', 'Vous ne pouvez pas vous envoyer une demande à vous-même.');
            $this->redirect('/rechercher');
        }

        $demandeModel = new DemandeEchange();

        if ($demandeModel->demandeEnAttenteExiste(Auth::id(), $idDestinataire)) {
            $this->setFlash('error', 'Une demande est déjà en attente entre vous et cet utilisateur.');
            $this->redirect('/rechercher');
        }

        $idDemande = $demandeModel->insert([
            'statut'                => 'EN_ATTENTE',
            'idEmetteur'            => Auth::id(),
            'idDestinataire'        => $idDestinataire,
            'idCompetenceProposee'  => $idCompetenceProposee,
            'idCompetenceSouhaitee' => $idCompetenceSouhaitee,
        ]);

        (new Notification())->creer(
            $idDestinataire,
            'DEMANDE_ECHANGE',
            'Vous avez reçu une nouvelle demande d\'échange.'
        );

        $this->setFlash('success', 'Votre demande d\'échange a été envoyée.');
        $this->redirect('/demandes-echange');
    }

    public function respond($idDemande): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $demandeModel = new DemandeEchange();
        $demande = $demandeModel->find($idDemande);

        if (!$demande || (int) $demande['idDestinataire'] !== (int) Auth::id()) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }
        if ($demande['statut'] !== 'EN_ATTENTE') {
            $this->setFlash('error', 'Cette demande a déjà été traitée ou a expiré.');
            $this->redirect('/demandes-echange');
        }

        $decision = $this->input('decision');

        if ($decision === 'accepter') {
            $demandeModel->update($idDemande, ['statut' => 'ACCEPTEE', 'dateReponse' => date('Y-m-d H:i:s')]);

            $seanceModel = new Seance();
            $seanceModel->insert([
                'statut'         => 'PROPOSEE',
                'idDemande'      => $idDemande,
                'idParticipantA' => $demande['idEmetteur'],
                'idParticipantB' => $demande['idDestinataire'],
            ]);

            (new Conversation())->trouverOuCreer($demande['idEmetteur'], $demande['idDestinataire'], $idDemande);

            (new Notification())->creer($demande['idEmetteur'], 'DEMANDE_ECHANGE', 'Votre demande d\'échange a été acceptée !');
            $this->setFlash('success', 'Demande acceptée. Une séance a été proposée.');
        } else {
            $demandeModel->update($idDemande, ['statut' => 'REFUSEE', 'dateReponse' => date('Y-m-d H:i:s')]);
            (new Notification())->creer($demande['idEmetteur'], 'DEMANDE_ECHANGE', 'Votre demande d\'échange a été refusée.');
            $this->setFlash('success', 'Demande refusée.');
        }

        $this->redirect('/demandes-echange');
    }

    public function cancel($idDemande): void
    {
        Auth::requireLogin();
        $demandeModel = new DemandeEchange();
        $demande = $demandeModel->find($idDemande);

        if (!$demande || (int) $demande['idEmetteur'] !== (int) Auth::id() || $demande['statut'] !== 'EN_ATTENTE') {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $demandeModel->update($idDemande, ['statut' => 'ANNULEE']);
        $this->setFlash('success', 'Demande annulée.');
        $this->redirect('/demandes-echange');
    }
}
