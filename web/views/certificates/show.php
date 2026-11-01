<style>
  body { background: var(--panel); }
  .cert-page { max-width: 960px; margin: 24px auto; padding: 0 16px; }
  .cert-sheet { background: #fff; border: 10px double var(--gold); padding: 48px 40px; text-align: center; }
  .cert-title { font-family: 'Fraunces', serif; font-size: 2.6rem; color: var(--navy); margin: 8px 0 4px; }
  .cert-name { font-family: 'Fraunces', serif; font-size: 2.2rem; color: var(--navy); border-bottom: 2px solid var(--gold); display: inline-block; padding: 0 24px 4px; }
  .cert-skill { font-size: 1.6rem; font-weight: 700; color: var(--gold); }
  @media print {
    @page { size: A4 landscape; margin: 10mm; }
    body { background: #fff; }
    .no-print { display: none !important; }
    .cert-page { margin: 0; max-width: none; padding: 0; }
  }
</style>

<div class="cert-page">
  <div class="no-print d-flex justify-content-between mb-3">
    <a href="<?= APP_URL ?>/certificats" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Retour</a>
    <button onclick="window.print()" class="btn btn-navy"><i class="fa-solid fa-print me-1"></i>Imprimer / Enregistrer en PDF</button>
  </div>

  <div class="cert-sheet">
    <div><span class="swap-mark"><i class="fa-solid fa-right-left"></i></span> <strong style="font-family:'Fraunces',serif;font-size:1.4rem;color:var(--navy);">SkillSwap</strong></div>
    <div class="cert-title">Certificat de compétence</div>
    <p class="text-muted mb-4">décerné à</p>

    <div class="cert-name"><?= htmlspecialchars($certificat['apprenantPrenom'] . ' ' . $certificat['apprenantNom']) ?></div>

    <p class="mt-4 mb-1">pour avoir acquis la compétence</p>
    <div class="cert-skill mb-3"><?= htmlspecialchars($certificat['competenceAcquise']) ?></div>
    <p>auprès de <strong><?= htmlspecialchars($certificat['formateurPrenom'] . ' ' . $certificat['formateurNom']) ?></strong>,
       le <?= date('d/m/Y', strtotime($certificat['dateDelivrance'])) ?></p>

    <?php if ($certificat['statut'] !== 'ACTIF'): ?>
      <div class="alert alert-danger d-inline-block mt-2">Certificat non valide : statut <?= htmlspecialchars($certificat['statut']) ?></div>
    <?php endif; ?>

    <hr class="my-4">
    <div class="small text-muted">Identifiant : <span class="mono"><?= htmlspecialchars($certificat['identifiantUnique']) ?></span></div>
    <div class="small text-muted">Code de vérification : <span class="mono"><?= htmlspecialchars($certificat['codeVerification']) ?></span></div>
    <div class="small text-muted">Vérifier sur : <?= htmlspecialchars(APP_URL) ?>/certificats/verifier?code=<?= urlencode($certificat['codeVerification']) ?></div>
  </div>
</div>