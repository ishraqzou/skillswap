<div class="card-flat p-4" style="max-width:640px;">
  <form method="post" action="<?= APP_URL ?>/profil/modifier" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12">
  <label class="form-label">Photo de profil</label>
  <input type="file" name="photoProfil" class="form-control" accept="image/png,image/jpeg,image/webp">
  <div class="form-text">JPG, PNG ou WEBP, 2 Mo maximum.</div>
</div>
      <div class="col-6">
        <label class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
      </div>
      <div class="col-6">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
      </div>
      <div class="col-12">
        <label class="form-label">Ville</label>
        <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($utilisateur['ville'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Langues parlées</label>
        <input type="text" name="languesParlees" class="form-control" value="<?= htmlspecialchars($utilisateur['languesParlees'] ?? '') ?>" placeholder="Français, Anglais, Arabe...">
      </div>
      <div class="col-12">
        <label class="form-label">Biographie</label>
        <textarea name="biographie" class="form-control" rows="4"><?= htmlspecialchars($utilisateur['biographie'] ?? '') ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Visibilité du profil</label>
        <select name="visibiliteProfil" class="form-select">
          <option value="PUBLIQUE" <?= $utilisateur['visibiliteProfil'] === 'PUBLIQUE' ? 'selected' : '' ?>>Publique</option>
          <option value="RESTREINTE" <?= $utilisateur['visibiliteProfil'] === 'RESTREINTE' ? 'selected' : '' ?>>Restreinte aux membres inscrits</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-navy mt-4">Enregistrer les modifications</button>
    <a href="<?= APP_URL ?>/profil/mot-de-passe" class="btn btn-outline-secondary mt-4 ms-2">Changer mon mot de passe</a>
  </form>
</div>
