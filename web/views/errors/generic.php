<?php $pageTitle = $title ?? 'Erreur'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($pageTitle) ?> — SkillSwap</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="text-center">
    <h1 class="display-4 fw-bold" style="color:#1F3A5F;"><?= $code ?? 500 ?></h1>
    <p class="lead"><?= htmlspecialchars($message ?? 'Une erreur est survenue.') ?></p>
    <a href="<?= APP_URL ?>/" class="btn" style="background:#1F3A5F;color:#fff;">Retour à l'accueil</a>
  </div>
</body>
</html>
