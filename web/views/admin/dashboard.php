<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card-flat kpi-card p-3">
      <div class="text-muted small">Utilisateurs</div>
      <div class="kpi-value"><?= (int) $nbUtilisateurs ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-flat kpi-card green p-3">
      <div class="text-muted small">Demandes en attente</div>
      <div class="kpi-value"><?= (int) $nbDemandesEnAttente ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-flat kpi-card gold p-3">
      <div class="text-muted small">Certificats actifs</div>
      <div class="kpi-value"><?= (int) $nbCertificats ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-flat kpi-card alert p-3">
      <div class="text-muted small">Signalements en attente</div>
      <div class="kpi-value"><?= (int) $nbSignalementsAttente ?></div>
    </div>
  </div>
</div>

<div class="card-flat p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="section-label mb-0">Derniers signalements en attente</div>
    <a href="<?= APP_URL ?>/admin/signalements" class="small fw-semibold" style="color:var(--navy)">Tout voir</a>
  </div>
  <table class="table table-skillswap mb-0">
    <thead><tr><th>Auteur</th><th>Cible</th><th>Motif</th></tr></thead>
    <tbody>
      <?php foreach ($signalements as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['auteurPrenom'] . ' ' . $s['auteurNom']) ?></td>
          <td><?= htmlspecialchars($s['cibleType']) ?> #<?= (int) $s['cibleId'] ?></td>
          <td><?= htmlspecialchars($s['motif']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($signalements)): ?><tr><td colspan="3" class="text-center text-muted">Aucun signalement en attente.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
