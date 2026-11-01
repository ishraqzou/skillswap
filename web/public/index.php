<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

session_start();

use App\Core\Router;
use App\Core\Auth;

Auth::checkTimeout();

$router = new Router();

$router->get('/', 'HomeController', 'index');
$router->get('/catalogue', 'HomeController', 'catalogue');
$router->get('/certificats/verifier', 'CertificateController', 'verify');

$router->get('/inscription', 'AuthController', 'showRegister');
$router->post('/inscription', 'AuthController', 'register');
$router->get('/connexion', 'AuthController', 'showLogin');
$router->post('/connexion', 'AuthController', 'login');
$router->get('/deconnexion', 'AuthController', 'logout');

$router->get('/tableau-de-bord', 'ProfileController', 'dashboard');
$router->get('/profil/modifier', 'ProfileController', 'edit');
$router->post('/profil/modifier', 'ProfileController', 'update');
$router->get('/profil/mot-de-passe', 'ProfileController', 'passwordForm');
$router->post('/profil/mot-de-passe', 'ProfileController', 'passwordUpdate');
$router->post('/profil/photo', 'ProfileController', 'updatePhoto');
$router->get('/profil/{id}', 'ProfileController', 'show');

$router->get('/mes-competences', 'CompetenceController', 'index');
$router->get('/mes-competences/ajouter', 'CompetenceController', 'create');
$router->post('/mes-competences/ajouter', 'CompetenceController', 'store');
$router->post('/mes-competences/{id}/pause', 'CompetenceController', 'pause');
$router->post('/mes-competences/{id}/supprimer', 'CompetenceController', 'delete');

$router->get('/rechercher', 'SearchController', 'index');
$router->get('/competences/{id}', 'SearchController', 'show');

$router->get('/demandes-echange', 'ExchangeController', 'index');
$router->post('/demandes-echange/envoyer', 'ExchangeController', 'store');
$router->post('/demandes-echange/{id}/repondre', 'ExchangeController', 'respond');
$router->post('/demandes-echange/{id}/annuler', 'ExchangeController', 'cancel');

$router->get('/seances', 'SeanceController', 'index');
$router->post('/seances/{id}/creneau', 'SeanceController', 'proposerCreneau');
$router->post('/seances/{id}/terminer', 'SeanceController', 'terminer');
$router->post('/seances/{id}/annuler', 'SeanceController', 'annuler');

$router->get('/seances/{id}/avis', 'AvisController', 'create');
$router->post('/seances/{id}/avis', 'AvisController', 'store');

$router->get('/messagerie', 'MessageController', 'index');
$router->get('/messagerie/{id}', 'MessageController', 'show');
$router->post('/messagerie/{id}/envoyer', 'MessageController', 'send');

$router->get('/points', 'PointController', 'index');
$router->get('/certificats', 'CertificateController', 'index');
$router->get('/certificats/{id}', 'CertificateController', 'show');
$router->get('/notifications', 'NotificationController', 'index');
$router->post('/notifications/{id}/lue', 'NotificationController', 'markRead');

$router->get('/admin', 'AdminController', 'dashboard');
$router->get('/admin/utilisateurs', 'AdminController', 'users');
$router->post('/admin/utilisateurs/{id}/suspendre', 'AdminController', 'suspendUser');
$router->post('/admin/points/ajuster', 'AdminController', 'adjustPoints');
$router->get('/admin/signalements', 'AdminController', 'reports');
$router->post('/admin/signalements/{id}/traiter', 'AdminController', 'resolveReport');
$router->get('/admin/certificats', 'AdminController', 'certificates');
$router->post('/admin/certificats/{id}/revoquer', 'AdminController', 'revokeCertificate');
$router->post('/admin/certificats/{id}/regenerer', 'AdminController', 'regenerateCertificate');
$router->get('/admin/statistiques', 'AdminController', 'stats');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
