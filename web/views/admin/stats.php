<div class="row g-3">
  <div class="col-md-4">
    <div class="card-flat p-4 text-center">
      <div class="kpi-value"><?= (int) $nbUtilisateurs ?></div>
      <div class="text-muted small">Utilisateurs inscrits</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-flat p-4 text-center">
      <div class="kpi-value"><?= (int) $nbCompetences ?></div>
      <div class="text-muted small">Compétences actives</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-flat p-4 text-center">
      <div class="kpi-value"><?= (int) $nbDemandesAcceptees ?></div>
      <div class="text-muted small">Demandes acceptées</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-flat p-4 text-center">
      <div class="kpi-value"><?= (int) $nbSeancesTerminees ?></div>
      <div class="text-muted small">Séances complétées</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-flat p-4 text-center">
      <div class="kpi-value"><?= (int) $nbCertificats ?></div>
      <div class="text-muted small">Certificats actifs délivrés</div>
    </div>
  </div>
</div>
<p class="text-muted small mt-4">Ces indicateurs correspondent aux exigences FR-122 à FR-129 (cahier des spécifications fonctionnelles, Tâche 2) et aux règles BR-065 à BR-067 (catalogue des règles métier, Tâche 5).</p>
