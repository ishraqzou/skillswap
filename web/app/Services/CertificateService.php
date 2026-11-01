<?php

namespace App\Services;

use App\Models\Certificat;
use App\Models\Avis;
use App\Models\Seance;
use App\Models\Signalement;
use App\Models\Notification;

/**
 * CertificateService - Génération automatique des certificats (BR-047 à BR-054).
 */
class CertificateService
{
    private Certificat $certificatModel;
    private Avis $avisModel;
    private Seance $seanceModel;
    private Signalement $signalementModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->certificatModel = new Certificat();
        $this->avisModel = new Avis();
        $this->seanceModel = new Seance();
        $this->signalementModel = new Signalement();
        $this->notificationModel = new Notification();
    }

    public function verifierEtGenererSiEligible($idSeance): ?int
    {
        $seance = $this->seanceModel->find($idSeance);
        if (!$seance || $seance['statut'] !== 'TERMINEE') {
            return null;
        }

        if ($this->avisModel->nombreAvisPourSeance($idSeance) < 2) {
            return null;
        }

        $litigeRows = $this->signalementModel->where(['cibleType' => 'SEANCE', 'cibleId' => $idSeance]);
        foreach ($litigeRows as $litige) {
            if ($litige['statut'] !== 'CLOTURE') {
                return null;
            }
        }

        if ($this->certificatModel->existePourSeance($idSeance)) {
            return null;
        }

        return $this->genererCertificat($seance);
    }

    private function genererCertificat(array $seance): int
    {
        $ids = $this->certificatModel->genererIdentifiants();

        $idCertificat = $this->certificatModel->insert([
            'identifiantUnique'  => $ids['identifiantUnique'],
            'competenceAcquise'  => 'Compétence échangée lors de la séance #' . $seance['idSeance'],
            'codeVerification'   => $ids['codeVerification'],
            'signatureNumerique' => $ids['signatureNumerique'],
            'statut'             => 'ACTIF',
            'idSeance'           => $seance['idSeance'],
            'idApprenant'        => $seance['idParticipantA'],
            'idFormateur'        => $seance['idParticipantB'],
        ]);

        $this->notificationModel->creer(
            $seance['idParticipantA'],
            'CERTIFICAT',
            'Un certificat vous a été délivré (code de vérification : ' . $ids['codeVerification'] . ').'
        );

        return $idCertificat;
    }

    public function regenerer($idCertificatExistant): int
    {
        $ancien = $this->certificatModel->find($idCertificatExistant);
        $this->certificatModel->update($idCertificatExistant, ['statut' => 'REMPLACE']);

        $ids = $this->certificatModel->genererIdentifiants();
        return $this->certificatModel->insert([
            'identifiantUnique'     => $ids['identifiantUnique'],
            'competenceAcquise'     => $ancien['competenceAcquise'],
            'codeVerification'      => $ids['codeVerification'],
            'signatureNumerique'    => $ids['signatureNumerique'],
            'statut'                => 'ACTIF',
            'idSeance'              => $ancien['idSeance'],
            'idApprenant'           => $ancien['idApprenant'],
            'idFormateur'           => $ancien['idFormateur'],
            'idCertificatPrecedent' => $idCertificatExistant,
        ]);
    }
}
