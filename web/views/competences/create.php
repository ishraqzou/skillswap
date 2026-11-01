<div class="card-flat p-4" style="max-width:640px;">
  <form method="post" action="<?= APP_URL ?>/mes-competences/ajouter">
    <div class="mb-3">
      <label class="form-label">Type</label>
      <select name="type" class="form-select">
        <option value="OFFERTE" <?= ($old['type'] ?? '') === 'OFFERTE' ? 'selected' : '' ?>>Compétence offerte (je sais faire)</option>
        <option value="DEMANDEE" <?= ($old['type'] ?? '') === 'DEMANDEE' ? 'selected' : '' ?>>Compétence demandée (je veux apprendre)</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Titre</label>
      <input type="text" name="titre" class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['titre'] ?? '') ?>" required>
      <?php if (isset($errors['titre'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Catégorie</label>
      <select name="idCategorie" class="form-select <?= isset($errors['idCategorie']) ? 'is-invalid' : '' ?>">
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['idCategorie'] ?>" <?= ($old['idCategorie'] ?? '') == $cat['idCategorie'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Niveau</label>
      <select name="niveau" class="form-select">
        <option value="DEBUTANT">Débutant</option>
        <option value="INTERMEDIAIRE">Intermédiaire</option>
        <option value="AVANCE">Avancé</option>
        <option value="EXPERT">Expert</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" rows="4" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
      <div class="form-text">Minimum 20 caractères (BR-011).</div>
      <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Mots-clés</label>
      <input type="text" name="motsCles" class="form-control" value="<?= htmlspecialchars($old['motsCles'] ?? '') ?>" placeholder="séparés par des virgules">
    </div>
    <button type="submit" class="btn btn-navy">Publier la compétence</button>
  </form>
</div>
