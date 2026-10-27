# Modifications realisees

## Simplification de l'architecture

Les dossiers `src/Service` et `src/Validateur` ont ete supprimes pour simplifier la lecture du projet par des etudiants debutants.

## Mise a jour de l'autoload Composer

Le fichier `composer.json` a ete modifie pour retirer les espaces de noms suivants :

- `Validateur\\`
- `Service\\`

Cela permet de garder un autoload coherent avec la nouvelle structure du projet.

## Deplacement des controles dans les controleurs

Les verifications auparavant centralisees dans `CoureurValidateur` ont ete dupliquees dans les controleurs AJAX suivants :

- `public/coureur/getbycategorie/ajax/getlescoureurs.php`
- `public/coureur/getbylicence/ajax/getbylicence.php`
- `public/coureur/getbyname/ajax/getbyname.php`
- `public/coureur/recherchemultiple/ajax/getlescoureurs.php`

Ces controleurs appellent maintenant directement `ClasseMetier\Coureur`.

## Controles maintenant faits directement dans les controleurs

Les regles suivantes sont desormais visibles directement dans les scripts qui traitent la requete :

- validation du format d'un identifiant de categorie
- validation du format d'un numero de licence
- validation du format du sexe
- validation du format d'un identifiant de club
- validation du terme de recherche
- verification de l'existence d'un coureur pour la recherche par licence

## Interet pedagogique de cette simplification

Pour un debutant, cette version est plus facile a suivre :

- le controleur recupere les parametres
- il verifie directement les donnees
- il appelle la classe metier
- il renvoie la reponse JSON

Le cheminement d'une requete est donc plus court et plus visible.

# Interet des couches Service et Validateur

## Couche Validateur

Une couche `Validateur` permet de centraliser les regles de controle des donnees.

Ses avantages sont :

- eviter de dupliquer les expressions regulieres et messages d'erreur
- garantir des validations identiques partout dans l'application
- faciliter la maintenance quand une regle change
- rendre les controleurs plus courts et plus lisibles

## Couche Service

Une couche `Service` permet d'isoler la logique applicative entre le controleur et la classe metier.

Ses avantages sont :

- separer la gestion HTTP de la logique fonctionnelle
- centraliser les enchainements : validation, verification metier, appel aux donnees
- reutiliser les memes traitements depuis plusieurs controleurs
- limiter le couplage direct entre les points d'entree HTTP et les classes metier

## Pourquoi ces couches restent utiles

Dans un projet reel ou plus grand, ces couches deviennent tres utiles des que :

- plusieurs controleurs utilisent les memes regles
- les traitements metier deviennent plus complexes
- il faut tester la logique sans passer par l'interface HTTP
- l'application evolue avec davantage d'ecrans, d'API ou de cas d'usage

## Conclusion

Supprimer `Service` et `Validateur` rend ici le projet plus direct pour l'apprentissage initial.

En revanche, conserver ces couches est generalement preferable dans une application plus riche, car elles ameliorent la reutilisation, la maintenabilite et la clarte des responsabilites.
