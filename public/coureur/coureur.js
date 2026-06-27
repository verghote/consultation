import { getTd, getTr } from '/composant/fonction/trtd.js';
import { ucWord } from '/composant/fonction/chaine.js';

export function creerLigneCoureur(coureur) {
    return getTr([
        getTd(coureur.licence, { centrer: true }),
        getTd(ucWord(coureur.nomPrenom)),
        getTd(coureur.sexe, { centrer: true }),
        getTd(coureur.dateNaissanceFr, { centrer: true }),
        getTd(coureur.idCategorie, { centrer: true }),
        getTd(ucWord(coureur.nomClub)),
    ]);
}