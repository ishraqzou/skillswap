<div class="d-flex justify-content-between align-items-center mb-4">
  <div></div>
  <a href="<?= APP_URL ?>/mes-competences/ajouter" class="btn btn-navy"><i class="fa-solid fa-plus me-1"></i> Ajouter une compétence</a>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card-flat p-4">
      <div class="section-label mb-3">Compétences offertes</div>
      <?php foreach ($offertes as $c): ?>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div>
            <span class="status-dot status-<?= strtolower($c['statut']) ?>"></span><strong><?= htmlspecialchars($c['titre']) ?></strong>
            <div class="text-muted small"><?= htmlspecialchars($c['niveau'] ?? '') ?></div>
          </div>
          <div class="d-flex gap-2">
            <form method="post" action="<?= APP_URL ?>/mes-competences/<?= $c['idCompetence'] ?>/pause">
              <button class="btn btn-sm btn-outline-navy"><?= $c['statut'] === 'ACTIVE' ? 'Mettre en pause' : 'Réactiver' ?></button>
            </form>
            <form method="post" action="<?= APP_URL ?>/mes-competences/<?= $c['idCompetence'] ?>/supprimer" data-confirm="Confirmez-vous l'archivage de cette compétence ?">
              <button class="btn btn-sm btn-outline-danger">Archiver</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($offertes)): ?><p class="text-muted small mb-0 mt-2">Aucune compétence offerte publiée.</p><?php endif; ?>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-flat p-4">
      <div class="section-label mb-3">Compétences demandées</div>
      <?php foreach ($demandees as $c): ?>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div>
            <span class="status-dot status-<?= strtolower($c['statut']) ?>"></span><strong><?= htmlspecialchars($c['titre']) ?></strong>
            <div class="text-muted small"><?= htmlspecialchars($c['niveau'] ?? '') ?></div>
          </div>
          <div class="d-flex gap-2">
            <form method="post" action="<?= APP_URL ?>/mes-competences/<?= $c['idCompetence'] ?>/pause">
              <button class="btn btn-sm btn-outline-navy"><?= $c['statut'] === 'ACTIVE' ? 'Mettre en pause' : 'Réactiver' ?></button>
            </form>
            <form method="post" action="<?= APP_URL ?>/mes-competences/<?= $c['idCompetence'] ?>/supprimer" data-confirm="Confirmez-vous l'archivage de cette compétence ?">
              <button class="btn btn-sm btn-outline-danger">Archiver</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($demandees)): ?><p class="text-muted small mb-0 mt-2">Aucune compétence demandée publiée.</p><?php endif; ?>
    </div>
  </div>
</div>
