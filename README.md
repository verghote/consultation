# Consultation et recherche

Application Web PHP de consultation de données sportives et d'illustration de
différentes méthodes de recherche. Elle présente notamment des coureurs, leurs
clubs et catégories, des annonces de courses, ainsi que des projets associés
aux compétences du référentiel BTS SIO.

## Objectifs

- Consulter des données issues de tables relationnelles.
- Séparer l'accès aux données, la logique de présentation et les interfaces.
- Afficher des listes sous forme de tableaux ou de cartes.
- Comparer plusieurs modes de recherche : identifiant, autocomplétion,
  catégorie ou combinaison de critères.
- Mettre à jour les résultats côté navigateur au moyen de requêtes AJAX/JSON.

## Fonctionnalités

| URL | Fonction |
| --- | --- |
| `/` | Présentation de l'activité et de ses objectifs. |
| `/categorie` | Catégories sportives, tranches d'âge et périodes de naissance ; export PDF. |
| `/coureur` | Liste des coureurs avec tableau et outils de consultation. |
| `/club` | Clubs, logos disponibles et nombre de licenciés. |
| `/annonce` | Annonces de courses à venir, filtrables par mois, avec affiche et détails. |
| `/projet` | Projets et compétences associées. |
| `/competenceprojet` | Sélection d'un projet et consultation de ses compétences. |
| `/competence` | Compétences filtrables par bloc et domaine. |
| `/coureur/getbylicence` | Recherche d'un coureur par numéro de licence. |
| `/coureur/getbyname` | Recherche par nom avec autocomplétion à partir de données chargées par la page. |
| `/coureur/getbyname2` | Variante d'autocomplétion avec recherche côté serveur. |
| `/coureur/getbycategorie` | Liste des coureurs d'une catégorie. |
| `/coureur/recherchemultiple` | Recherche par sexe, club et/ou catégorie. |

Les chemins correspondent aux répertoires de `public/`. Apache utilise
`index.php` comme page par défaut dans chacun de ces répertoires.

## Technologies et prérequis

- PHP **8.3 ou supérieur** (le code utilise notamment des constantes de classe typées).
- MySQL ou MariaDB, avec le moteur InnoDB et le pilote PHP `pdo_mysql`.
- Extensions PHP `PDO`, `intl` et `curl` (déclarées dans `composer.json`).
- Composer pour générer l'autoloader PSR-4.
- Apache avec `mod_rewrite` et l'autorisation de lire les fichiers `.htaccess`.
- Navigateur récent prenant en charge les modules JavaScript et `fetch`.

L'autocomplétion de la page `/coureur/getbyname2` charge également une
dépendance depuis jsDelivr ; cette page nécessite donc un accès réseau au CDN.
Les autres bibliothèques utilisées par l'interface sont principalement
distribuées dans `public/composant/`.

## Installation

1. Installer PHP, Apache, MySQL/MariaDB et Composer, puis placer le projet sur
   le serveur.
2. Configurer Apache pour que le **DocumentRoot pointe vers le répertoire
   `public/`**. Le reste du projet (configuration, code PHP, scripts SQL et
   journaux) ne doit pas être exposé directement au navigateur. Activer
   `mod_rewrite` et autoriser les règles `.htaccess`.
3. Installer l'autoloader depuis la racine du projet :

   ```sh
   composer install
   ```

4. Configurer l'accès MySQL dans `config/Database.php` : hôte, nom de base,
   utilisateur, mot de passe et port. Utiliser des identifiants propres à
   l'environnement ; ne pas réutiliser ni publier de mot de passe réel.
5. Créer et alimenter la base `consultation` en important les scripts SQL dans
   cet ordre :
   - `sql/create database.sql` ;
   - `sql/coureur/create.sql`, `sql/annonce/create.sql` et
     `sql/projet/create.sql` ;
   - les scripts de données correspondants `sql/coureur/insert.sql`,
     `sql/annonce/insert.sql` et `sql/projet/insert.sql`.
6. Vérifier la configuration et les droits du compte MySQL. `sql/user.sql`
   contient un exemple de création d'un compte limité à la lecture ; adapter
   les identifiants du script et ceux de l'application avant de l'utiliser.
7. Ouvrir le site à la racine du virtual host et vérifier les pages ainsi que
   les requêtes AJAX.

> **Attention :** les scripts SQL de création commencent par supprimer la base
> ou les tables qu'ils recréent. Ils sont destinés à une initialisation ou à
> une reconstruction complète, pas à une mise à jour d'une base de production.
> La création du déclencheur dans `sql/coureur/create.sql` peut également
> nécessiter l'adaptation du délimiteur selon l'outil d'import utilisé.

### Configuration sensible à la casse

Le chargeur de configuration demande le fichier `database.php`, tandis que le
fichier présent dans le projet est nommé `Database.php`. Cela peut fonctionner
sur un environnement Windows sans distinction de casse, mais échouer sur un
système de fichiers Linux. Avant un déploiement Linux, mettre le nom du fichier
et celui demandé par le chargeur en cohérence.

