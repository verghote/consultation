"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {creerLigneCoureur} from "../coureur.js";
import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Récupération des données injectées par Vue
const lesCategories = getData('lesCategories');

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
    lesLignes.replaceChildren();
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
// Une catégorie sans licencié est visible mais ne peut pas être sélectionnée.
for (const element of lesCategories) {

    const option = new Option(
        element.nom,
        element.id
    );

    // Une catégorie sans licencié est visible
    // mais ne peut pas être sélectionnée.
    if (Number(element.nb) === 0) {
        option.disabled = true;
    }

    idCategorie.add(option);
}


// sélection de la première catégorie ayant des licenciés et récupération de la liste des coureurs
for (const element of lesCategories) {
    if (Number(element.nb) > 0) {
        idCategorie.value = element.id;
        getLesCoureurs(idCategorie.value);
        break;
    }
}
