<?php

namespace App\Models;

use App\Core\Model;

class Signalement extends Model
{
    protected string $table = 'signalement';
    protected string $primaryKey = 'idSignalement';

    public function enAttente(): array
    {
        return $this->query(
            "SELECT s.*, u.nom AS auteurNom, u.prenom AS auteurPrenom
             FROM signalement s
             INNER JOIN utilisateur u ON u.idCompte = s.idAuteur
             WHERE s.statut = 'EN_ATTENTE' ORDER BY s.dateCreation ASC"
        );
    }

    public function traiter($idSignalement, $idAdministrateur, string $decision, string $statut): bool
    {
        return $this->update($idSignalement, [
            'statut'           => $statut,
            'decision'         => $decision,
            'dateTraitement'   => date('Y-m-d H:i:s'),
            'idAdministrateur' => $idAdministrateur,
        ]);
    }
}
