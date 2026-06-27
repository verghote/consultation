"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {creerLigneCoureur} from "../coureur.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesCategories, lesClubs */
// lesCatégories : Tableau permettant de remplir la zone de liuste des catégories
// lesClubs : Tableau permettant de remplir la zone de liste des clubs

// récupération des éléments de l'interface
const idCategorie = document.getElementById('idCategorie');
const idClub = document.getElementById('idClub');
const sexe = document.getElementById('sexe');
const nb = document.getElementById('nb');
const lesLignes = document.getElementById('lesLignes');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

idCategorie.onchange = filtrer;
idClub.onchange = filtrer;
sexe.onchange = filtrer;

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(lesCoureurs) {
    lesLignes.innerHTML = '';
    nb.innerText = lesCoureurs.length;

    for (const coureur of lesCoureurs) {
        lesLignes.appendChild(creerLigneCoureur(coureur));
    }
}


function filtrer() {
    appelAjax({
        url: 'ajax/getlescoureurs.php',
        data: {
            idCategorie: idCategorie.value,
            idClub: idClub.value,
            sexe: sexe.value
        },
        success: afficher
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
// alimentation de la zone de liste des catégories
for (const element of lesCategories) {
    idCategorie.appendChild(new Option(element.nom, element.id));
}

// alimentation de la zone de liste des clubs
for (const element of lesClubs) {
    idClub.add(new Option(element.nom, element.id));
}

filtrer(); // Lancement de la recherche initiale