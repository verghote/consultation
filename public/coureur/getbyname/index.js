"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getAge} from '/composant/fonction/date.js';
import {appelAjax} from "/composant/fonction/ajax.js";
import {effacerSousLeChamp} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Données injectées par le contrôleur PHP
const lesCoureurs = getData('lesCoureurs');

// Récupération des éléments de l'interface
const search = document.getElementById('search');
const licence = document.getElementById('licence');
const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const nomClub = document.getElementById('nomClub');
const idCategorie = document.getElementById('idCategorie');
const age = document.getElementById('age');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.onfocus = function () {
    // Efface les valeurs de la fiche
    document.querySelectorAll('output').forEach(element => element.textContent = '');
    // Efface le champ de recherche
    this.value = '';
    // Efface un éventuel message d'erreur
    effacerSousLeChamp('search');
};

search.onblur = function () {
    effacerSousLeChamp('search');
};

search.oninput = function () {
    effacerSousLeChamp('search');
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function rechercher(licence) {
    appelAjax({
        url: 'ajax/getbylicence.php',
        data: {
            licence: licence
        },
        success: afficher
    });
}

function afficher(coureur) {
    licence.textContent = coureur.licence;
    nom.textContent = coureur.nom;
    prenom.textContent = coureur.prenom;
    sexe.textContent = coureur.sexe;
    dateNaissance.textContent = coureur.dateNaissanceFr;
    nomClub.textContent = coureur.nomClub;
    idCategorie.textContent = coureur.idCategorie;
    age.textContent = getAge(coureur.dateNaissanceFr) + ' ans';
    // Retire le focus du champ de recherche
    search.blur();
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// initialisation du composant autoComplete.js
const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,
    data: {
        src: lesCoureurs,
        cache : false,
        keys: ["nomPrenom"],
    },
    searchEngine: "loose",
    resultsList: {
        maxResults: 10,
        noResults: true,
        element: (list, data) => {
            if (!data.results.length) {
                const message = document.createElement("li");

                message.textContent = "Aucun coureur trouvé.";
                message.setAttribute("class", "no_result");

                list.appendChild(message);
            }
        }
    },
    resultItem: {
        highlight: true,
        element: (item, data) => item.innerHTML = `<span>${data.match}</span>`
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                search.value = selection.nomPrenom;
                rechercher(selection.licence);
            }
        }
    }
});