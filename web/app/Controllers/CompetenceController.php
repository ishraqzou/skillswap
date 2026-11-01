<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\Competence;
use App\Models\Categorie;

class CompetenceController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $competenceModel = new Competence();

        $this->render('competences/index', [
            'pageTitle' => 'Mes compétences',
            'offertes'  => $competenceModel->findByUtilisateur(Auth::id(), 'OFFERTE'),
            'demandees' => $competenceModel->findByUtilisateur(Auth::id(), 'DEMANDEE'),
            'flash'     => $this->getFlash(),
        ], 'app');
    }

    public function create(): void
    {
        Auth::requireLogin();
        $categorieModel = new Categorie();
        $this->render('competences/create', [
            'pageTitle'  => 'Ajouter une compétence',
            'categories' => $categorieModel->all('nom ASC'),
            'errors'     => [],
            'old'        => [],
        ], 'app');
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $data = [
            'titre'       => $this->input('titre'),
            'description' => $this->input('description'),
            'type'        => $this->input('type'),
            'niveau'      => $this->input('niveau'),
            'motsCles'    => $this->input('motsCles'),
            'idCategorie' => $this->input('idCategorie'),
        ];

        $validator = new Validator($data);
        $validator->required('titre', 'Titre')
            ->minLength('description', 20, 'Description')
            ->required('idCategorie', 'Catégorie')
            ->in('type', ['OFFERTE', 'DEMANDEE'], 'Type');

        $competenceModel = new Competence();

        if (!$validator->fails() && $competenceModel->doublonActifExiste(Auth::id(), $data['titre'], $data['type'])) {
            $errors = ['titre' => 'Vous avez déjà une compétence active portant ce titre (BR-015).'];
        } else {
            $errors = $validator->errors();
        }

        if (!empty($errors)) {
            $categorieModel = new Categorie();
            $this->render('competences/create', [
                'pageTitle'  => 'Ajouter une compétence',
                'categories' => $categorieModel->all('nom ASC'),
                'errors'     => $errors,
                'old'        => $data,
            ], 'app');
            return;
        }

        $data['idCompte'] = Auth::id();
        $data['statut'] = 'ACTIVE';
        $competenceModel->insert($data);

        $this->setFlash('success', 'Compétence publiée avec succès.');
        $this->redirect('/mes-competences');
    }

    public function pause($idCompetence): void
    {
        Auth::requireLogin();
        $competenceModel = new Competence();
        $competence = $competenceModel->find($idCompetence);

        if (!$competence || (int) $competence['idCompte'] !== (int) Auth::id()) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $nouveauStatut = $competence['statut'] === 'ACTIVE' ? 'EN_PAUSE' : 'ACTIVE';
        $competenceModel->update($idCompetence, ['statut' => $nouveauStatut]);
        $this->setFlash('success', 'Statut de la compétence mis à jour.');
        $this->redirect('/mes-competences');
    }

    public function delete($idCompetence): void
    {
        Auth::requireLogin();
        $competenceModel = new Competence();
        $competence = $competenceModel->find($idCompetence);

        if (!$competence || (int) $competence['idCompte'] !== (int) Auth::id()) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $competenceModel->update($idCompetence, ['statut' => 'ARCHIVEE']);
        $this->setFlash('success', 'Compétence archivée.');
        $this->redirect('/mes-competences');
    }
}
