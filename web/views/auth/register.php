<div class="container py-5" style="max-width:520px;">
  <div class="text-center mb-4">
    <span class="swap-mark" style="width:48px;height:48px;font-size:1.3rem;"><i class="fa-solid fa-right-left"></i></span>
    <h1 class="h3 mt-3">Créer votre compte SkillSwap</h1>
    <p class="text-muted">Rejoignez une communauté qui apprend en partageant.</p>
  </div>

  <div class="card-flat p-4">
    <form method="post" action="<?= APP_URL ?>/inscription" novalidate>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Prénom</label>
          <input type="text" name="prenom" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
          <?php if (isset($errors['prenom'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div><?php endif; ?>
        </div>
        <div class="col-6">
          <label class="form-label">Nom</label>
          <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
          <?php if (isset($errors['nom'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div><?php endif; ?>
        </div>
        <div class="col-12">
          <label class="form-label">Adresse e-mail</label>
          <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
          <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
        </div>
        <div class="col-12">
          <label class="form-label">Mot de passe</label>
          <input type="password" name="motDePasse" data-password-strength class="form-control <?= isset($errors['motDePasse']) ? 'is-invalid' : '' ?>" required>
          <div class="password-strength mt-2"><div class="password-strength-bar"></div></div>
          <div class="form-text">Au moins 8 caractères, avec une lettre et un chiffre.</div>
          <?php if (isset($errors['motDePasse'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['motDePasse']) ?></div><?php endif; ?>
        </div>
        <div class="col-12">
          <label class="form-label">Confirmer le mot de passe</label>
          <input type="password" name="motDePasseConfirm" class="form-control <?= isset($errors['motDePasseConfirm']) ? 'is-invalid' : '' ?>" required>
          <?php if (isset($errors['motDePasseConfirm'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['motDePasseConfirm']) ?></div><?php endif; ?>
        </div>
      </div>
      <button type="submit" class="btn btn-navy w-100 mt-4">Créer mon compte</button>
    </form>
  </div>

  <p class="text-center text-muted mt-3">Déjà inscrit ? <a href="<?= APP_URL ?>/connexion" style="color:var(--navy);font-weight:600;">Se connecter</a></p>
</div>
