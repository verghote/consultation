"use strict"; // Active le mode strict pour éviter les erreurs silencieuses

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesClubs */

const lesCartes = document.getElementById('lesCartes');
const modeleClub = document.getElementById('modeleClub');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------
function creerCarte(club) {

// clonage du template
    const fragment = modeleClub.content.cloneNode(true);

// récupération des éléments du template
    const entete = fragment.querySelector('.enteteClub');
    const image = fragment.querySelector('.logoClub');
    const pied = fragment.querySelector('.nbLicencies');

// alimentation des données
    entete.textContent = club.nom;

    pied.textContent = `${club.nb} licenciés`;

    if (club.present) {
        image.src = `/data/club/${club.fichier}`;
        image.alt = `${club.nom} logo`;
    } else {
        image.remove();
    }

    return fragment;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

for (const club of lesClubs) {
    lesCartes.appendChild(creerCarte(club));
}