## Architecture du projet

```text
bootstrap/       Initialisation PHP, session, chemins, autoloader et gestion d'erreurs
config/          Configuration de la base et définition du menu
documentation/   Documents PDF de référence sur la consultation et la recherche
maintenance/     Contrôles CLI de structure de base et de fichiers associés aux données
public/          Racine Web : pages, ressources statiques et points d'accès AJAX
  composant/     Bibliothèques et fonctions JavaScript/CSS partagées
  data/          Logos de clubs et affiches d'annonces
  erreur/        Pages d'erreur personnalisées
sql/             Création et alimentation des tables et de la base
src/ClasseMetier/ Accès aux données métier : coureur, club, catégorie, annonce, projet, compétence
src/ClasseTechnique/ Outils partagés : PDO, configuration, page, menu, requêtes et réponses
view/            Gabarits HTML communs et interface des messages système
vendor/          Autoloader et fichiers Composer installés
```

### Fonctionnement d'une page

Les contrôleurs PHP de `public/` initialisent l'application via
`bootstrap/bootstrap.php`, récupèrent les données nécessaires auprès des
classes de `src/ClasseMetier/`, puis construisent une instance de
`ClasseTechnique\Page`. `InterfaceHtml` assemble le gabarit commun, le menu,
les ressources et le contenu spécifique.

Pour une page nommée `index`, le système recherche automatiquement les fichiers
`index.html`, `index.js` et éventuellement `index.css` dans le même répertoire.
Les données transmises par PHP sont encodées en JSON pour être lues par le
JavaScript de la page. Les données métier sont consultées via `Select`, qui
centralise l'exécution de requêtes PDO et l'accès à une ligne, plusieurs lignes
ou une valeur.

Les interactions dynamiques appellent des scripts PHP placés dans des
répertoires `ajax/`. Ils valident les paramètres et renvoient du JSON à l'aide
de `ReponseJson`. Les helpers partagés, notamment `public/composant/fonction/ajax.js`,
gèrent ces appels côté navigateur.

## Modèle de données

- **Coureurs** : licence, identité, sexe, date de naissance, catégorie et club.
  Le schéma SQL inclut un déclencheur qui valide les principales données et
  détermine la catégorie à partir de l'âge pour la saison en cours.
- **Catégories** : identifiant, libellé et bornes d'âge ; les périodes de
  naissance sont calculées pour la saison affichée.
- **Clubs** : identifiant, nom et nom de fichier du logo.
- **Annonces** : nom, description, date, URL et affiche ; l'écran public ne
  retient que les dates à partir du jour courant.
- **Compétences et projets** : blocs, domaines, compétences, projets et table
  d'association entre projets et compétences.

Les fichiers d'images référencés dans la base sont conservés dans
`public/data/club/` et `public/data/annonce/`.

## Maintenance

Les scripts du répertoire `maintenance/` sont réservés à la ligne de commande
et doivent être exécutés avec PHP CLI, jamais exposés comme pages Web :

Depuis la racine du projet, lancer chaque contrôle depuis son propre
répertoire. Les chemins de fichiers de la configuration du contrôle des
images sont relatifs au répertoire courant :

```sh
cd "maintenance/Contrôle de la structure de données"
php controler.php

cd "../Contrôle des fichiers de données"
php controler.php
```

- `Contrôle de la structure de données/controler.php` compare les tables et
  colonnes réelles à `structure.json`.
- `Contrôle des fichiers de données/controler.php` compare les fichiers
  référencés en base aux images présentes sur disque et écrit les fichiers
  orphelins dans `orphelins.json`.
- `Contrôle des fichiers de données/supprimer_orphelin.php` propose de
  supprimer les fichiers listés dans ce JSON après confirmation. Cette
  suppression est irréversible : contrôler le rapport avant de confirmer.

Chaque contrôle possède sa propre configuration de connexion à la base dans
son fichier JSON ; la mettre en cohérence avec l'environnement avant exécution.

## Sécurité et exploitation

- Les pages ayant des requêtes sensibles peuvent activer un jeton CSRF via
  `Page::avecJeton()`. Le helper AJAX transmet le jeton dans l'en-tête
  `X-CSRF-Token`.
- Les règles Apache interdisent l'indexation des répertoires et limitent
  l'accès direct aux scripts sous `ajax/` aux requêtes comportant
  `X-Requested-With: XMLHttpRequest`.
- `public/composant/.htaccess` interdit l'exécution de scripts PHP dans le
  répertoire des ressources statiques.
- Les exceptions techniques sont journalisées et présentent un message
  générique à l'utilisateur ; les pages 401, 403 et 404 disposent de vues
  dédiées.
- Garder `public/` comme seule racine Web et protéger les fichiers de
  configuration, en particulier ceux qui contiennent des identifiants de base.
