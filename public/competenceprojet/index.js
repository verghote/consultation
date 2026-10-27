"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {activerTri } from "/composant/fonction/tableau.js";
import {genererMessage} from "/composant/fonction/afficher.js";
import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const idProjet = document.getElementById('idProjet');
const lesLignes = document.getElementById('lesLignes');
const msg = document.getElementById('msg');
const filtre = document.getElementById('filtre');

// Récupération des données injectées par Vue
const lesProjets = getData('lesProjets');

let lesCompetences = [];

// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

// Sur le changement de valeur sélectionnée dans la zone de liste, récupérer les compétences associées
idProjet.onchange = () => {
        chargerLesCompetencesDuProjet(idProjet.value);
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Récupération des compétences du projet sélectionné
 */
function chargerLesCompetencesDuProjet(idProjet) {
    msg.innerHTML = "";
    appelAjax({
        url: 'ajax/getlescompetences.php',
        data: { idProjet: idProjet },
        success: data => {
				lesCompetences = data;
                afficherLesCompetences(lesCompetences);
            }
    });
}

function afficherLesCompetences(lesCompetences) {
    // Effacer le tableau précédent
    lesLignes.innerHTML = "";

    for (const competence of lesCompetences) {
        const tr = lesLignes.insertRow();
        tr.style.verticalAlign = 'middle';

        // Colonne : Code
        tr.insertCell().innerText = competence.code;

        // Colonne : libelle
        tr.insertCell().innerText = competence.libelle;
    }
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Vérification de la présence de projet

if (lesProjets.length === 0) {
    msg.innerHTML = genererMessage("Aucun projet n'est actuellement enregistré", 'orange');

} else {

    // Alimenter la zone de liste des projets
    for (const projet of lesProjets) {
        idProjet.add(new Option(projet.nom, projet.id));
    }

// afficher les compétences du premier projet
    chargerLesCompetencesDuProjet(lesProjets[0].id);


// Activer le tri sur les colonnes
    activerTri({
        idTable: "leTableau",
        getData: () => lesCompetences,
        afficher: afficherLesCompetences,
        triInitial: {
            colonne: 'libelle',
            ordre: "asc"
        }
    });
}

