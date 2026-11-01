<?php use App\Core\Auth; ?>
<div class="row g-0 card-flat overflow-hidden" style="min-height:65vh;">
  <div class="col-md-4 border-end">
    <div class="p-3 border-bottom fw-semibold">Conversations</div>
    <div class="chat-list">
      <?php foreach ($conversations as $c): ?>
        <a href="<?= APP_URL ?>/messagerie/<?= $c['idConversation'] ?>" class="d-flex align-items-center gap-2 p-3 text-decoration-none text-reset border-bottom <?= ($conversationActive && $conversationActive['idConversation'] == $c['idConversation']) ? 'bg-light' : '' ?>">
          <?php if (!empty($c['interlocuteurPhoto'])): ?>
  <img src="<?= APP_URL ?>/<?= htmlspecialchars($c['interlocuteurPhoto']) ?>" alt="" class="avatar-sm" style="object-fit:cover;">
<?php else: ?>
  <div class="avatar-sm"><?= strtoupper(mb_substr($c['interlocuteurPrenom'],0,1) . mb_substr($c['interlocuteurNom'],0,1)) ?></div>
<?php endif; ?>
          <div>
            <div class="fw-semibold small"><?= htmlspecialchars($c['interlocuteurPrenom'] . ' ' . $c['interlocuteurNom']) ?></div>
            <div class="text-muted" style="font-size:.75rem;">Conversation ouverte le <?= date('d/m/Y', strtotime($c['dateCreation'])) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if (empty($conversations)): ?><p class="text-muted small p-3">Aucune conversation. Elles s'ouvrent automatiquement lorsqu'une demande d'échange est acceptée.</p><?php endif; ?>
    </div>
  </div>
  <div class="col-md-8 d-flex flex-column">
    <?php if ($conversationActive): ?>
      <div class="p-3 border-bottom fw-semibold">Conversation</div>
      <div class="chat-list flex-grow-1 p-3">
        <?php foreach ($messages as $m): ?>
          <?php $mine = (int) $m['idExpediteur'] === (int) Auth::id(); ?>
          <div class="d-flex flex-column <?= $mine ? 'align-items-end' : 'align-items-start' ?>">
            <div class="chat-bubble <?= $mine ? 'mine' : 'theirs' ?>"><?= htmlspecialchars($m['contenu']) ?></div>
            <div class="text-muted mb-2" style="font-size:.7rem;"><?= htmlspecialchars($m['expediteurPrenom']) ?> · <?= date('H:i', strtotime($m['dateEnvoi'])) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><p class="text-muted small">Aucun message pour le moment. Envoyez le premier !</p><?php endif; ?>
      </div>
      <form method="post" action="<?= APP_URL ?>/messagerie/<?= $conversationActive['idConversation'] ?>/envoyer" class="p-3 border-top d-flex gap-2">
        <input type="text" name="contenu" class="form-control" placeholder="Écrire un message..." required autocomplete="off">
        <button class="btn btn-navy"><i class="fa-solid fa-paper-plane"></i></button>
      </form>
    <?php else: ?>
      <div class="d-flex align-items-center justify-content-center flex-grow-1 text-muted">Sélectionnez une conversation pour l'afficher.</div>
    <?php endif; ?>
  </div>
</div>
