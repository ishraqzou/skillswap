<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;

class MessageController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $conversationModel = new Conversation();

        $this->render('messages/index', [
            'pageTitle'      => 'Messagerie',
            'conversations'  => $conversationModel->pourUtilisateur(Auth::id()),
            'conversationActive' => null,
            'messages'       => [],
        ], 'app');
    }

    public function show($idConversation): void
    {
        Auth::requireLogin();
        $conversationModel = new Conversation();

        if (!$conversationModel->estParticipant($idConversation, Auth::id())) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $messageModel = new Message();

        $this->render('messages/index', [
            'pageTitle'          => 'Messagerie',
            'conversations'      => $conversationModel->pourUtilisateur(Auth::id()),
            'conversationActive' => $conversationModel->find($idConversation),
            'messages'           => $messageModel->pourConversation($idConversation),
        ], 'app');
    }

    public function send($idConversation): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $conversationModel = new Conversation();
        if (!$conversationModel->estParticipant($idConversation, Auth::id())) {
            http_response_code(403);
            echo 'Action non autorisée.';
            return;
        }

        $contenu = trim($this->input('contenu', ''));
        if ($contenu !== '') {
            $messageModel = new Message();
            $messageModel->insert([
                'contenu'        => $contenu,
                'statutLecture'  => 0,
                'idConversation' => $idConversation,
                'idExpediteur'   => Auth::id(),
            ]);

            $conversation = $conversationModel->find($idConversation);
            $destinataire = (int) $conversation['idParticipantA'] === (int) Auth::id()
                ? $conversation['idParticipantB'] : $conversation['idParticipantA'];
            (new Notification())->creer($destinataire, 'MESSAGE', 'Vous avez reçu un nouveau message.');
        }

        $this->redirect('/messagerie/' . $idConversation);
    }
}
