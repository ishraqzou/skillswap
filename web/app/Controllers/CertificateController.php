<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Certificat;

class CertificateController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $certificatModel = new Certificat();

        $this->render('certificates/index', [
            'pageTitle'   => 'Mes certificats',
            'certificats' => $certificatModel->pourUtilisateur(Auth::id()),
        ], 'app');
    }
    public function show($idCertificat): void
{
    Auth::requireLogin();
    $certificat = (new Certificat())->findDetaille($idCertificat);

    $autorise = $certificat && (
        Auth::isAdmin()
        || in_array(Auth::id(), [(int)$certificat['idApprenant'], (int)$certificat['idFormateur']], true)
    );

    if (!$autorise) {
        $this->setFlash('error', 'Certificat introuvable.');
        $this->redirect('/certificats');
    }

    $this->render('certificates/show', [
        'pageTitle'  => 'Certificat',
        'certificat' => $certificat,
    ], 'blank');
}

    public function verify(): void
    {
        $code = $_GET['code'] ?? '';
        $certificat = null;
        if ($code !== '') {
            $certificatModel = new Certificat();
            $certificat = $certificatModel->findByCodeVerification($code);
        }

        $this->render('certificates/verify', [
            'pageTitle'   => 'Vérifier un certificat',
            'code'        => $code,
            'certificat'  => $certificat,
        ]);
    }
}
