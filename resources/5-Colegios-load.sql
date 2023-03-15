--------------------------------------------------------
-- Archivo creado  - lunes-febrero-21-2022   
--------------------------------------------------------
--------------------------------------------------------
-- COLEGIO
--------------------------------------------------------
DELETE FROM public.colegio;
INSERT INTO public.colegio(id, colegio, estado, visible) values (0,'Abogados',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (1,'Procuradores',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (2,'Ministerio Público',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (3,'Contadores',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (4,'Martilleros',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (5,'Peritos',0, 0);
INSERT INTO public.colegio(id, colegio, estado, visible) values (6,'Psicólogos',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (7,'Ingenieros',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (8,'Médicos',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (9,'Calígrafos',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (10,'Profesionales Higiene, Seguridad y Salud Ocupacional',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (11,'Profesionaesl de la Ingeniería Civil',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (12,'Profesionales de Maestros Mayores de Obras',0, 1);
INSERT INTO public.colegio(id, colegio, estado, visible) values (13,'Escribanos',0, 1);


	-- SEQUENCE: public.colegio_id_seq
	DROP SEQUENCE public.colegio_id_seq;
	CREATE SEQUENCE public.colegio_id_seq
		INCREMENT 1
		START 14
		MINVALUE 1
		MAXVALUE 9223372036854775807
		CACHE 1;

	ALTER SEQUENCE public.colegio_id_seq
		OWNER TO postgres;
		