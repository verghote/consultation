"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {creerLigneCoureur} from "../coureur.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesCategories */

const idCategorie = document.getElementById('idCategorie');
const lesLignes = document.getElementById('lesLignes');
const nb = document.getElementById('nb');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// gestionnaire d'évènement
idCategorie.onchange = () => {
        getLesCoureurs(idCategorie.value);
};

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

function getLesCoureurs(idCategorie) {
    appelAjax({
        url: 'ajax/getlescoureurs.php',
        data: {idCategorie: idCategorie},
        success: afficher
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// alimentation de la zone de liste des catégories
for (const element of lesCategories) {
    idCategorie.add(new Option(element.nom, element.id));
}

// sélection de la première catégorie
getLesCoureurs(idCategorie.value);
