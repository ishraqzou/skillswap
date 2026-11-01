<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\Compte;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/tableau-de-bord');
        }
        $this->render('auth/register', ['pageTitle' => 'Inscription', 'errors' => [], 'old' => []]);
    }

    public function register(): void
    {
        $this->requirePost();

        $data = [
            'nom'                  => $this->input('nom'),
            'prenom'               => $this->input('prenom'),
            'email'                => $this->input('email'),
            'motDePasse'           => $this->input('motDePasse'),
            'motDePasseConfirm'    => $this->input('motDePasseConfirm'),
        ];

        $validator = new Validator($data);
        $validator->required('nom', 'Nom')
            ->required('prenom', 'Prénom')
            ->required('email', 'E-mail')
            ->email('email')
            ->passwordComplexity('motDePasse')
            ->matches('motDePasseConfirm', 'motDePasse', 'Confirmation du mot de passe');

        $compteModel = new Compte();

        if (!$validator->fails() && $compteModel->emailExists($data['email'])) {
            $errors = ['email' => 'Cette adresse e-mail est déjà utilisée par un autre compte (BR-001).'];
        } else {
            $errors = $validator->errors();
        }

        if (!empty($errors)) {
            $this->render('auth/register', ['pageTitle' => 'Inscription', 'errors' => $errors, 'old' => $data]);
            return;
        }

        $idCompte = $compteModel->createWithProfile(
            [
                'email'          => $data['email'],
                'motDePasseHash' => password_hash($data['motDePasse'], PASSWORD_BCRYPT),
                'statut'         => 'ACTIF',
            ],
            [
                'nom'              => $data['nom'],
                'prenom'           => $data['prenom'],
                'soldePoints'      => 0,
                'visibiliteProfil' => 'PUBLIQUE',
            ],
            'UTILISATEUR'
        );

        $compte = $compteModel->find($idCompte);
        Auth::login($compte);
        $this->setFlash('success', 'Bienvenue sur SkillSwap ! Votre compte a été créé avec succès.');
        $this->redirect('/tableau-de-bord');
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/tableau-de-bord');
        }
        $this->render('auth/login', ['pageTitle' => 'Connexion', 'error' => null]);
    }

    public function login(): void
    {
        $this->requirePost();

        $email = $this->input('email');
        $motDePasse = $this->input('motDePasse');

        $compteModel = new Compte();

        if ($compteModel->isLocked($email)) {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'error' => 'Compte temporairement verrouillé suite à plusieurs tentatives infructueuses. Réessayez dans ' . LOCKOUT_DURATION_MIN . ' minutes.',
            ]);
            return;
        }

        $compte = $compteModel->findByEmail($email);

        if (!$compte || !password_verify($motDePasse, $compte['motDePasseHash'])) {
            $compteModel->registerFailedAttempt($email);
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'error' => "Adresse e-mail ou mot de passe incorrect.",
            ]);
            return;
        }

        if ($compte['statut'] !== 'ACTIF') {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'error' => "Ce compte est actuellement suspendu. Contactez l'administration.",
            ]);
            return;
        }

        $compteModel->clearFailedAttempts($email);
        Auth::login($compte);

        $this->setFlash('success', 'Vous êtes connecté. Bon retour parmi nous !');
        $this->redirect($compte['typeCompte'] === 'ADMIN' ? '/admin' : '/tableau-de-bord');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/connexion');
    }
}
