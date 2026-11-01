<?php

namespace App\Models;

use App\Core\Model;

class Badge extends Model
{
    protected string $table = 'badge';
    protected string $primaryKey = 'idBadge';

    public function tousOrdonnesParSeuil(): array
    {
        return $this->all('seuilPoints ASC');
    }

    public function obtenusPar($idCompte): array
    {
        return $this->query(
            "SELECT b.*, ub.dateObtention FROM badge b
             INNER JOIN utilisateur_badge ub ON ub.idBadge = b.idBadge
             WHERE ub.idCompte = :id ORDER BY ub.dateObtention DESC",
            ['id' => $idCompte]
        );
    }

    public function estObtenu($idCompte, $idBadge): bool
    {
        $rows = $this->query(
            "SELECT 1 FROM utilisateur_badge WHERE idCompte = :c AND idBadge = :b",
            ['c' => $idCompte, 'b' => $idBadge]
        );
        return count($rows) > 0;
    }

    public function attribuer($idCompte, $idBadge): void
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO utilisateur_badge (idCompte, idBadge, dateObtention) VALUES (:c, :b, NOW())"
        );
        $stmt->execute(['c' => $idCompte, 'b' => $idBadge]);
    }
}
