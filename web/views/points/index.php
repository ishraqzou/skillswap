<div class="row g-4">
  <div class="col-lg-4">
    <div class="card-flat p-4 text-center mb-4">
      <div class="text-muted small">Solde actuel</div>
      <div class="mono" style="font-size:2.4rem;color:var(--navy);font-weight:600;"><?= (int) $solde ?></div>
      <div class="text-muted small">points</div>
    </div>
    <div class="card-flat p-4">
      <div class="section-label mb-3">Badges</div>
      <div class="d-flex gap-3 flex-wrap">
        <?php foreach ($tousBadges as $b): ?>
          <?php $obtenu = in_array($b['idBadge'], array_column($badges, 'idBadge')); ?>
          <div class="text-center" style="width:74px;">
            <div class="badge-icon <?= $obtenu ? '' : 'locked' ?> mx-auto"><i class="fa-solid <?= $obtenu ? 'fa-award' : 'fa-lock' ?>"></i></div>
            <div class="small mt-1"><?= htmlspecialchars($b['nom']) ?></div>
            <div class="text-muted" style="font-size:.7rem;"><?= (int) $b['seuilPoints'] ?> pts</div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card-flat p-4">
      <div class="section-label mb-3">Historique des points</div>
      <table class="table table-skillswap">
        <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Motif</th></tr></thead>
        <tbody>
          <?php foreach ($historique as $h): ?>
            <tr>
              <td class="small"><?= date('d/m/Y', strtotime($h['dateCreation'])) ?></td>
              <td><span class="badge-pill"><?= $h['type'] === 'ATTRIBUTION' ? 'Séance complétée' : 'Ajustement admin.' ?></span></td>
              <td class="mono fw-semibold" style="color:<?= $h['montant'] >= 0 ? 'var(--green)' : 'var(--alert)' ?>"><?= $h['montant'] >= 0 ? '+' : '' ?><?= (int) $h['montant'] ?></td>
              <td class="small text-muted"><?= htmlspecialchars($h['motif'] ?? '—') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($historique)): ?><tr><td colspan="4" class="text-center text-muted">Aucun mouvement de points pour le moment.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
