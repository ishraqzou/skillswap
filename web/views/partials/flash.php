<?php
$flashMessages = $flash ?? [];
if (!empty($flashMessages)):
?>
<div class="container mt-3">
  <?php foreach ($flashMessages as $type => $message): ?>
    <?php $cls = $type === 'success' ? 'success' : ($type === 'error' ? 'danger' : 'info'); ?>
    <div class="alert alert-<?= $cls ?> alert-dismissible fade show" role="alert" data-autohide>
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
