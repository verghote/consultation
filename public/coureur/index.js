"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {creerLigneCoureur} from "./coureur.js";
import {activerTri } from "/composant/fonction/tableau.js";
import { getData } from "/composant/fonction/page.js";
import {filtrerLaSaisie } from "/composant/fonction/formulaire.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesLignes = document.getElementById('lesLignes');
const nb = document.getElementById('nb');
const search = document.getElementById('search');

// Récupération des données injectées par Vue
const lesCoureurs = getData('lesCoureurs');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.oninput = () => afficher(lesCoureurs);

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(lesCoureurs) {
    const valeur = search.value.toLowerCase();

    lesLignes.replaceChildren();
    let count = 0;

    for (const coureur of lesCoureurs) {
        // Si une valeur de recherche est saisie et qu'aucun champ ne correspond, on passe
        if (valeur &&
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

filtrerLaSaisie('search', /[A-Za-z0-9 -]/);

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


