"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {creerLigneCoureur} from "../coureur.js";
import {activerTri } from "/composant/fonction/tableau.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesCoureurs */

const lesLignes = document.getElementById('lesLignes');
const nb = document.getElementById('nb');
const search = document.getElementById('search');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.oninput = () => afficher(lesCoureurs);

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(lesCoureurs) {
    const valeur = search.value.toLowerCase();

    lesLignes.innerHTML = '';
    let count = 0;

    for (const coureur of lesCoureurs) {
        // Filtrage direct, sans créer un nouveau tableau
        if (
            valeur &&
            !coureur.licence.toLowerCase().includes(valeur) &&
            !coureur.nomPrenom.toLowerCase().includes(valeur) &&
            !coureur.nomClub.toLowerCase().includes(valeur)
        ) {
            continue; // on passe au suivant
        }

        count++;

        lesLignes.appendChild(creerLigneCoureur(coureur));
    }

    nb.innerText = count;
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

afficher(lesCoureurs);

activerTri({
    idTable: "leTableau",
    getData: () => lesCoureurs,
    afficher: afficher,
    triInitial: {
        colonne: 'licence',
        ordre: "asc"
    }
});


