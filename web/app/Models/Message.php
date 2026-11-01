<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected string $table = 'message';
    protected string $primaryKey = 'idMessage';

    public function pourConversation($idConversation): array
    {
        return $this->query(
            "SELECT m.*, u.nom AS expediteurNom, u.prenom AS expediteurPrenom
             FROM message m
             INNER JOIN utilisateur u ON u.idCompte = m.idExpediteur
             WHERE m.idConversation = :id ORDER BY m.dateEnvoi ASC",
            ['id' => $idConversation]
        );
    }
}
