<div class="row g-4">
  <?php foreach ($certificats as $c): ?>
    <div class="col-md-6">
      <div class="card-flat p-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <span class="badge-pill"><i class="fa-solid fa-certificate" style="color:var(--gold)"></i> <?= htmlspecialchars($c['statut']) ?></span>
          <span class="text-muted small"><?= date('d/m/Y', strtotime($c['dateDelivrance'])) ?></span>
        </div>
        <h3 class="h6"><?= htmlspecialchars($c['competenceAcquise']) ?></h3>
       <?php $estApprenant = ((int)$c['idApprenant'] === (int)\App\Core\Auth::id()); ?>
<p class="small text-muted mb-2">
  <?php if ($estApprenant): ?>
    Formateur : <?= htmlspecialchars($c['formateurPrenom'] . ' ' . $c['formateurNom']) ?>
  <?php else: ?>
    Apprenant : <?= htmlspecialchars($c['apprenantPrenom'] . ' ' . $c['apprenantNom']) ?>
  <?php endif; ?>
</p>
        <div class="mono small p-2" style="background:var(--panel);border-radius:8px;">Code : <?= htmlspecialchars($c['codeVerification']) ?></div>
        <a href="<?= APP_URL ?>/certificats/<?= (int)$c['idCertificat'] ?>" class="btn btn-sm btn-outline-secondary mt-3"><i class="fa-solid fa-eye me-1"></i>Voir / Télécharger</a>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($certificats)): ?>
    <div class="col-12 text-center text-muted py-5">Aucun certificat pour le moment. Complétez une séance et recevez deux avis pour en obtenir un !</div>
  <?php endif; ?>
</div>
