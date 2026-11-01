<?php

namespace App\Models;

use App\Core\Model;

class DemandeEchange extends Model
{
    protected string $table = 'demande_echange';
    protected string $primaryKey = 'idDemande';

    public function demandeEnAttenteExiste($idEmetteur, $idDestinataire): bool
    {
        $rows = $this->query(
            "SELECT idDemande FROM demande_echange
             WHERE idEmetteur = :e AND idDestinataire = :d AND statut = 'EN_ATTENTE'",
            ['e' => $idEmetteur, 'd' => $idDestinataire]
        );
        return count($rows) > 0;
    }

    public function findDetaillee($idDemande): ?array
    {
        $rows = $this->query(
            "SELECT de.*,
                    ue.nom AS emetteurNom, ue.prenom AS emetteurPrenom,
                    ud.nom AS destinataireNom, ud.prenom AS destinatairePrenom,
                    cp.titre AS competenceProposeeTitre, cs.titre AS competenceSouhaiteeTitre
             FROM demande_echange de
             INNER JOIN utilisateur ue ON ue.idCompte = de.idEmetteur
             INNER JOIN utilisateur ud ON ud.idCompte = de.idDestinataire
             INNER JOIN competence cp ON cp.idCompetence = de.idCompetenceProposee
             INNER JOIN competence cs ON cs.idCompetence = de.idCompetenceSouhaitee
             WHERE de.idDemande = :id",
            ['id' => $idDemande]
        );
        return $rows[0] ?? null;
    }

    public function envoyees($idEmetteur): array
    {
        return $this->query(
            "SELECT de.*, ud.nom AS destinataireNom, ud.prenom AS destinatairePrenom,
                    cs.titre AS competenceSouhaiteeTitre
             FROM demande_echange de
             INNER JOIN utilisateur ud ON ud.idCompte = de.idDestinataire
             INNER JOIN competence cs ON cs.idCompetence = de.idCompetenceSouhaitee
             WHERE de.idEmetteur = :id ORDER BY de.dateCreation DESC",
            ['id' => $idEmetteur]
        );
    }

    public function recues($idDestinataire): array
    {
        return $this->query(
            "SELECT de.*, ue.nom AS emetteurNom, ue.prenom AS emetteurPrenom,
                    cp.titre AS competenceProposeeTitre
             FROM demande_echange de
             INNER JOIN utilisateur ue ON ue.idCompte = de.idEmetteur
             INNER JOIN competence cp ON cp.idCompetence = de.idCompetenceProposee
             WHERE de.idDestinataire = :id ORDER BY de.dateCreation DESC",
            ['id' => $idDestinataire]
        );
    }

    public function expirerDemandesEnRetard(): int
    {
        $stmt = $this->db->prepare(
            "UPDATE demande_echange SET statut = 'EXPIREE'
             WHERE statut = 'EN_ATTENTE' AND dateCreation < DATE_SUB(NOW(), INTERVAL :jours DAY)"
        );
        $stmt->execute(['jours' => EXCHANGE_REQUEST_EXPIRY_DAYS]);
        return $stmt->rowCount();
    }
}
