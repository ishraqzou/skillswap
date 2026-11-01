<?php

namespace App\Models;

use App\Core\Model;

class Utilisateur extends Model
{
    protected string $table = 'utilisateur';
    protected string $primaryKey = 'idCompte';

    public function updateProfile($idCompte, array $data): bool
    {
        return $this->update($idCompte, $data);
    }

    public function recalculerNoteMoyenne($idCompte): void
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(note) AS moyenne FROM avis WHERE idEvalue = :id AND statut = 'VISIBLE'"
        );
        $stmt->execute(['id' => $idCompte]);
        $moyenne = $stmt->fetchColumn();
        $this->update($idCompte, ['noteMoyenne' => $moyenne !== null ? round((float) $moyenne, 2) : null]);
    }

    public function ajusterSolde($idCompte, int $montant): void
    {
        $stmt = $this->db->prepare(
            "UPDATE utilisateur SET soldePoints = GREATEST(0, soldePoints + :montant) WHERE idCompte = :id"
        );
        $stmt->execute(['montant' => $montant, 'id' => $idCompte]);
    }

    public function rechercher(array $filtres): array
    {
        $sql = "SELECT u.*, c.email FROM utilisateur u
                INNER JOIN compte c ON c.idCompte = u.idCompte
                WHERE c.statut = 'ACTIF'";
        $params = [];
        if (!empty($filtres['ville'])) {
            $sql .= " AND u.ville LIKE :ville";
            $params['ville'] = '%' . $filtres['ville'] . '%';
        }
        $sql .= " ORDER BY u.noteMoyenne DESC LIMIT 50";
        return $this->query($sql, $params);
    }
}
