"use strict";

// -----------------------------------------------------------------------------------
// Imports
// -----------------------------------------------------------------------------------

import {ucWord} from '/composant/fonction/chaine.js';
import {getData} from '/composant/fonction/page.js';


// -----------------------------------------------------------------------------------
// Données
// -----------------------------------------------------------------------------------

const lesCoureurs = getData('lesCoureurs');


// -----------------------------------------------------------------------------------
// Éléments DOM
// -----------------------------------------------------------------------------------

const lesLignes = document.getElementById('lesLignes');
const modeleLigneCoureur = document.getElementById('modeleLigneCoureur');


// -----------------------------------------------------------------------------------
// Fonctions
// -----------------------------------------------------------------------------------

/**
 * Crée une ligne du tableau des coureurs à partir du template HTML.
 *
 * @param {Object} coureur
 * @returns {DocumentFragment}
 */
export function creerLigneCoureur(coureur) {

    // -------------------------------------------------------------------------------
    // Clone du template
    // -------------------------------------------------------------------------------

    const fragment = modeleLigneCoureur.content.cloneNode(true);

    // -------------------------------------------------------------------------------
    // Récupération des éléments
    // -------------------------------------------------------------------------------

    const tr = fragment.querySelector('tr');
    const celluleLicence = fragment.querySelector('.coureur-licence');
    const celluleNom = fragment.querySelector('.coureur-nom');
    const celluleSexe = fragment.querySelector('.coureur-sexe');
    const celluleDate = fragment.querySelector('.coureur-date');
    const celluleCategorie = fragment.querySelector('.coureur-categorie');
    const celluleClub = fragment.querySelector('.coureur-club');

    // -------------------------------------------------------------------------------
    // Formatage
    // -------------------------------------------------------------------------------

    const nomFormate = ucWord(coureur.nomPrenom);
    const clubFormate = ucWord(coureur.nomClub);

    // -------------------------------------------------------------------------------
    // Remplissage des cellules
    // -------------------------------------------------------------------------------

    celluleLicence.textContent = coureur.licence;
    celluleNom.textContent = nomFormate;
    celluleSexe.textContent = coureur.sexe;
    celluleDate.textContent = coureur.dateNaissanceFr;
    celluleCategorie.textContent = coureur.idCategorie;
    celluleClub.textContent = clubFormate;

    // -------------------------------------------------------------------------------
    // Informations complémentaires pour l'affichage mobile
    // -------------------------------------------------------------------------------

    tr.dataset.details =
        `👤 ${nomFormate}\n` +
        `🪪 Licence : ${coureur.licence}\n` +
        `👫 Sexe : ${coureur.sexe} | Cat. : ${coureur.idCategorie}\n` +
        `🎂 Né(e) le : ${coureur.dateNaissanceFr}\n` +
        `🏆 Club : ${clubFormate}`;
    return fragment;
}

// -----------------------------------------------------------------------------------
// Affichage des lignes
// -----------------------------------------------------------------------------------

for (const coureur of lesCoureurs) {
    lesLignes.appendChild(
        creerLigneCoureur(coureur)
    );
}
