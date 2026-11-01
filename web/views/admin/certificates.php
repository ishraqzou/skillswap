<div class="card-flat p-4">
  <table class="table table-skillswap">
    <thead><tr><th>Identifiant</th><th>Compétence</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($certificats as $c): ?>
        <tr>
          <td class="mono small"><?= htmlspecialchars($c['identifiantUnique']) ?></td>
          <td><?= htmlspecialchars($c['competenceAcquise']) ?></td>
          <td><span class="status-pill <?= $c['statut'] === 'ACTIF' ? 'active' : 'negative' ?>"><?= htmlspecialchars($c['statut']) ?></span></td>
          <td class="small text-muted"><?= date('d/m/Y', strtotime($c['dateDelivrance'])) ?></td>
          <td>
            <div class="d-flex gap-1">
              <?php if ($c['statut'] === 'ACTIF'): ?>
                <form method="post" action="<?= APP_URL ?>/admin/certificats/<?= $c['idCertificat'] ?>/revoquer" data-confirm="Confirmez-vous la révocation de ce certificat ?">
                  <button class="btn btn-sm btn-outline-danger">Révoquer</button>
                </form>
                <form method="post" action="<?= APP_URL ?>/admin/certificats/<?= $c['idCertificat'] ?>/regenerer" data-confirm="Régénérer ce certificat ?">
                  <button class="btn btn-sm btn-outline-navy">Régénérer</button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
