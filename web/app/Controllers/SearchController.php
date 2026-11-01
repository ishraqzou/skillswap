<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Competence;
use App\Models\Categorie;

class SearchController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $competenceModel = new Competence();
        $categorieModel = new Categorie();

        $filtres = [
            'q'         => $_GET['q'] ?? null,
            'categorie' => $_GET['categorie'] ?? null,
            'type'      => $_GET['type'] ?? null,
            'niveau'    => $_GET['niveau'] ?? null,
        ];

        $resultats = $competenceModel->rechercher($filtres, Auth::id());

        $this->render('search/index', [
            'pageTitle'  => 'Rechercher des compétences',
            'resultats'  => $resultats,
            'categories' => $categorieModel->all('nom ASC'),
            'filtres'    => $filtres,
        ], 'app');
    }

    public function show($idCompetence): void
    {
        Auth::requireLogin();
        $competenceModel = new Competence();
        $competence = $competenceModel->findWithAuteur($idCompetence);

        if (!$competence) {
            http_response_code(404);
            echo 'Compétence introuvable.';
            return;
        }

        $mesCompetencesOffertes = $competenceModel->findByUtilisateur(Auth::id(), 'OFFERTE');

        $this->render('search/show', [
            'pageTitle'   => $competence['titre'],
            'competence'  => $competence,
            'mesCompetencesOffertes' => $mesCompetencesOffertes,
        ], 'app');
    }
}
