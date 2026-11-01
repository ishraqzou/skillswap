<?php

namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected string $table = 'notification';
    protected string $primaryKey = 'idNotification';

    public function pourUtilisateur($idDestinataire, int $limite = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notification WHERE idDestinataire = :id ORDER BY dateCreation DESC LIMIT " . (int) $limite
        );
        $stmt->execute(['id' => $idDestinataire]);
        return $stmt->fetchAll();
    }

    public function nombreNonLues($idDestinataire): int
    {
        return $this->count(['idDestinataire' => $idDestinataire, 'statutLecture' => 0]);
    }

    public function marquerLue($idNotification): bool
    {
        return $this->update($idNotification, ['statutLecture' => 1]);
    }

    public function creer($idDestinataire, string $type, string $contenu, string $canal = 'INTERNE'): int
    {
        return $this->insert([
            'type'           => $type,
            'contenu'        => $contenu,
            'canal'          => $canal,
            'statutLecture'  => 0,
            'idDestinataire' => $idDestinataire,
        ]);
    }
}
