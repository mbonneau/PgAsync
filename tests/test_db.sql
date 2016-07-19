CREATE TABLE thing (
  id SERIAL,
  thing_type varchar(50),
  thing_description TEXT,
  thing_cost decimal(10,4),
  thing_in_stock bool
);

INSERT INTO thing(thing_type, thing_description, thing_cost, thing_in_stock)
    VALUES('pen', NULL, 50.23, 'f');
INSERT INTO thing(thing_type, thing_description, thing_cost, thing_in_stock)
    VALUES('pencil', 'something you write with', 27.50, null);
INSERT INTO thing(thing_type, thing_description, thing_cost, thing_in_stock)
    VALUES('marker', NULL, 50.23, 't');

CREATE TABLE numero
(
  numero_id SERIAL, --bigint NOT NULL DEFAULT nextval('numeros_id_numero_seq'::regclass),
  empresa_id bigint NOT NULL,
  tg_username character varying(255),
  tg_phone character varying(255) NOT NULL,
  tg_photo character varying(255),
  tg_lastseen character varying(255),
  tg_printname character varying(255),
  tg_firstname character varying(64),
  tg_lastname character varying(64),
  tg_peer character varying(64),
  tg_id character varying(64),
  profile character varying(255) NOT NULL,
  st_ativo boolean NOT NULL DEFAULT true,
  st_instalado boolean NOT NULL DEFAULT false,
  CONSTRAINT numeros_pkey PRIMARY KEY (numero_id)
  -- ,
--   CONSTRAINT numero_empresa_id_fkey FOREIGN KEY (empresa_id)
--       REFERENCES empresas (id_empresa) MATCH SIMPLE
--       ON UPDATE CASCADE ON DELETE CASCADE
);


INSERT INTO numero (numero_id, empresa_id, tg_username, tg_phone, tg_photo, tg_lastseen, tg_printname, tg_firstname, tg_lastname, tg_peer, tg_id, profile, st_ativo, st_instalado) VALUES (61, 5, NULL, '5584992227303', NULL, NULL, NULL, 'Contauditoria', NULL, NULL, NULL, 'contaauditoria', true, true);
INSERT INTO numero (numero_id, empresa_id, tg_username, tg_phone, tg_photo, tg_lastseen, tg_printname, tg_firstname, tg_lastname, tg_peer, tg_id, profile, st_ativo, st_instalado) VALUES (63, 12, NULL, '5511940417680', NULL, NULL, NULL, 'Bya Eventos', NULL, NULL, NULL, 'byaeventos', true, true);
INSERT INTO numero (numero_id, empresa_id, tg_username, tg_phone, tg_photo, tg_lastseen, tg_printname, tg_firstname, tg_lastname, tg_peer, tg_id, profile, st_ativo, st_instalado) VALUES (64, 16, '', '556292815499', '', '', '', '', '', '', '', 'autoeste', true, true);