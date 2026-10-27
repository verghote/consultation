SET default_storage_engine = InnoDb;

Set foreign_key_checks = 0;

use consultation;

drop table if exists categorie;
drop table if exists club;
drop table if exists coureur;


create table categorie
(
    id     char(3) primary key,
    nom    varchar(20) NOT NULL unique,
    ageMin tinyint     NOT NULL,
    ageMax tinyint     NOT NULL,
    check (ageMin < ageMax)
);

create table club
(
    id  char(6) primary key,
    nom varchar(70) NOT NULL unique,
    fichier varchar(100) null
);

create table coureur
(
    licence       char(7)      NOT NULL primary key,
    nom           varchar(30)  NOT NULL,
    prenom        varchar(30)  NOT NULL,
    sexe          char(1)      NOT NULL DEFAULT 'M',
    dateNaissance date         NOT NULL,
    idCategorie   char(3)      NOT NULL,
    idClub        varchar(6)   NOT NULL,
    unique (nom, prenom, dateNaissance),
    foreign KEY (idCategorie) references categorie (id) on update cascade,
    foreign KEY (idClub) references club (id)
);

-- le champ catégorie de la table coureur est un champ calculé

create trigger avantAjoutCoureur
    before insert
    on coureur
    for each row
begin
    -- Initialiser l'année de la saison en cours qui commence en septembre de l'année précédente
    declare annee int;
    set annee = year(curdate());
    if month(curdate()) >= 9 then
        set annee = annee + 1;
    end if;

    -- Vérification sur le numéro de licence
    if new.licence not regexp '^[0-9]{6,7}$' then
        signal sqlstate '45000' set message_text = 'Le numéro de licence ne respecte pas le format attendu.';
    end if;

    if exists(select 1 from coureur where licence = new.licence) then
        signal sqlstate '45000' set message_text = 'Ce numéro de licence est déjà attribué.';
    end if;

    -- Mise en forme et vérification sur le nom
    set new.nom = ucase(new.nom);

    if char_length(new.nom) not between 3 and 30 then
        signal sqlstate '45000' set message_text = 'Le nom doit comporter entre 3 et 30 caractères';
    end if;

    if new.nom not regexp '^[A-Z]( ?[A-Z])*$' then
        signal sqlstate '45000' set message_text = 'Le format du nom est invalide.';
    end if;

    -- mise en forme et vérification sur le prénom
    set new.prenom = ucase(new.prenom);

    if char_length(new.prenom) not between 3 and 30 then
        signal sqlstate '45000' set message_text = 'Le prénom doit comporter entre 3 et 30 caractères';
    end if;

    if new.prenom not regexp '^[A-Z]( ?[A-Z])*$' then
        signal sqlstate '45000' set message_text = 'Le format du prénom est invalide.';
    end if;

    -- Vérification de la date de naissance et détermination de la catégorie correspondante
    if new.dateNaissance not regexp '^(19|20)[0-9]{2}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$' then
        signal sqlstate '45000' set message_text = 'Le format de la date de naissance est invalide.';
    end if;

    select id
    into @idCategorie
    from categorie
    where annee - year(new.dateNaissance) between ageMin and AgeMax;
    if @idCategorie is not null then
        set new.idCategorie = @idCategorie;
    else
        signal sqlstate '45000' set message_text =
                'Aucune catégorie ne correspond à ce coureur, veuillez vérifier sa date de naissance';
    end if;

    -- vérification de l'unicité sur le triplet nom, prenom, dateNaissance
    if exists(select 1
              from coureur
              where nom = new.nom
                and prenom = new.prenom
                and dateNaissance = new.dateNaissance) then
        signal sqlstate '45000' set message_text = 'Un homonyme est déjà présent dans la table';
    end if;

    -- vérification de l'existence de l'identifiant du club
    if not exists(select 1 from club where id = new.idClub) then
        signal sqlstate '45000' set message_text = 'Ce club n''existe pas.';
    end if;
end;



Set foreign_key_checks = 1;
