CREATE EXTENSION postgis;

-- Table: public.joueurs

-- DROP TABLE IF EXISTS public.joueurs;

CREATE TABLE IF NOT EXISTS public.joueurs
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    pseudo character varying(50) COLLATE pg_catalog."default" NOT NULL,
    mail character varying(100) COLLATE pg_catalog."default" NOT NULL,
    password character varying(255) COLLATE pg_catalog."default" NOT NULL,
    score time without time zone,
    highscore time without time zone,
    CONSTRAINT joueurs_pkey PRIMARY KEY (id),
    CONSTRAINT joueurs_mail_key UNIQUE (mail)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.joueurs
    OWNER to postgres;


-- Table: public.objet

-- DROP TABLE IF EXISTS public.objet;

CREATE TABLE IF NOT EXISTS public.objet
(
    id integer NOT NULL,
    nom_objet character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "position" geometry,
    zoom integer,
    depart boolean DEFAULT false,
    fin boolean,
    type character varying COLLATE pg_catalog."default",
    block integer,
    ajout integer[],
    code character varying(4) COLLATE pg_catalog."default",
    description character varying COLLATE pg_catalog."default",
    indice character varying COLLATE pg_catalog."default",
    CONSTRAINT objet_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.objet
    OWNER to postgres;


TRUNCATE objet;
INSERT INTO objet (id, nom_objet, position, zoom, depart, fin, type, block, ajout, code, description, indice)
VALUES (1,'debut',ST_GeomFromText('POINT(2.3522 48.8566)', 4326),13,TRUE,FALSE,'HISTOIRE',NULL,'{2,3,4,5}',NULL,'Bienvenue à Drive to Escape ! Le trophée de F1 a été volé il y a quelques jours. Le gala de remise des prix a lieu aujourd’hui et tu es chargé de ramener le trophée à Jeddah le plus vite possible. Pour commencer ta mission, rends-toi chez le dernier champion du monde de Formule 1.
','Rocher, yacht et luxe'),

(2,'verstappen',ST_GeomFromText('POINT(7.429 43.737)', 4326),13,FALSE,FALSE,'HISTOIRE',NULL,NULL,NULL,'Hallo ! On m’a volé le trophée il y a quelques jours… Je n’ai aucune idée de qui a fait ça, mais j’ai des soupçons sur mon coéquipier. Je veux bien te prêter mon jet pour y aller, mais avant cela, tu dois te rendre dans mon pays d’origine. Là-bas, tu devras répondre à une question.','Je suis sous la mer'),

(3,'jet',ST_GeomFromText('POINT(7.422 43.741)', 4326),13,FALSE,FALSE,'DEBLOQUANT',4,NULL,NULL,'','Dia de los muertos'),

(4,'redbull',ST_GeomFromText('POINT(4.541 52.389)', 4326),13,FALSE,FALSE,'CODE',NULL,NULL,2016,'En quelle année Verstappen remporte-t-il son premier GP ?','Il rejoint la F1 en 2015'),

(5,'Atterrissage',ST_GeomFromText('POINT(-99.091 19.402)', 4326),13,FALSE,FALSE,'BLOQUE',3,'{6,7,8}',NULL,'',''),

(6,'Checo',ST_GeomFromText('POINT(-99.091 19.402)', 4326),13,FALSE,FALSE,'HISTOIRE',NULL,NULL,NULL,'Hola ! Désolé, je n’ai pas le trophée… j’aurais bien aimé pourtant. Je pense que Lewis sait où il se trouve : il l’a quand même gardé 7 fois chez lui.','Le premier GP de l’histoire a eu lieu chez moi '),

(7,'Hamilton',ST_GeomFromText('POINT(-1.017 52.072)', 4326),13,FALSE,FALSE,'BLOQUE',8,'{9,10,11}',NULL,'Hey ! On m’a volé mon chien, je ne te dirai rien tant qu’on ne me l’aura pas rendu. C’est le patron de Ferrari qui me l’a volé pour que je signe chez eux, j’en suis sûr !','Curva Parabolica'),

(8,'Roscoe',ST_GeomFromText('POINT(9.290 45.621)', 4326),13,FALSE,FALSE,'DEBLOQUANT',NULL,NULL,NULL,'Wouf !', ''),

(9,'Hamilton',ST_GeomFromText('POINT(-1.017 52.072)', 4326),13,FALSE,FALSE,'HISTOIRE',NULL,NULL,NULL,'Thanks mate ! Je ne peux pas te rendre le trophée, mais je sais qu’Ocon l’a volé… Il l’a ramené sur le circuit de sa première victoire !','Perle du Danube'),

(10,'Ocon',ST_GeomFromText('POINT(19.250 47.583)', 4326),13,FALSE,FALSE,'BLOQUE',11,'{12,13}',NULL,'Désolé… Je voulais savoir ce que ça faisait d’être champion du monde. Je veux bien te dire où j’ai caché le trophée, mais il faut me ramener le casque de mon meilleur ami.','Tabarnak'),

(11,'casque',ST_GeomFromText('POINT(-73.525 45.506)', 4326),13,FALSE,FALSE,'DEBLOQUANT',NULL,NULL,NULL,'',''),

(12,'Ocon',ST_GeomFromText('POINT(19.250 47.583)', 4326),13,FALSE,FALSE,'HISTOIRE',NULL,NULL,NULL,'Bien joué… Le trophée est à Singapour, mais il est bloqué par un code.',''),

(13,'f1',ST_GeomFromText('POINT(103.859 1.291)', 4326),13,FALSE,FALSE,'CODE',NULL,'{14,15}',1950,'En quelle année a lieu le premier GP de l’histoire de la F1 ?','Farina remporte ce GP'),

(14,'coupe',ST_GeomFromText('POINT(103.859 1.291)', 4326),13,FALSE,FALSE,'DEBLOQUANT',NULL,NULL,NULL,'Bravo ! Il faut ramener le trophée au gala maintenant !',''),

(15,'vitrine',ST_GeomFromText('POINT(39.104 21.632)', 4326),13,FALSE,FALSE,'BLOQUE',14,'{16}',NULL,NULL,NULL),

(16,'fin',ST_GeomFromText('POINT(39.104 21.632)', 4326),13,FALSE,TRUE,'HISTOIRE',NULL,NULL,NULL,'Bravo, tu as réussi à sauver le gala !',NULL)



