<?php

namespace App\Controllers;

use App\Core\Validator;
use App\Models\Compte;
use App\Core\Controller;
use App\Core\Auth;
use App\Models\Utilisateur;
use App\Models\Competence;
use App\Models\Avis;
use App\Models\Point;
use App\Models\Certificat;
use App\Models\Badge;
use App\Models\DemandeEchange;

class ProfileController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireLogin();
        $idCompte = Auth::id();

        $competenceModel = new Competence();
        $avisModel = new Avis();
        $pointModel = new Point();
        $certificatModel = new Certificat();
        $badgeModel = new Badge();
        $demandeModel = new DemandeEchange();

        $this->render('profile/dashboard', [
            'pageTitle'    => 'Tableau de bord',
            'utilisateur'  => Auth::user(),
            'competences'  => $competenceModel->findByUtilisateur($idCompte),
            'avis'         => $avisModel->pourUtilisateur($idCompte),
            'soldePoints'  => $pointModel->totalAttribue($idCompte),
            'certificats'  => $certificatModel->pourUtilisateur($idCompte),
            'badges'       => $badgeModel->obtenusPar($idCompte),
            'demandesRecues' => count(array_filter($demandeModel->recues($idCompte), fn($d) => $d['statut'] === 'EN_ATTENTE')),
            'flash'        => $this->getFlash(),
        ], 'app');
    }

    public function show($idCompte): void
    {
        Auth::requireLogin();
        $utilisateurModel = new Utilisateur();
        $avisModel = new Avis();
        $badgeModel = new Badge();

        $utilisateur = $utilisateurModel->find($idCompte);
        if (!$utilisateur) {
            http_response_code(404);
            echo 'Profil introuvable.';
            return;
        }

        $this->render('profile/show', [
            'pageTitle'   => $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
            'profil'      => $utilisateur,
            'avis'        => $avisModel->pourUtilisateur($idCompte),
            'badges'      => $badgeModel->obtenusPar($idCompte),
        ], 'app');
    }

    public function edit(): void
    {
        Auth::requireLogin();
        $this->render('profile/edit', [
            'pageTitle'   => 'Modifier mon profil',
            'utilisateur' => Auth::user(),
            'errors'      => [],
        ], 'app');
    }

    public function update(): void
{
    Auth::requireLogin();
    $this->requirePost();

    $utilisateurModel = new Utilisateur();
    $data = [
        'nom'              => $this->input('nom'),
        'prenom'           => $this->input('prenom'),
        'ville'            => $this->input('ville'),
        'biographie'       => $this->input('biographie'),
        'languesParlees'   => $this->input('languesParlees'),
        'visibiliteProfil' => $this->input('visibiliteProfil', 'PUBLIQUE'),
    ];

    // Photo de profil (optionnelle)
    if (!empty($_FILES['photoProfil']['name']) && $_FILES['photoProfil']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photoProfil'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);

        if (!isset($allowed[$mime]) || $file['size'] > 2 * 1024 * 1024) {
            $this->setFlash('error', 'Photo invalide : JPG, PNG ou WEBP, 2 Mo maximum.');
            $this->redirect('/profil/modifier');
        }

        $dir = __DIR__ . '/../../public/uploads/avatars/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $nomFichier = 'u' . Auth::id() . '_' . time() . '.' . $allowed[$mime];

        if (move_uploaded_file($file['tmp_name'], $dir . $nomFichier)) {
            $data['photoProfil'] = 'uploads/avatars/' . $nomFichier;
        }
    }

    $utilisateurModel->updateProfile(Auth::id(), $data);

    $this->setFlash('success', 'Votre profil a été mis à jour.');
    $this->redirect('/tableau-de-bord');
}
public function updatePhoto(): void
{
    Auth::requireLogin();
    $this->requirePost();

    if (empty($_FILES['photoProfil']['name']) || $_FILES['photoProfil']['error'] !== UPLOAD_ERR_OK) {
        $this->setFlash('error', 'Aucune photo reçue.');
        $this->redirect('/tableau-de-bord');
    }

    $file = $_FILES['photoProfil'];
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);

    if (!isset($allowed[$mime]) || $file['size'] > 2 * 1024 * 1024) {
        $this->setFlash('error', 'Photo invalide : JPG, PNG ou WEBP, 2 Mo maximum.');
        $this->redirect('/tableau-de-bord');
    }

    $dir = __DIR__ . '/../../public/uploads/avatars/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $nomFichier = 'u' . Auth::id() . '_' . time() . '.' . $allowed[$mime];

    if (move_uploaded_file($file['tmp_name'], $dir . $nomFichier)) {
        (new Utilisateur())->updateProfile(Auth::id(), ['photoProfil' => 'uploads/avatars/' . $nomFichier]);
        $this->setFlash('success', 'Votre photo de profil a été mise à jour.');
    } else {
        $this->setFlash('error', 'Impossible d\'enregistrer la photo.');
    }

    $this->redirect('/tableau-de-bord');
}
public function passwordForm(): void
{
    Auth::requireLogin();
    $this->render('profile/password', [
        'pageTitle' => 'Changer mon mot de passe',
        'errors'    => [],
    ], 'app');
}

public function passwordUpdate(): void
{
    Auth::requireLogin();
    $this->requirePost();

    $data = [
        'ancien'            => $this->input('ancien'),
        'motDePasse'        => $this->input('motDePasse'),
        'motDePasseConfirm' => $this->input('motDePasseConfirm'),
    ];

    $validator = new Validator($data);
    $validator->required('ancien', 'Mot de passe actuel')
        ->passwordComplexity('motDePasse')
        ->matches('motDePasseConfirm', 'motDePasse', 'Confirmation du mot de passe');
    $errors = $validator->errors();

    $compteModel = new Compte();
    $compte = $compteModel->find(Auth::id());

    if (empty($errors['ancien']) && !password_verify($data['ancien'], $compte['motDePasseHash'])) {
        $errors['ancien'] = 'Le mot de passe actuel est incorrect.';
    }
    if (empty($errors) && password_verify($data['motDePasse'], $compte['motDePasseHash'])) {
        $errors['motDePasse'] = 'Le nouveau mot de passe doit être différent de l\'ancien.';
    }

    if (!empty($errors)) {
        $this->render('profile/password', [
            'pageTitle' => 'Changer mon mot de passe',
            'errors'    => $errors,
        ], 'app');
        return;
    }

    $compteModel->update(Auth::id(), [
        'motDePasseHash' => password_hash($data['motDePasse'], PASSWORD_BCRYPT),
    ]);
    session_regenerate_id(true);

    $this->setFlash('success', 'Votre mot de passe a été modifié.');
    $this->redirect('/tableau-de-bord');
}
}
