<div class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
    <div>
      <div class="section-label">Catalogue public</div>
      <h1 class="mt-2 mb-0">Compétences disponibles sur SkillSwap</h1>
    </div>
  </div>

  <form method="get" class="row g-2 mb-4">
    <div class="col-md-6">
      <input type="text" name="q" class="form-control" placeholder="Rechercher un mot-clé..." value="<?= htmlspecialchars($filtres['q'] ?? '') ?>">
    </div>
    <div class="col-md-4">
      <select name="categorie" class="form-select">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['idCategorie'] ?>" <?= ($filtres['categorie'] ?? '') == $cat['idCategorie'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-navy w-100" type="submit">Filtrer</button>
    </div>
  </form>

  <div class="row g-4">
    <?php foreach ($competences as $c): ?>
      <div class="col-sm-6 col-lg-4">
        <div class="skill-card p-3">
          <span class="cat-tag"><?= htmlspecialchars($c['categorieNom']) ?></span>
          <h3 class="h6 mt-3"><?= htmlspecialchars($c['titre']) ?></h3>
          <p class="small text-muted mb-2"><?= htmlspecialchars(mb_substr($c['description'], 0, 90)) ?>…</p>
          <div class="swap-for"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($c['auteurPrenom']) ?> <?= htmlspecialchars(mb_substr($c['auteurNom'], 0, 1)) ?>.</div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($competences)): ?>
      <div class="col-12 text-center text-muted py-5">Aucune compétence ne correspond à votre recherche.</div>
    <?php endif; ?>
  </div>

  <div class="text-center mt-5">
    <p class="text-muted">Envie d'échanger vos propres compétences ?</p>
    <a href="<?= APP_URL ?>/inscription" class="btn btn-gold btn-lg px-5">Rejoindre SkillSwap</a>
  </div>
</div>
