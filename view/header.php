<?php
use ClasseTechnique\FilAriane;

// Création du fil
$fil = new FilAriane();

// Optionnel : titre final
$fil->definirTitreFinal($titre ?? null);

?>

<div>
    <?php $fil->afficher(); ?>
</div>
