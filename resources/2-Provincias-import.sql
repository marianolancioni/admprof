--------------------------------------------------------
-- Archivo creado  - lunes-febrero-21-2022   
--------------------------------------------------------
--------------------------------------------------------
-- PROVINCIA
--------------------------------------------------------		
DELETE FROM public.provincia;
INSERT INTO public.provincia (id, provincia, estado) values (1,'Capital Federal','0');
INSERT INTO public.provincia (id, provincia, estado) values (2,'Buenos Aires ','0');
INSERT INTO public.provincia (id, provincia, estado) values (3,'Catamarca','0');
INSERT INTO public.provincia (id, provincia, estado) values (4,'Cordoba','0');
INSERT INTO public.provincia (id, provincia, estado) values (5,'Corrientes','0');
INSERT INTO public.provincia (id, provincia, estado) values (6,'Chaco','0');
INSERT INTO public.provincia (id, provincia, estado) values (7,'Chubut','0');
INSERT INTO public.provincia (id, provincia, estado) values (8,'Entre Rios','0');
INSERT INTO public.provincia (id, provincia, estado) values (9,'Formosa','0');
INSERT INTO public.provincia (id, provincia, estado) values (10,'Jujuy','0');
INSERT INTO public.provincia (id, provincia, estado) values (11,'La Pampa','0');
INSERT INTO public.provincia (id, provincia, estado) values (12,'La Rioja','0');
INSERT INTO public.provincia (id, provincia, estado) values (13,'Mendoza','0');
INSERT INTO public.provincia (id, provincia, estado) values (14,'Misiones','0');
INSERT INTO public.provincia (id, provincia, estado) values (15,'Neuquen','0');
INSERT INTO public.provincia (id, provincia, estado) values (16,'Rio Negro','0');
INSERT INTO public.provincia (id, provincia, estado) values (17,'Salta','0');
INSERT INTO public.provincia (id, provincia, estado) values (18,'San Juan','0');
INSERT INTO public.provincia (id, provincia, estado) values (19,'San Luis','0');
INSERT INTO public.provincia (id, provincia, estado) values (20,'santa cruz','0');
INSERT INTO public.provincia (id, provincia, estado) values (21,'Santa Fe','0');
INSERT INTO public.provincia (id, provincia, estado) values (22,'Santiago del Estero','0');
INSERT INTO public.provincia (id, provincia, estado) values (23,'Tucuman','0');
INSERT INTO public.provincia (id, provincia, estado) values (24,'Tierra del Fuego','0');
INSERT INTO public.provincia (id, provincia, estado) values (25,'Verificar Datos','0');

	-- SEQUENCE: public.provincia_id_seq
	DROP SEQUENCE public.provincia_id_seq;
	CREATE SEQUENCE public.provincia_id_seq
		INCREMENT 1
		START 26
		MINVALUE 1
		MAXVALUE 9223372036854775807
		CACHE 1;

	ALTER SEQUENCE public.provincia_id_seq
		OWNER TO postgres;
		

		