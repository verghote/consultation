"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getAge} from '/composant/fonction/date.js';
import {effacerSousLeChamp} from "/composant/fonction/afficher.js";
import {appelAjax} from "/composant/fonction/ajax.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

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

// Efface le champ de saisie quand celui-ci est sélectionné ainsi que les balises output
search.onfocus = function() {
    // Efface le message d'erreur
    effacerSousLeChamp('search');
};

search.onblur = function() {
    // Efface le message d'erreur
    effacerSousLeChamp('search');
};

search.oninput = function() {
    // Efface le message d'erreur
    effacerSousLeChamp('search');
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(coureur) {
    licence.textContent = coureur.licence;
    prenom.textContent = coureur.prenom;
    sexe.textContent = coureur.sexe;
    dateNaissance.textContent = coureur.dateNaissanceFr;
    nomClub.textContent = coureur.nomClub;
    idCategorie.textContent = coureur.idCategorie;
    age.textContent = getAge(coureur.dateNaissanceFr) + ' ans';
    nom.textContent = coureur.nom;

// Retire le focus du champ de saisie
    search.blur();

}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,
    data: {
        src: async (query) => {
            const reponse = await appelAjax({
                url: "ajax/getbyname.php?search=" + query,
                method: "GET",
                dataType: "json"
            });
            console.log(`Résultat de la recherche " :`, reponse);
            return reponse ?? [];
        },

        keys: ["nomPrenom"],
        cache: false
    },
    searchEngine: "loose",
    resultsList: {
        maxResults: 10,
        noResults: true,
        element: (list, data) => {
            if (!data.results.length) {
                const message = document.createElement("li");
                message.textContent = "Aucun étudiant trouvé." + (data.query ? ` pour "${data.query}"` : "");
                // message.textContent = "Aucun coureur trouvé.";
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
                afficher(selection);
            }
        }
    }
});