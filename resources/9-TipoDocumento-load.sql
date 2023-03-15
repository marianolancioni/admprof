--------------------------------------------------------
-- Archivo creado  - lunes-mayo-16-2022   
--------------------------------------------------------
--------------------------------------------------------
-- TIPO_DOCUMENTO
--------------------------------------------------------
DELETE FROM public.tipo_documento;
INSERT INTO public.tipo_documento(id, tipo_documento) values (1,'DNI');

	-- SEQUENCE: public.tipo_documento_id_seq
	DROP SEQUENCE public.tipo_documento_id_seq;
	CREATE SEQUENCE public.tipo_documento_id_seq
		INCREMENT 1
		START 2
		MINVALUE 1
		MAXVALUE 9223372036854775807
		CACHE 1;

	ALTER SEQUENCE public.tipo_documento_id_seq
		OWNER TO postgres;
		