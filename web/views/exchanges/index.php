<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#recues">Reçues (<?= count($recues) ?>)</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#envoyees">Envoyées (<?= count($envoyees) ?>)</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="recues">
    <?php foreach ($recues as $d): ?>
      <div class="card-flat p-3 mb-3 d-flex flex-row justify-content-between align-items-center flex-wrap gap-2">
        <div class="exchange-mini">
          <strong><?= htmlspecialchars($d['emetteurPrenom'] . ' ' . $d['emetteurNom']) ?></strong>
          <span class="mx-2"><?= htmlspecialchars($d['competenceProposeeTitre']) ?></span>
          <i class="fa-solid fa-right-left swap-icon"></i>
          <span class="mx-2">votre compétence</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="status-pill <?= $d['statut'] === 'EN_ATTENTE' ? 'pending' : ($d['statut'] === 'ACCEPTEE' ? 'active' : 'negative') ?>"><?= htmlspecialchars($d['statut']) ?></span>
          <?php if ($d['statut'] === 'EN_ATTENTE'): ?>
            <form method="post" action="<?= APP_URL ?>/demandes-echange/<?= $d['idDemande'] ?>/repondre" class="d-inline">
              <input type="hidden" name="decision" value="accepter">
              <button class="btn btn-sm btn-green">Accepter</button>
            </form>
            <form method="post" action="<?= APP_URL ?>/demandes-echange/<?= $d['idDemande'] ?>/repondre" class="d-inline">
              <input type="hidden" name="decision" value="refuser">
              <button class="btn btn-sm btn-outline-danger">Refuser</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($recues)): ?><p class="text-muted">Aucune demande reçue.</p><?php endif; ?>
  </div>

  <div class="tab-pane fade" id="envoyees">
    <?php foreach ($envoyees as $d): ?>
      <div class="card-flat p-3 mb-3 d-flex flex-row justify-content-between align-items-center flex-wrap gap-2">
        <div class="exchange-mini">
          <span class="mx-2">Vers <strong><?= htmlspecialchars($d['destinatairePrenom'] . ' ' . $d['destinataireNom']) ?></strong></span>
          <i class="fa-solid fa-right-left swap-icon"></i>
          <span class="mx-2"><?= htmlspecialchars($d['competenceSouhaiteeTitre']) ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="status-pill <?= $d['statut'] === 'EN_ATTENTE' ? 'pending' : ($d['statut'] === 'ACCEPTEE' ? 'active' : 'negative') ?>"><?= htmlspecialchars($d['statut']) ?></span>
          <?php if ($d['statut'] === 'EN_ATTENTE'): ?>
            <form method="post" action="<?= APP_URL ?>/demandes-echange/<?= $d['idDemande'] ?>/annuler">
              <button class="btn btn-sm btn-outline-navy">Annuler</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($envoyees)): ?><p class="text-muted">Aucune demande envoyée.</p><?php endif; ?>
  </div>
</div>
