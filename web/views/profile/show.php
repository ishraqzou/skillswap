<div class="row g-4">
  <div class="col-lg-4">
    <div class="card-flat p-4 text-center">
      <?php if (!empty($profil['photoProfil'])): ?>
  <img src="<?= APP_URL ?>/<?= htmlspecialchars($profil['photoProfil']) ?>" alt="Photo de profil" class="avatar-lg mx-auto mb-3" style="object-fit:cover;">
<?php else: ?>
  <div class="avatar-lg mx-auto mb-3"><?= strtoupper(mb_substr($profil['prenom'],0,1).mb_substr($profil['nom'],0,1)) ?></div>
<?php endif; ?>
      <h2 class="h5 mb-0"><?= htmlspecialchars($profil['prenom'] . ' ' . $profil['nom']) ?></h2>
      <div class="text-muted small mb-3"><?= htmlspecialchars($profil['ville'] ?? '') ?></div>
      <span class="badge-pill"><i class="fa-solid fa-star" style="color:var(--gold)"></i> <?= $profil['noteMoyenne'] !== null ? number_format($profil['noteMoyenne'],1) : '—' ?> / 5</span>
      <p class="text-muted small mt-3"><?= nl2br(htmlspecialchars($profil['biographie'] ?? 'Aucune biographie renseignée.')) ?></p>
    </div>
    <div class="card-flat p-4 mt-4">
      <div class="section-label mb-3">Badges</div>
      <div class="d-flex gap-3 flex-wrap">
        <?php foreach ($badges as $b): ?>
          <div class="text-center" style="width:70px;">
            <div class="badge-icon mx-auto"><i class="fa-solid fa-award"></i></div>
            <div class="small mt-1"><?= htmlspecialchars($b['nom']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card-flat p-4">
      <div class="section-label mb-3">Avis reçus</div>
      <?php foreach ($avis as $a): ?>
        <div class="activity-item">
          <div class="activity-icon"><i class="fa-solid fa-star" style="color:var(--gold)"></i></div>
          <div>
            <div><strong><?= htmlspecialchars($a['auteurPrenom']) ?></strong> — <?= (int) $a['note'] ?>/5</div>
            <div class="text-muted small"><?= htmlspecialchars($a['commentaire'] ?? '') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($avis)): ?><p class="text-muted small mb-0">Aucun avis pour le moment.</p><?php endif; ?>
    </div>
  </div>
</div>
