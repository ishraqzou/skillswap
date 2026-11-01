<div class="card-flat p-4">
  <table class="table table-skillswap">
    <thead><tr><th>Compte</th><th>Type</th><th>Statut</th><th>Inscription</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($comptes as $c): ?>
        <tr>
          <td class="mono small"><?= htmlspecialchars($c['email']) ?></td>
          <td><span class="badge-pill"><?= htmlspecialchars($c['typeCompte']) ?></span></td>
          <td><span class="status-pill <?= $c['statut'] === 'ACTIF' ? 'active' : 'negative' ?>"><?= htmlspecialchars($c['statut']) ?></span></td>
          <td class="small text-muted"><?= date('d/m/Y', strtotime($c['dateCreation'])) ?></td>
          <td>
            <div class="d-flex gap-1 flex-wrap">
              <form method="post" action="<?= APP_URL ?>/admin/utilisateurs/<?= $c['idCompte'] ?>/suspendre" data-confirm="Confirmez-vous ce changement de statut ?">
                <button class="btn btn-sm btn-outline-navy"><?= $c['statut'] === 'ACTIF' ? 'Suspendre' : 'Réactiver' ?></button>
              </form>
              <?php if ($c['typeCompte'] === 'UTILISATEUR'): ?>
              <button class="btn btn-sm btn-outline-navy" type="button" data-bs-toggle="modal" data-bs-target="#adjust<?= $c['idCompte'] ?>">Ajuster pts</button>
              <?php endif; ?>
            </div>
          </td>
        </tr>

        <?php if ($c['typeCompte'] === 'UTILISATEUR'): ?>
        <div class="modal fade" id="adjust<?= $c['idCompte'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="post" action="<?= APP_URL ?>/admin/points/ajuster">
                <input type="hidden" name="idUtilisateur" value="<?= $c['idCompte'] ?>">
                <div class="modal-header"><h5 class="modal-title">Ajustement manuel de points (BR-042)</h5></div>
                <div class="modal-body">
                  <label class="form-label">Montant (positif ou négatif)</label>
                  <input type="number" name="montant" class="form-control mb-3" required>
                  <label class="form-label">Motif (obligatoire — BR-042)</label>
                  <textarea name="motif" class="form-control" required></textarea>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-navy">Appliquer l'ajustement</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
