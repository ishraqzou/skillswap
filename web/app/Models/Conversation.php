<?php

namespace App\Models;

use App\Core\Model;

class Conversation extends Model
{
    protected string $table = 'conversation';
    protected string $primaryKey = 'idConversation';

    public function pourUtilisateur($idCompte): array
    {
        return $this->query(
            "SELECT c.*,
                    CASE WHEN c.idParticipantA = :id1 THEN ub.nom ELSE ua.nom END AS interlocuteurNom,
                    CASE WHEN c.idParticipantA = :id2 THEN ub.prenom ELSE ua.prenom END AS interlocuteurPrenom,
                    CASE WHEN c.idParticipantA = :id6 THEN ub.photoProfil ELSE ua.photoProfil END AS interlocuteurPhoto,
                    CASE WHEN c.idParticipantA = :id3 THEN c.idParticipantB ELSE c.idParticipantA END AS interlocuteurId
             FROM conversation c
             INNER JOIN utilisateur ua ON ua.idCompte = c.idParticipantA
             INNER JOIN utilisateur ub ON ub.idCompte = c.idParticipantB
             WHERE c.idParticipantA = :id4 OR c.idParticipantB = :id5
             ORDER BY c.dateCreation DESC",
            ['id1' => $idCompte, 'id2' => $idCompte, 'id3' => $idCompte,
             'id4' => $idCompte, 'id5' => $idCompte, 'id6' => $idCompte]
        );
    }

    public function estParticipant($idConversation, $idCompte): bool
    {
        $conv = $this->find($idConversation);
        if (!$conv) {
            return false;
        }
        return (int) $conv['idParticipantA'] === (int) $idCompte
            || (int) $conv['idParticipantB'] === (int) $idCompte;
    }

    public function trouverOuCreer($idA, $idB, $idDemande = null): int
    {
        $existing = $this->query(
            "SELECT idConversation FROM conversation
             WHERE (idParticipantA = :a1 AND idParticipantB = :b1)
                OR (idParticipantA = :b2 AND idParticipantB = :a2)",
            ['a1' => $idA, 'b1' => $idB, 'a2' => $idA, 'b2' => $idB]
        );
        if (!empty($existing)) {
            return (int) $existing[0]['idConversation'];
        }
        return $this->insert([
            'idParticipantA' => $idA,
            'idParticipantB' => $idB,
            'idDemande'      => $idDemande,
        ]);
    }
}
