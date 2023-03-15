--------------------------------------------------------
-- Archivo creado  - lunes-febrero-21-2022   
--------------------------------------------------------
--------------------------------------------------------
-- ESTADO
--------------------------------------------------------
DELETE FROM public.estado;
INSERT INTO public.estado(id, estado_profesional, estado) values (1,'Activo',0);
INSERT INTO public.estado(id, estado_profesional, estado) values (2,'Inhabilitado',1);
INSERT INTO public.estado(id, estado_profesional, estado) values (3,'Suspendido',2);
INSERT INTO public.estado(id, estado_profesional, estado) values (4,'Cancelado',3);
INSERT INTO public.estado(id, estado_profesional, estado) values (5,'Jubilado',4);

	-- SEQUENCE: public.estado_id_seq
	DROP SEQUENCE public.estado_id_seq;
	CREATE SEQUENCE public.estado_id_seq
		INCREMENT 1
		START 6
		MINVALUE 1
		MAXVALUE 9223372036854775807
		CACHE 1;

	ALTER SEQUENCE public.estado_id_seq
		OWNER TO postgres;
		