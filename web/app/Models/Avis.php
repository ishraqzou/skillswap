<?php

namespace App\Models;

use App\Core\Model;

class Avis extends Model
{
    protected string $table = 'avis';
    protected string $primaryKey = 'idAvis';

    public function avisExiste($idSeance, $idAuteur): bool
    {
        return $this->first(['idSeance' => $idSeance, 'idAuteur' => $idAuteur]) !== null;
    }

    public function nombreAvisPourSeance($idSeance): int
    {
        return $this->count(['idSeance' => $idSeance, 'statut' => 'VISIBLE']);
    }

    public function pourUtilisateur($idEvalue): array
    {
        return $this->query(
            "SELECT av.*, u.nom AS auteurNom, u.prenom AS auteurPrenom
             FROM avis av
             INNER JOIN utilisateur u ON u.idCompte = av.idAuteur
             WHERE av.idEvalue = :id AND av.statut = 'VISIBLE'
             ORDER BY av.dateCreation DESC",
            ['id' => $idEvalue]
        );
    }
}
