<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Point;
use App\Models\Badge;

class PointController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $pointModel = new Point();
        $badgeModel = new Badge();

        $this->render('points/index', [
            'pageTitle'   => 'Mes points et badges',
            'historique'  => $pointModel->historique(Auth::id()),
            'solde'       => $pointModel->totalAttribue(Auth::id()),
            'badges'      => $badgeModel->obtenusPar(Auth::id()),
            'tousBadges'  => $badgeModel->tousOrdonnesParSeuil(),
        ], 'app');
    }
}
