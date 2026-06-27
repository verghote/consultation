"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {genererMessage} from "/composant/fonction/afficher.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/*global lesAnnonces */
const lesCartes = document.getElementById('lesCartes');
const filtreMois = document.getElementById('filtreMois');
const modeleAnnonce = document.getElementById('modeleAnnonce');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

filtreMois.onchange = afficher;


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Création d'une carte à partir du template HTML
 */
function creerCarte(element) {
    const fragment = modeleAnnonce.content.cloneNode(true);

    const entete = fragment.querySelector('.enteteAnnonce');
    const corps = fragment.querySelector('.corpsAnnonce');
    const divBoutons = fragment.querySelector('.boutonsAction');
    const divImg = fragment.querySelector('.image-container');

    // --- En-tête ---
    const date = new Date(element.date);
    const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
    entete.textContent = date.toLocaleString('fr-FR', options) + " - " + element.nom;

    // --- Boutons ---
    divBoutons.innerHTML = ''; // réinitialiser
    if (element.url) {
        const a = document.createElement('a');
        a.className = 'btn btn-sm btn-outline-primary text-end';
        a.href = element.url;
        a.innerText = 'Site de l\'épreuve';
        divBoutons.appendChild(a);
    }

    const btnDetail = document.createElement('button');
    btnDetail.className = 'btn btn-sm btn-outline-primary text-center m-2';
    btnDetail.innerText = 'Détail de l\'épreuve';
    btnDetail.onclick = () => {
        document.getElementById('description').innerHTML = element.description;
        document.getElementById('epreuve').textContent = element.nom;
        new bootstrap.Modal(document.getElementById('detail')).show();
    };
    divBoutons.appendChild(btnDetail);

    // --- Image ---
    divImg.innerHTML = '';
    if (element.affiche && element.present) {
        const img = document.createElement('img');
        img.src = "/data/annonce/" + element.affiche;
        img.alt = "Affiche";
        img.style.maxWidth = "100%";
        divImg.appendChild(img);
    }

    return fragment;
}

/**
 * Affichage des cartes avec filtre par mois
 */
function afficher() {
    lesCartes.innerHTML = '';
    lesCartes.style.display = 'grid';
    lesCartes.style.gridTemplateColumns = 'repeat(auto-fill, minmax(360px, 1fr))';
    lesCartes.style.gap = '1rem';

    const moisChoisi = filtreMois.value;

    for (const element of lesAnnonces) {
        const date = new Date(element.date);
        if (moisChoisi !== "" && date.getMonth() != moisChoisi) continue;

        lesCartes.appendChild(creerCarte(element));
    }
}




// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

if (lesAnnonces.length > 0) {
    afficher();
} else {
    const contenuCadreAnnonce = document.getElementById('contenuCadreAnnonce');
    contenuCadreAnnonce.innerHTML = genererMessage("Aucune annonce pour le moment.", "orange");
}

