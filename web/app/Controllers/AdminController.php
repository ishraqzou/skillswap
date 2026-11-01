<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Compte;
use App\Models\Utilisateur;
use App\Models\Signalement;
use App\Models\Certificat;
use App\Models\Competence;
use App\Models\DemandeEchange;
use App\Models\Seance;
use App\Services\PointService;
use App\Services\CertificateService;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireAdmin();

        $compteModel = new Compte();
        $signalementModel = new Signalement();
        $certificatModel = new Certificat();
        $demandeModel = new DemandeEchange();

        $this->render('admin/dashboard', [
            'pageTitle'            => 'Tableau de bord administrateur',
            'nbUtilisateurs'       => $compteModel->count(['typeCompte' => 'UTILISATEUR']),
            'nbSignalementsAttente'=> count($signalementModel->enAttente()),
            'nbCertificats'        => $certificatModel->count(['statut' => 'ACTIF']),
            'nbDemandesEnAttente'  => $demandeModel->count(['statut' => 'EN_ATTENTE']),
            'signalements'         => array_slice($signalementModel->enAttente(), 0, 5),
        ], 'admin');
    }

    public function users(): void
    {
        Auth::requireAdmin();
        $compteModel = new Compte();

        $this->render('admin/users', [
            'pageTitle' => 'Gestion des utilisateurs',
            'comptes'   => $compteModel->all('dateCreation DESC'),
            'flash'     => $this->getFlash(),
        ], 'admin');
    }

    public function suspendUser($idCompte): void
    {
        Auth::requireAdmin();
        $this->requirePost();

        $compteModel = new Compte();
        $compte = $compteModel->find($idCompte);
        $nouveauStatut = $compte['statut'] === 'ACTIF' ? 'SUSPENDU' : 'ACTIF';
        $compteModel->updateStatut($idCompte, $nouveauStatut);

        $this->setFlash('success', 'Statut du compte mis à jour.');
        $this->redirect('/admin/utilisateurs');
    }

    public function reports(): void
    {
        Auth::requireAdmin();
        $signalementModel = new Signalement();

        $this->render('admin/reports', [
            'pageTitle'     => 'Signalements en attente',
            'signalements'  => $signalementModel->enAttente(),
            'flash'         => $this->getFlash(),
        ], 'admin');
    }

    public function resolveReport($idSignalement): void
    {
        Auth::requireAdmin();
        $this->requirePost();

        $decision = $this->input('decision');
        $motif = $this->input('motif', '');
        $signalementModel = new Signalement();

        if ($decision === 'fonde') {
            $signalement = $signalementModel->find($idSignalement);
            if ($signalement['cibleType'] === 'COMPETENCE') {
                (new Competence())->update($signalement['cibleId'], ['statut' => 'ARCHIVEE']);
            }
            $signalementModel->traiter($idSignalement, Auth::id(), $motif, 'CLOTURE');
        } else {
            $signalementModel->traiter($idSignalement, Auth::id(), $motif ?: 'Signalement non retenu.', 'CLOTURE');
        }

        $this->setFlash('success', 'Signalement traité.');
        $this->redirect('/admin/signalements');
    }

    public function adjustPoints(): void
    {
        Auth::requireAdmin();
        $this->requirePost();

        $idUtilisateur = (int) $this->input('idUtilisateur');
        $montant = (int) $this->input('montant');
        $motif = trim($this->input('motif', ''));

        if ($motif === '') {
            $this->setFlash('error', 'Un motif est obligatoire pour tout ajustement manuel (BR-042).');
            $this->redirect('/admin/utilisateurs');
        }

        (new PointService())->ajusterManuel($idUtilisateur, $montant, $motif, Auth::id());

        $this->setFlash('success', 'Solde de points ajusté et journalisé.');
        $this->redirect('/admin/utilisateurs');
    }

    public function certificates(): void
    {
        Auth::requireAdmin();
        $certificatModel = new Certificat();
        $this->render('admin/certificates', [
            'pageTitle'   => 'Gestion des certificats',
            'certificats' => $certificatModel->all('dateDelivrance DESC'),
            'flash'       => $this->getFlash(),
        ], 'admin');
    }

    public function revokeCertificate($idCertificat): void
    {
        Auth::requireAdmin();
        $this->requirePost();

        (new Certificat())->revoquer($idCertificat, Auth::id());
        $this->setFlash('success', 'Certificat révoqué.');
        $this->redirect('/admin/certificats');
    }

    public function regenerateCertificate($idCertificat): void
    {
        Auth::requireAdmin();
        $this->requirePost();

        (new CertificateService())->regenerer($idCertificat);
        $this->setFlash('success', 'Certificat régénéré ; l\'ancien a été marqué comme remplacé.');
        $this->redirect('/admin/certificats');
    }

    public function stats(): void
    {
        Auth::requireAdmin();

        $compteModel = new Compte();
        $competenceModel = new Competence();
        $demandeModel = new DemandeEchange();
        $seanceModel = new Seance();
        $certificatModel = new Certificat();

        $this->render('admin/stats', [
            'pageTitle'          => 'Statistiques',
            'nbUtilisateurs'     => $compteModel->count(['typeCompte' => 'UTILISATEUR']),
            'nbCompetences'      => $competenceModel->count(['statut' => 'ACTIVE']),
            'nbDemandesAcceptees'=> $demandeModel->count(['statut' => 'ACCEPTEE']),
            'nbSeancesTerminees' => $seanceModel->count(['statut' => 'TERMINEE']),
            'nbCertificats'      => $certificatModel->count(['statut' => 'ACTIF']),
        ], 'admin');
    }
}
