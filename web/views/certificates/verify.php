<div class="container py-5" style="max-width:560px;">
  <h1 class="h4 text-center mb-4"><i class="fa-solid fa-shield-halved" style="color:var(--gold)"></i> Vérifier un certificat SkillSwap</h1>
  <form method="get" class="d-flex gap-2 mb-4">
    <input type="text" name="code" class="form-control mono" placeholder="Code de vérification" value="<?= htmlspecialchars($code) ?>">
    <button class="btn btn-navy">Vérifier</button>
  </form>

  <?php if ($code !== ''): ?>
    <?php if ($certificat): ?>
      <div class="card-flat p-4">
        <div class="text-center mb-3"><span class="badge-pill" style="font-size:1rem;"><i class="fa-solid fa-circle-check" style="color:var(--green)"></i> Certificat <?= htmlspecialchars($certificat['statut']) ?></span></div>
        <table class="table table-borderless small">
          <tr><td class="text-muted">Compétence</td><td class="fw-semibold"><?= htmlspecialchars($certificat['competenceAcquise']) ?></td></tr>
          <tr><td class="text-muted">Apprenant</td><td><?= htmlspecialchars($certificat['apprenantPrenom'] . ' ' . $certificat['apprenantNom']) ?></td></tr>
          <tr><td class="text-muted">Formateur</td><td><?= htmlspecialchars($certificat['formateurPrenom'] . ' ' . $certificat['formateurNom']) ?></td></tr>
          <tr><td class="text-muted">Date de délivrance</td><td><?= date('d/m/Y', strtotime($certificat['dateDelivrance'])) ?></td></tr>
          <tr><td class="text-muted">Identifiant</td><td class="mono"><?= htmlspecialchars($certificat['identifiantUnique']) ?></td></tr>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">Aucun certificat ne correspond à ce code de vérification.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
