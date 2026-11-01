<?php

namespace App\Models;

use App\Core\Model;

class Point extends Model
{
    protected string $table = 'point';
    protected string $primaryKey = 'idPoint';

    public function historique($idUtilisateur): array
    {
        return $this->where(['idUtilisateur' => $idUtilisateur], 'dateCreation DESC');
    }

    public function totalAttribue($idUtilisateur): int
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(montant), 0) FROM point WHERE idUtilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);
        return (int) $stmt->fetchColumn();
    }
}
