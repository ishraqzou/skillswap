<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $notificationModel = new Notification();

        $this->render('notifications/index', [
            'pageTitle'     => 'Notifications',
            'notifications' => $notificationModel->pourUtilisateur(Auth::id()),
        ], 'app');
    }

    public function markRead($idNotification): void
    {
        Auth::requireLogin();
        (new Notification())->marquerLue($idNotification);
        $this->redirect('/notifications');
    }
}
