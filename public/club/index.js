"use strict"; // Active le mode strict pour éviter les erreurs silencieuses

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesClubs = getData('lesClubs');
const repertoire = '/data/club/';

const lesCartes = document.getElementById('lesCartes');
const carteClubTemplate = document.getElementById('carteClubTemplate');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------


/**
 * Crée une carte pour afficher les informations d'un club
 * @param {Object} club - Données du club
 * @returns {HTMLDivElement}
 */
function creerCarte(club) {

// clonage du template
    const carte = carteClubTemplate.content.cloneNode(true);

// récupération des éléments du template
    const entete = carte.querySelector('.enteteClub');
    const logo = carte.querySelector('.logoClub');
    const pied = carte.querySelector('.nbLicencies');

// alimentation des données
    entete.textContent = club.nom;
    pied.textContent = `${club.nb} licenciés`;
    if (club.present) {
        logo.src = repertoire + club.fichier;
        logo.alt = `${club.nom} logo`;
    } else {
        logo.remove();
    }

    return carte;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

for (const club of lesClubs) {
    lesCartes.appendChild(creerCarte(club));
}
