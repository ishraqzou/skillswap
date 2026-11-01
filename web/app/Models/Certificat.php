<?php

namespace App\Models;

use App\Core\Model;

class Certificat extends Model
{
    protected string $table = 'certificat';
    protected string $primaryKey = 'idCertificat';

   public function pourUtilisateur($idUtilisateur): array
{
    return $this->query(
        "SELECT ce.*,
                uf.nom AS formateurNom, uf.prenom AS formateurPrenom,
                ua.nom AS apprenantNom, ua.prenom AS apprenantPrenom
         FROM certificat ce
         INNER JOIN utilisateur uf ON uf.idCompte = ce.idFormateur
         INNER JOIN utilisateur ua ON ua.idCompte = ce.idApprenant
         WHERE ce.idApprenant = :id1 OR ce.idFormateur = :id2
         ORDER BY ce.dateDelivrance DESC",
        ['id1' => $idUtilisateur, 'id2' => $idUtilisateur]
    );
}

    public function findByCodeVerification(string $code): ?array
    {
        $rows = $this->query(
            "SELECT ce.*, ua.nom AS apprenantNom, ua.prenom AS apprenantPrenom,
                    uf.nom AS formateurNom, uf.prenom AS formateurPrenom
             FROM certificat ce
             INNER JOIN utilisateur ua ON ua.idCompte = ce.idApprenant
             INNER JOIN utilisateur uf ON uf.idCompte = ce.idFormateur
             WHERE ce.codeVerification = :code",
            ['code' => $code]
        );
        return $rows[0] ?? null;
    }
    public function findDetaille($idCertificat): ?array
{
    $rows = $this->query(
        "SELECT ce.*, ua.nom AS apprenantNom, ua.prenom AS apprenantPrenom,
                uf.nom AS formateurNom, uf.prenom AS formateurPrenom
         FROM certificat ce
         INNER JOIN utilisateur ua ON ua.idCompte = ce.idApprenant
         INNER JOIN utilisateur uf ON uf.idCompte = ce.idFormateur
         WHERE ce.idCertificat = :id",
        ['id' => $idCertificat]
    );
    return $rows[0] ?? null;
}

    public function existePourSeance($idSeance): bool
    {
        return $this->count(['idSeance' => $idSeance]) > 0;
    }

    public function genererIdentifiants(): array
    {
        return [
            'identifiantUnique'  => 'SKW-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4))),
            'codeVerification'   => strtoupper(bin2hex(random_bytes(6))),
            'signatureNumerique' => hash('sha256', bin2hex(random_bytes(16)) . microtime()),
        ];
    }

    public function revoquer($idCertificat, $idAdministrateur): bool
    {
        return $this->update($idCertificat, [
            'statut' => 'REVOQUE',
            'idAdministrateur' => $idAdministrateur,
        ]);
    }
}
