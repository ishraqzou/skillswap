<form method="get" class="row g-2 mb-4">
  <div class="col-md-4">
    <input type="text" name="q" class="form-control" placeholder="Rechercher un mot-clé..." value="<?= htmlspecialchars($filtres['q'] ?? '') ?>">
  </div>
  <div class="col-md-3">
    <select name="categorie" class="form-select">
      <option value="">Toutes catégories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['idCategorie'] ?>" <?= ($filtres['categorie'] ?? '') == $cat['idCategorie'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="type" class="form-select">
      <option value="">Offerte / Demandée</option>
      <option value="OFFERTE" <?= ($filtres['type'] ?? '') === 'OFFERTE' ? 'selected' : '' ?>>Offerte</option>
      <option value="DEMANDEE" <?= ($filtres['type'] ?? '') === 'DEMANDEE' ? 'selected' : '' ?>>Demandée</option>
    </select>
  </div>
  <div class="col-md-3">
    <button class="btn btn-navy w-100" type="submit">Rechercher</button>
  </div>
</form>

<div class="row g-4">
  <?php foreach ($resultats as $c): ?>
    <div class="col-sm-6 col-lg-4">
      <a href="<?= APP_URL ?>/competences/<?= $c['idCompetence'] ?>" class="text-decoration-none text-reset">
        <div class="skill-card p-3">
          <span class="cat-tag"><?= htmlspecialchars($c['categorieNom']) ?></span>
          <span class="badge-pill float-end"><?= $c['type'] === 'OFFERTE' ? 'Offerte' : 'Demandée' ?></span>
          <h3 class="h6 mt-3"><?= htmlspecialchars($c['titre']) ?></h3>
          <p class="small text-muted mb-2"><?= htmlspecialchars(mb_substr($c['description'], 0, 80)) ?>…</p>
          <div class="swap-for"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($c['auteurPrenom']) ?> <?= htmlspecialchars(mb_substr($c['auteurNom'],0,1)) ?>. · <i class="fa-solid fa-star" style="color:var(--gold)"></i> <?= $c['auteurNote'] !== null ? number_format($c['auteurNote'],1) : '—' ?></div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
  <?php if (empty($resultats)): ?>
    <div class="col-12 text-center text-muted py-5">Aucune compétence ne correspond à vos critères de recherche.</div>
  <?php endif; ?>
</div>
