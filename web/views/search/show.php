<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-flat p-4">
      <span class="cat-tag"><?= htmlspecialchars($competence['categorieNom']) ?></span>
      <span class="badge-pill float-end"><?= $competence['type'] === 'OFFERTE' ? 'Offerte' : 'Demandée' ?></span>
      <h1 class="h4 mt-3"><?= htmlspecialchars($competence['titre']) ?></h1>
      <p class="text-muted"><?= nl2br(htmlspecialchars($competence['description'])) ?></p>
      <div class="d-flex gap-3 small text-muted">
        <span><i class="fa-solid fa-layer-group"></i> Niveau : <?= htmlspecialchars($competence['niveau'] ?? 'Non précisé') ?></span>
        <span><i class="fa-solid fa-tags"></i> <?= htmlspecialchars($competence['motsCles'] ?? '') ?></span>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card-flat p-4 text-center mb-3">
      <?php if (!empty($competence['auteurPhoto'])): ?>
  <img src="<?= APP_URL ?>/<?= htmlspecialchars($competence['auteurPhoto']) ?>" alt="" class="avatar-lg mx-auto mb-2" style="object-fit:cover;">
<?php else: ?>
  <div class="avatar-lg mx-auto mb-2"><?= strtoupper(mb_substr($competence['auteurPrenom'],0,1).mb_substr($competence['auteurNom'],0,1)) ?></div>
<?php endif; ?>
      <h2 class="h6 mb-0"><?= htmlspecialchars($competence['auteurPrenom'] . ' ' . $competence['auteurNom']) ?></h2>
      <div class="text-muted small mb-2"><?= htmlspecialchars($competence['auteurVille'] ?? '') ?></div>
      <span class="badge-pill"><i class="fa-solid fa-star" style="color:var(--gold)"></i> <?= $competence['auteurNote'] !== null ? number_format($competence['auteurNote'],1) : '—' ?> / 5</span>
    </div>

    <?php if ($competence['type'] === 'DEMANDEE'): ?>
      <div class="card-flat p-4">
        <div class="section-label mb-3"><i class="fa-solid fa-right-left"></i> Proposer un échange</div>
        <?php if (empty($mesCompetencesOffertes)): ?>
          <p class="text-muted small">Vous devez d'abord <a href="<?= APP_URL ?>/mes-competences/ajouter">publier une compétence offerte</a> pour proposer un échange.</p>
        <?php else: ?>
          <form method="post" action="<?= APP_URL ?>/demandes-echange/envoyer">
            <input type="hidden" name="idDestinataire" value="<?= $competence['idCompte'] ?>">
            <input type="hidden" name="idCompetenceSouhaitee" value="<?= $competence['idCompetence'] ?>">
            <label class="form-label">Compétence que vous proposez en retour</label>
            <select name="idCompetenceProposee" class="form-select mb-3">
              <?php foreach ($mesCompetencesOffertes as $mc): ?>
                <option value="<?= $mc['idCompetence'] ?>"><?= htmlspecialchars($mc['titre']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-green w-100"><i class="fa-solid fa-right-left me-1"></i> Envoyer la demande</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
