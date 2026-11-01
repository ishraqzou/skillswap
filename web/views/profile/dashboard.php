<form method="get" action="<?= APP_URL ?>/rechercher" class="mb-4">
  <div class="input-group input-group-lg">
    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
    <input type="text" name="q" class="form-control" placeholder="Rechercher une compétence (ex : Photographie, Python, Cuisine…)" aria-label="Rechercher une compétence">
    <button class="btn btn-navy" type="submit">Rechercher</button>
  </div>
</form>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card-flat p-4 text-center">
      <?php $initiales = strtoupper(mb_substr($utilisateur['prenom'],0,1) . mb_substr($utilisateur['nom'],0,1)); ?>
<form id="formPhoto" method="post" action="<?= APP_URL ?>/profil/photo" enctype="multipart/form-data" class="d-none">
  <input type="file" name="photoProfil" id="inputPhoto" accept="image/png,image/jpeg,image/webp"
         onchange="document.getElementById('formPhoto').submit()">
</form>

<div class="dropdown d-inline-block mb-3">
  <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="d-block" style="cursor:pointer;">
    <?php if (!empty($utilisateur['photoProfil'])): ?>
      <img src="<?= APP_URL ?>/<?= htmlspecialchars($utilisateur['photoProfil']) ?>" alt="Photo de profil" class="avatar-lg" style="object-fit:cover;">
    <?php else: ?>
      <div class="avatar-lg"><?= $initiales ?></div>
    <?php endif; ?>
  </a>
  <ul class="dropdown-menu">
    <?php if (!empty($utilisateur['photoProfil'])): ?>
      <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalPhoto"><i class="fa-solid fa-eye me-2"></i>Voir la photo</a></li>
    <?php endif; ?>
    <li><a class="dropdown-item" href="#" onclick="document.getElementById('inputPhoto').click(); return false;"><i class="fa-solid fa-camera me-2"></i>Changer la photo</a></li>
  </ul>
</div>
      <h2 class="h5 mb-0"><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></h2>
      <div class="text-muted small mb-3"><?= htmlspecialchars($utilisateur['ville'] ?? 'Ville non renseignée') ?></div>
      <div class="d-flex justify-content-center gap-2 mb-3">
        <span class="badge-pill"><i class="fa-solid fa-star" style="color:var(--gold)"></i> <?= $utilisateur['noteMoyenne'] !== null ? number_format($utilisateur['noteMoyenne'],1) : '—' ?> / 5</span>
      </div>
      <a href="<?= APP_URL ?>/profil/modifier" class="btn btn-sm btn-navy w-100">Modifier mon profil</a>
    </div>

    <div class="card-flat p-4 mt-4">
      <div class="section-label mb-3">Badges obtenus</div>
      <?php if (empty($badges)): ?>
        <p class="text-muted small mb-0">Aucun badge pour le moment. Complétez votre première séance pour commencer !</p>
      <?php endif; ?>
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
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="card-flat p-3 text-center">
          <div class="mono fs-4 fw-semibold" style="color:var(--navy)"><?= (int) $soldePoints ?></div>
          <div class="small text-muted">Points cumulés</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card-flat p-3 text-center">
          <div class="mono fs-4 fw-semibold" style="color:var(--navy)"><?= count($certificats) ?></div>
          <div class="small text-muted">Certificats</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card-flat p-3 text-center">
          <div class="mono fs-4 fw-semibold" style="color:var(--navy)"><?= (int) $demandesRecues ?></div>
          <div class="small text-muted">Demandes en attente</div>
        </div>
      </div>
    </div>

    <div class="card-flat p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-label mb-0">Mes compétences</div>
        <a href="<?= APP_URL ?>/mes-competences/ajouter" class="small fw-semibold" style="color:var(--green)">+ Ajouter une compétence</a>
      </div>
      <?php if (empty($competences)): ?>
        <p class="text-muted small mb-0">Vous n'avez pas encore publié de compétence.</p>
      <?php endif; ?>
      <?php foreach (array_slice($competences, 0, 5) as $c): ?>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div>
            <span class="status-dot status-<?= strtolower($c['statut']) ?>"></span><strong><?= htmlspecialchars($c['titre']) ?></strong>
            <div class="exchange-mini text-muted mt-1"><i class="fa-solid fa-tag"></i> <?= $c['type'] === 'OFFERTE' ? 'Offerte' : 'Demandée' ?></div>
          </div>
          <span class="badge-pill"><?= htmlspecialchars($c['statut']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card-flat p-4">
      <div class="section-label mb-3">Avis reçus récemment</div>
      <?php if (empty($avis)): ?>
        <p class="text-muted small mb-0">Aucun avis reçu pour le moment.</p>
      <?php endif; ?>
      <?php foreach (array_slice($avis, 0, 4) as $a): ?>
        <div class="activity-item">
          <div class="activity-icon"><i class="fa-solid fa-star" style="color:var(--gold)"></i></div>
          <div>
            <div><strong><?= htmlspecialchars($a['auteurPrenom']) ?></strong> — <?= (int) $a['note'] ?>/5</div>
            <div class="text-muted small"><?= htmlspecialchars($a['commentaire'] ?? '') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php if (!empty($utilisateur['photoProfil'])): ?>
<div class="modal fade" id="modalPhoto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <img src="<?= APP_URL ?>/<?= htmlspecialchars($utilisateur['photoProfil']) ?>" class="img-fluid rounded" alt="Photo de profil">
    </div>
  </div>
</div>
<?php endif; ?>