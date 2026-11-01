<div class="card-flat p-4">
  <?php foreach ($notifications as $n): ?>
    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="activity-icon"><i class="fa-solid fa-bell" style="color:<?= $n['statutLecture'] ? '#aaa' : 'var(--gold)' ?>"></i></div>
        <div>
          <div class="<?= $n['statutLecture'] ? 'text-muted' : 'fw-semibold' ?>"><?= htmlspecialchars($n['contenu']) ?></div>
          <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($n['dateCreation'])) ?></div>
        </div>
      </div>
      <?php if (!$n['statutLecture']): ?>
        <form method="post" action="<?= APP_URL ?>/notifications/<?= $n['idNotification'] ?>/lue">
          <button class="btn btn-sm btn-outline-navy">Marquer comme lue</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (empty($notifications)): ?><p class="text-muted text-center py-4 mb-0">Aucune notification.</p><?php endif; ?>
</div>
