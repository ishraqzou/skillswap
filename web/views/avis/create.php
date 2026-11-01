<div class="card-flat p-4" style="max-width:520px;">
  <h2 class="h5 mb-3">Évaluer votre séance</h2>
  <form method="post" action="<?= APP_URL ?>/seances/<?= $seance['idSeance'] ?>/avis">
    <div class="mb-4">
      <label class="form-label">Votre note</label>
      <input type="range" min="1" max="5" value="5" name="note" class="form-range" data-note-range>
      <div class="text-center mono fs-4" style="color:var(--gold)"><span data-note-output>5</span> / 5 <i class="fa-solid fa-star"></i></div>
    </div>
    <div class="mb-3">
      <label class="form-label">Commentaire</label>
      <textarea name="commentaire" class="form-control" rows="4" placeholder="Partagez votre expérience..."></textarea>
    </div>
    <button type="submit" class="btn btn-navy w-100">Soumettre mon avis</button>
  </form>
</div>
