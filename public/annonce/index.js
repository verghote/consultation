"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesCartes = document.getElementById('lesCartes');
const filtreMois = document.getElementById('filtreMois');
const courseTemplate = document.getElementById('courseTemplate');
const detailDialog = document.getElementById('detailDialog');
const epreuve = document.getElementById('epreuve');
const description = document.getElementById('description');
const fermerDetail = document.getElementById('fermerDetail');

// Récupération des données injectées par Vue
const lesAnnonces = getData('lesAnnonces');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

filtreMois.onchange = afficher;

fermerDetail.onclick = () => detailDialog.close();

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------


function creerCarte(element) {
    const fragment = courseTemplate.content.cloneNode(true);

    const carte = fragment.querySelector('.course-carte');
    const dateNode = fragment.querySelector('.course-date');
    const nomNode = fragment.querySelector('.course-nom');
    const imageContainer = fragment.querySelector('.course-image-container');
    const pied = fragment.querySelector('.course-pied');

    // Date
    const date = new Date(element.date);
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    dateNode.textContent = date.toLocaleString('fr-FR', options);

    // Nom de l'épreuve
    nomNode.textContent = element.nom;

    // Lien vers le site de l'épreuve
    if (element.url) {
        const lien = document.createElement('a');

        lien.className = 'course-action';
        lien.title = 'Cliquez ici pour ouvrir un onglet vers le site';
        lien.href = element.url;
        lien.innerText = 'Site de l’épreuve';
        lien.target = 'courseannoncee';
        lien.rel = 'noopener';

        pied.appendChild(lien);
    }

    // Bouton de détail
    const bouton = document.createElement('button');

    bouton.className = 'course-action';
    bouton.type = 'button';
    bouton.innerText = 'Détail de l’épreuve';

    bouton.onclick = () => {
        description.innerHTML = element.description;
        epreuve.innerHTML = element.nom;
        detailDialog.showModal();
    };

    pied.appendChild(bouton);

    // Affiche
    if (element.affiche) {
        const image = document.createElement('img');

        image.src = '/data/annonce/' + element.affiche;
        image.alt = 'Affiche';

        imageContainer.appendChild(image);
    }

    return carte;
}

function afficher() {
    lesCartes.innerHTML = '';

    const moisChoisi = Number(filtreMois.value);

    for (const element of lesAnnonces) {
        const date = new Date(element.date);

        if (moisChoisi !== 0 && (date.getMonth() + 1) !== moisChoisi) {
            continue;
        }

        lesCartes.appendChild(creerCarte(element));
    }
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

for (let i = 1; i <= 12; i++) {
    const option = document.createElement('option');
    option.value = String(i);
    option.textContent = new Date(0, i - 1).toLocaleDateString('fr-FR', {month: 'long'});

    const existe = lesAnnonces.some(annonce => {
        const date = new Date(annonce.date);
        return date.getMonth() === i - 1;
    });

    if (!existe) {
        option.disabled = true;
    }

    filtreMois.appendChild(option);
}

afficher();

