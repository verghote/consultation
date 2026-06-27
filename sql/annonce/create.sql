SET default_storage_engine = InnoDb;


drop table if exists  annonce;

create table  annonce
(
    id          smallint unsigned not null auto_increment primary key ,
    nom         varchar(100)      not null,
    description text              not null,
    date        date              not null,
    url         varchar(255)      null,
    affiche     varchar(30)       null
);

