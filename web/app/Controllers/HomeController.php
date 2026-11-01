<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Competence;
use App\Models\Categorie;

class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::check()) {
            $this->redirect('/tableau-de-bord');
        }

        $competenceModel = new Competence();
        $categorieModel = new Categorie();

        $this->render('home/index', [
            'pageTitle'    => 'Accueil',
            'competences'  => $competenceModel->rechercher([]),
            'categories'   => $categorieModel->all('nom ASC'),
            'flash'        => $this->getFlash(),
        ]);
    }

    public function catalogue(): void
    {
        $competenceModel = new Competence();
        $categorieModel = new Categorie();

        $filtres = [
            'categorie' => $_GET['categorie'] ?? null,
            'q'         => $_GET['q'] ?? null,
        ];

        $this->render('home/catalogue', [
            'pageTitle'   => 'Catalogue public',
            'competences' => $competenceModel->rechercher($filtres),
            'categories'  => $categorieModel->all('nom ASC'),
            'filtres'     => $filtres,
        ]);
    }
}
