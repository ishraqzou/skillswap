<?php use App\Core\Auth; ?>
<div class="row g-4">
  <?php foreach ($seances as $s): ?>
    <?php
      $estA = (int) $s['idParticipantA'] === (int) Auth::id();
      $autreNom = $estA ? $s['participantBPrenom'] . ' ' . $s['participantBNom'] : $s['participantAPrenom'] . ' ' . $s['participantANom'];
      $maConfirmation = $estA ? !empty($s['confirmationParticipantA']) : !empty($s['confirmationParticipantB']);
    ?>
    <div class="col-lg-6">
      <div class="card-flat p-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h3 class="h6 mb-0">Séance avec <?= htmlspecialchars($autreNom) ?></h3>
          <span class="status-pill <?= $s['statut'] === 'TERMINEE' ? 'active' : ($s['statut'] === 'ANNULEE' ? 'negative' : 'pending') ?>"><?= htmlspecialchars($s['statut']) ?></span>
        </div>
        <p class="small text-muted mb-3">
          <?= $s['dateHeure'] ? '<i class="fa-solid fa-calendar"></i> ' . date('d/m/Y à H:i', strtotime($s['dateHeure'])) : 'Date à définir' ?>
          <?= $s['lieuModalite'] ? ' · ' . htmlspecialchars($s['lieuModalite']) : '' ?>
        </p>

        <?php if ($s['statut'] === 'PROPOSEE'): ?>
          <form method="post" action="<?= APP_URL ?>/seances/<?= $s['idSeance'] ?>/creneau" class="row g-2">
            <div class="col-7"><input type="datetime-local" name="dateHeure" class="form-control form-control-sm" required></div>
            <div class="col-5"><input type="number" name="duree" class="form-control form-control-sm" placeholder="Durée (min)" value="60"></div>
            <div class="col-12"><input type="text" name="lieuModalite" class="form-control form-control-sm" placeholder="Lieu / modalité"></div>
            <div class="col-12"><button class="btn btn-sm btn-navy w-100 mt-1">Confirmer le créneau</button></div>
          </form>
        <?php elseif ($s['statut'] === 'CONFIRMEE'): ?>
          <div class="d-flex gap-2">
            <form method="post" action="<?= APP_URL ?>/seances/<?= $s['idSeance'] ?>/terminer">
              <button class="btn btn-sm btn-green" <?= $maConfirmation ? 'disabled' : '' ?>>
                <?= $maConfirmation ? 'Confirmation envoyée ✓' : 'Marquer comme terminée' ?>
              </button>
            </form>
            <form method="post" action="<?= APP_URL ?>/seances/<?= $s['idSeance'] ?>/annuler" data-confirm="Annuler cette séance ? Aucun point ne sera attribué si elle était déjà confirmée par les deux parties.">
              <button class="btn btn-sm btn-outline-danger">Annuler</button>
            </form>
          </div>
        <?php elseif ($s['statut'] === 'TERMINEE'): ?>
          <a href="<?= APP_URL ?>/seances/<?= $s['idSeance'] ?>/avis" class="btn btn-sm btn-outline-navy"><i class="fa-solid fa-star"></i> Laisser un avis</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($seances)): ?>
    <div class="col-12 text-center text-muted py-5">Aucune séance pour le moment. Envoyez une demande d'échange pour en planifier une !</div>
  <?php endif; ?>
</div>
