--------------------------------------------------------
-- Archivo creado  - lunes-febrero-21-2022   
--------------------------------------------------------
--------------------------------------------------------
-- CIRCUNSCRIPCION
--------------------------------------------------------
DELETE FROM public.circunscripcion;
INSERT INTO public.circunscripcion(id, circunscripcion, estado, visible) values ('1','Santa Fe','0','1');
INSERT INTO public.circunscripcion(id, circunscripcion, estado, visible) values ('2','Rosario','0','1');
INSERT INTO public.circunscripcion(id, circunscripcion, estado, visible) values ('3','Venado Tuerto','0','1');
INSERT INTO public.circunscripcion(id, circunscripcion, estado, visible) values ('4','Reconquista','0','1');
INSERT INTO public.circunscripcion(id, circunscripcion, estado, visible) values ('5','Rafaela','0','1');

	-- SEQUENCE: public.circunscripcion_id_seq
	DROP SEQUENCE public.circunscripcion_id_seq;
	CREATE SEQUENCE public.circunscripcion_id_seq
		INCREMENT 1
		START 6
		MINVALUE 1
		MAXVALUE 9223372036854775807
		CACHE 1;

	ALTER SEQUENCE public.circunscripcion_id_seq
		OWNER TO postgres;
		