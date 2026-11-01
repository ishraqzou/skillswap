<div class="card-flat p-4" style="max-width:520px;">
  <h1 class="h4 mb-3">Changer mon mot de passe</h1>
  <form method="post" action="<?= APP_URL ?>/profil/mot-de-passe" novalidate>
    <div class="mb-3">
      <label class="form-label">Mot de passe actuel</label>
      <input type="password" name="ancien" class="form-control <?= isset($errors['ancien']) ? 'is-invalid' : '' ?>" required>
      <?php if (isset($errors['ancien'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['ancien']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Nouveau mot de passe</label>
      <input type="password" name="motDePasse" class="form-control <?= isset($errors['motDePasse']) ? 'is-invalid' : '' ?>" required>
      <?php if (isset($errors['motDePasse'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['motDePasse']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirmer le nouveau mot de passe</label>
      <input type="password" name="motDePasseConfirm" class="form-control <?= isset($errors['motDePasseConfirm']) ? 'is-invalid' : '' ?>" required>
      <?php if (isset($errors['motDePasseConfirm'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['motDePasseConfirm']) ?></div><?php endif; ?>
    </div>
    <button type="submit" class="btn btn-navy">Enregistrer</button>
    <a href="<?= APP_URL ?>/profil/modifier" class="btn btn-outline-secondary ms-2">Annuler</a>
  </form>
</div>