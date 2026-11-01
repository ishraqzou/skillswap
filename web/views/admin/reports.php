<div class="card-flat p-4">
  <?php foreach ($signalements as $s): ?>
    <div class="border-bottom py-3">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <strong><?= htmlspecialchars($s['auteurPrenom'] . ' ' . $s['auteurNom']) ?></strong>
          <span class="text-muted small"> a signalé <?= htmlspecialchars($s['cibleType']) ?> #<?= (int) $s['cibleId'] ?></span>
        </div>
        <span class="text-muted small"><?= date('d/m/Y', strtotime($s['dateCreation'])) ?></span>
      </div>
      <p class="mb-3"><?= htmlspecialchars($s['motif']) ?></p>
      <form method="post" action="<?= APP_URL ?>/admin/signalements/<?= $s['idSignalement'] ?>/traiter" class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label small">Décision motivée</label>
          <input type="text" name="motif" class="form-control form-control-sm" placeholder="Motif de la décision" required>
        </div>
        <div class="col-md-3">
          <button type="submit" name="decision" value="fonde" class="btn btn-sm btn-outline-danger w-100">Fondé — Agir</button>
        </div>
        <div class="col-md-3">
          <button type="submit" name="decision" value="non_fonde" class="btn btn-sm btn-outline-navy w-100">Non fondé — Clôturer</button>
        </div>
      </form>
    </div>
  <?php endforeach; ?>
  <?php if (empty($signalements)): ?><p class="text-muted text-center py-4 mb-0">Aucun signalement en attente de traitement.</p><?php endif; ?>
</div>
