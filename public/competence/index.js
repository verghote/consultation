"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {activerTri } from "/composant/fonction/tableau.js";
import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const idBloc = document.getElementById('idBloc');
const idDomaine = document.getElementById('idDomaine');
const lesLignes = document.getElementById('lesLignes');

// Récupération des données injectées par Vue
const lesBlocs = getData('lesBlocs');

// il est nécessaire de conserver les compétences dans un tableau afin de pouvoir appliquer le tri sur l'interface
let lesCompetences = [];

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// gestionnaire d'événement sur les zones de liste idBloc
idBloc.onchange = function() {
    getLesDomaines(this.value);
};

// gestionnaire d'événement sur la zone de liste idDomaine
idDomaine.onchange = function() {
    if (this.value === '') {
        getLesCompetencesDuBloc(idBloc.value);
    }
    else {
        getLesCompetencesDuDomaine(idBloc.value, this.value);
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function getLesDomaines(idBloc) {
    appelAjax({
        url: 'ajax/getlesdomaines.php',
        data: {
            idBloc: idBloc,
        },
        success: (data) => {
            // réinitialisation de la liste des domaines
            idDomaine.innerHTML = '';
            // ajout de l'option 'Tous les domaines'
            idDomaine.add(new Option('Tous les domaines', ''));
            // ajout des domaines contenu dans la réponse data
            for (const domaine of data) {
                idDomaine.add(new Option(domaine.libelle, domaine.idDomaine));
            }
            // Chargement initial : toutes les compétences du bloc
            getLesCompetencesDuBloc(idBloc);
        }
    });
}

function getLesCompetencesDuBloc(idBloc) {
    appelAjax({
        url: 'ajax/getlescompetencesdubloc.php',
        data: {idBloc: idBloc },
        success: data => {
            lesCompetences = data;
            afficher();
        }
    });
}

function getLesCompetencesDuDomaine(idBloc, idDomaine) {
    appelAjax({
        url: 'ajax/getlescompetencesdudomaine.php',
        data: {
            idBloc: idBloc,
            idDomaine: idDomaine
        },
        success: data => {
            lesCompetences = data;
            afficher();
        }
    });
}

function afficher() {
    lesLignes.replaceChildren();
    for (const competence of lesCompetences) {
        const tr = lesLignes.insertRow();
        tr.style.verticalAlign = 'middle';

        // Colonne : code
        tr.insertCell().innerText = competence.code;

        // Colonne Libellé
        tr.insertCell().innerText = competence.libelle;
    }
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
// remplir la zone de liste des blocs
for (const bloc of lesBlocs) {
    idBloc.appendChild(new Option(bloc.libelle, bloc.id));
}

// Lancer la fonction getLesdomaines() avec l’id du bloc sélectionné
getLesDomaines(idBloc.value);

// activer le tri sur le tableau
activerTri({
    idTable: "leTableau",
    getData: () => lesCompetences,
    afficher: afficher
});