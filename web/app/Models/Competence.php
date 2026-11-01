<?php

namespace App\Models;

use App\Core\Model;

class Competence extends Model
{
    protected string $table = 'competence';
    protected string $primaryKey = 'idCompetence';

    public function findByUtilisateur($idCompte, ?string $type = null): array
    {
        $conditions = ['idCompte' => $idCompte];
        if ($type !== null) {
            $conditions['type'] = $type;
        }
        return $this->where($conditions, 'dateCreation DESC');
    }

    public function rechercher(array $filtres, $excludeUtilisateur = null): array
    {
        $sql = "SELECT co.*, ca.nom AS categorieNom, u.nom AS auteurNom, u.prenom AS auteurPrenom,
                       u.noteMoyenne AS auteurNote, u.ville AS auteurVille
                FROM competence co
                INNER JOIN categorie ca ON ca.idCategorie = co.idCategorie
                INNER JOIN utilisateur u ON u.idCompte = co.idCompte
                INNER JOIN compte c ON c.idCompte = u.idCompte
                WHERE co.statut = 'ACTIVE' AND c.statut = 'ACTIF'";
        $params = [];

               if (!empty($filtres['q'])) {
            $sql .= " AND (co.titre LIKE :q1 OR co.description LIKE :q2 OR co.motsCles LIKE :q3)";
            $motif = '%' . $filtres['q'] . '%';
            $params['q1'] = $motif;
            $params['q2'] = $motif;
            $params['q3'] = $motif;
        }
        if (!empty($filtres['categorie'])) {
            $sql .= " AND co.idCategorie = :categorie";
            $params['categorie'] = $filtres['categorie'];
        }
        if (!empty($filtres['type'])) {
            $sql .= " AND co.type = :type";
            $params['type'] = $filtres['type'];
        }
        if (!empty($filtres['niveau'])) {
            $sql .= " AND co.niveau = :niveau";
            $params['niveau'] = $filtres['niveau'];
        }
        if ($excludeUtilisateur !== null) {
            $sql .= " AND co.idCompte != :moi";
            $params['moi'] = $excludeUtilisateur;
        }
        $sql .= " ORDER BY co.dateCreation DESC LIMIT 60";

        return $this->query($sql, $params);
    }

    public function findWithAuteur($idCompetence): ?array
    {
        $rows = $this->query(
            "SELECT co.*, ca.nom AS categorieNom, u.nom AS auteurNom, u.prenom AS auteurPrenom,
                    u.noteMoyenne AS auteurNote, u.biographie AS auteurBio, u.ville AS auteurVille,
u.photoProfil AS auteurPhoto
             FROM competence co
             INNER JOIN categorie ca ON ca.idCategorie = co.idCategorie
             INNER JOIN utilisateur u ON u.idCompte = co.idCompte
             WHERE co.idCompetence = :id",
            ['id' => $idCompetence]
        );
        return $rows[0] ?? null;
    }

    public function doublonActifExiste($idCompte, string $titre, string $type): bool
    {
        $rows = $this->query(
            "SELECT idCompetence FROM competence
             WHERE idCompte = :idCompte AND titre = :titre AND type = :type AND statut = 'ACTIVE'",
            ['idCompte' => $idCompte, 'titre' => $titre, 'type' => $type]
        );
        return count($rows) > 0;
    }
}
