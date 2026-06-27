"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {creerLigneCoureur} from "../coureur.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesCoureurs  */
const search = document.getElementById('search');
const nb = document.getElementById('nb');
const lesLignes = document.getElementById('lesLignes');


// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.onfocus = () => {
    search.value = '';
};

// sur chaque caractère saisi dans le champ de recherche
search.oninput = afficher;


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher() {
    lesLignes.innerHTML = '';
    let compteur = 0;

    for (const coureur of lesCoureurs) {
        if (!filtrer(coureur)) {
            continue;
        }
        compteur++;

        lesLignes.appendChild(creerLigneCoureur(coureur));
    }

    nb.innerText = compteur;
}


/**
 * Filtre les coureurs en fonction des critères sélectionnés.
 * @param coureur
 * @returns {boolean}
 */
function filtrer(coureur) {
    if (search.value === '') {
        return true; // Si le champ de recherche est vide, on affiche tous les coureurs
    }
    return coureur.nomPrenom.toLowerCase().includes(search.value.toLowerCase()) ||
           coureur.licence.includes(search.value) ||
           coureur.dateNaissanceFr.includes(search.value) ||
           coureur.nomClub.toLowerCase().includes(search.value.toLowerCase());
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

afficher();



