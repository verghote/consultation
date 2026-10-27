"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {creerLigneCoureur} from "../coureur.js";
import {getData} from "/composant/fonction/page.js";
import {genererMessage} from "/composant/fonction/afficher.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Récupération des données injectées par Vue
const lesCategories = getData('lesCategories');
const lesClubs = getData('lesClubs');

// récupération des éléments de l'interface
const idCategorie = document.getElementById('idCategorie');
const idClub = document.getElementById('idClub');
const sexe = document.getElementById('sexe');

const btnRechercher = document.getElementById('btnRechercher');

const nb = document.getElementById('nb');
const lesLignes = document.getElementById('lesLignes');
const msg = document.getElementById('msg');


// -----------------------------------------------------------------------------------
// Procédure évènementielle
// -----------------------------------------------------------------------------------

btnRechercher.onclick = rechercher;


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(lesCoureurs) {

    lesLignes.replaceChildren();
    msg.innerText = '';
    nb.innerText = lesCoureurs.length;
    if (lesCoureurs.length === 0) {
        msg.innerHTML = genererMessage("Aucun coureur ne correspond aux critères de recherche sélectionnés.", 'orange');
    } else {
        for (const coureur of lesCoureurs) {
            lesLignes.appendChild(creerLigneCoureur(coureur));
        }
    }
}


/**
 * Lance la recherche.
 */
function rechercher() {

    // Suppression d'un éventuel ancien message
    msg.innerHTML = '';

    // Vérification qu'au moins un critère de recherche est sélectionné
    if (sexe.value === '*' &&
        idClub.value === '*' &&
        idCategorie.value === '*') {
        msg.innerHTML = genererMessage("Veuillez sélectionner au moins un critère de recherche avant de lancer la recherche.", 'orange');
        return;
    }

    // Appel Ajax pour récupérer les coureurs correspondant aux critères sélectionnés
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

    idCategorie.appendChild(
        new Option(
            element.nom,
            element.id
        )
    );
}


// alimentation de la zone de liste des clubs
for (const element of lesClubs) {

    idClub.appendChild(
        new Option(
            element.nom,
            element.id
        )
    );
}


