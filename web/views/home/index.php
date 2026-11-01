<header class="hero py-5">
  <div class="swap-glyph d-none d-lg-block">⇄</div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="hero-badge mb-4"><i class="fa-solid fa-seedling"></i> Contribue à l'ODD 4 — Éducation de qualité</span>
        <h1 class="mt-4 mb-3" style="font-size:3rem;">Apprenez une compétence. <br>Transmettez la vôtre.</h1>
        <p class="lead mb-4" style="color:#D8CFB8;max-width:560px;">SkillSwap met en relation des personnes qui échangent leurs savoir-faire — sans argent, sans intermédiaire, avec confiance et réciprocité.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="<?= APP_URL ?>/inscription" class="btn btn-gold btn-lg px-4">Rejoindre SkillSwap</a>
          <a href="<?= APP_URL ?>/catalogue" class="btn btn-outline-light btn-lg px-4">Explorer le catalogue</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card-flat p-4 ms-lg-auto" style="max-width:420px;box-shadow:0 20px 50px rgba(0,0,0,0.25);">
          <div class="fw-semibold mb-2" style="color:var(--navy)"><i class="fa-solid fa-arrows-rotate me-2" style="color:var(--green)"></i>Compétences récemment publiées</div>
          <?php foreach (array_slice($competences, 0, 3) as $c): ?>
            <div class="d-flex align-items-center gap-2 py-2 border-bottom">
              <span class="cat-tag"><?= htmlspecialchars($c['categorieNom']) ?></span>
              <span class="small fw-semibold"><?= htmlspecialchars($c['titre']) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (empty($competences)): ?>
            <p class="text-muted small mb-0">Aucune compétence publiée pour le moment. Soyez le premier à en ajouter une !</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</header>

<section class="py-5 container">
  <div class="text-center mb-5 pt-4">
    <div class="section-label">Le principe</div>
    <h2 class="mt-2">Trois étapes, aucune transaction</h2>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card-flat p-4 h-100">
        <div style="font-family:'Fraunces',serif;font-size:2.2rem;color:var(--gold);font-weight:700;">01</div>
        <h3 class="h5">Publiez vos compétences</h3>
        <p class="text-muted mb-0">Indiquez ce que vous savez faire, et ce que vous aimeriez apprendre.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-flat p-4 h-100">
        <div style="font-family:'Fraunces',serif;font-size:2.2rem;color:var(--gold);font-weight:700;">02</div>
        <h3 class="h5">Trouvez une correspondance</h3>
        <p class="text-muted mb-0">SkillSwap met en évidence les échanges réciproques les plus pertinents.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-flat p-4 h-100">
        <div style="font-family:'Fraunces',serif;font-size:2.2rem;color:var(--gold);font-weight:700;">03</div>
        <h3 class="h5">Échangez et gagnez des points</h3>
        <p class="text-muted mb-0">Chaque séance réalisée rapporte des points, des avis et parfois un certificat.</p>
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background:#F3EFE4;">
  <div class="container text-center">
    <h2 class="mb-3">Prêt à échanger votre premier savoir ?</h2>
    <p class="text-muted mb-4">L'inscription est gratuite et prend moins de deux minutes.</p>
    <a href="<?= APP_URL ?>/inscription" class="btn btn-navy btn-lg px-5">Créer mon compte</a>
  </div>
</section>
