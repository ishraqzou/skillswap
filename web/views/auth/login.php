<div class="container py-5" style="max-width:440px;">
  <div class="text-center mb-4">
    <span class="swap-mark" style="width:48px;height:48px;font-size:1.3rem;"><i class="fa-solid fa-right-left"></i></span>
    <h1 class="h3 mt-3">Connexion</h1>
    <p class="text-muted">Heureux de vous revoir sur SkillSwap.</p>
  </div>

  <div class="card-flat p-4">
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= APP_URL ?>/connexion">
      <div class="mb-3">
        <label class="form-label">Adresse e-mail</label>
        <input type="email" name="email" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="motDePasse" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-navy w-100">Se connecter</button>
    </form>
  </div>

  <p class="text-center text-muted mt-3">Pas encore de compte ? <a href="<?= APP_URL ?>/inscription" style="color:var(--navy);font-weight:600;">S'inscrire</a></p>
</div>
