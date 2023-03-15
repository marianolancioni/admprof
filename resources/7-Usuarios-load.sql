--------------------------------------------------------
-- Archivo creado  - lunes-marzo-21-2022   
--------------------------------------------------------
--------------------------------------------------------
-- USUARIOS
--------------------------------------------------------
--
-- PostgreSQL database dump
--

-- Dumped from database version 12.4
-- Dumped by pg_dump version 13.4

-- Started on 2022-03-20 07:57:09

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2872 (class 0 OID 28134)
-- Dependencies: 213
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--

DELETE FROM public.usuario_log_accion;
DELETE FROM public.usuario;

INSERT INTO public.usuario (id, colegio_id, circunscripcion_id, username, roles, password, dni, apellido, nombre, email, fecha_alta, fecha_baja, ultimo_acceso, cantidad_accesos, trusted_version) VALUES (1, NULL, NULL, 'supersfe', '["ROLE_SUPER_ADMIN"]', '$2y$13$etWszRIxh5znV6ALXd5Ux.NyCm7vEdBibyo0Ki1lu56xESXeRFYh6', 1, 'Usuario Administrador (Santa Fe) - Ñandú N° 10', '', 'mmaglianesi@justiciasantafe.gov.ar', NOW(), NULL, NULL, NULL, 0);
INSERT INTO public.usuario (id, colegio_id, circunscripcion_id, username, roles, password, dni, apellido, nombre, email, fecha_alta, fecha_baja, ultimo_acceso, cantidad_accesos, trusted_version) VALUES (2, NULL, NULL, 'superros', '["ROLE_SUPER_ADMIN"]', '$2y$13$BHxHupDueggbn80ezOpFw.QRfJVkOSeVayCzNUR/FPReTr9V2RmZe', 1, 'Usuario Administrador (Rosario)', '', 'csanchez@justiciasantafe.gov.ar', NOW(), NULL, NULL, NULL, 0);

-- Completed on 2022-03-20 07:57:09

--
-- PostgreSQL database dump complete
--


-- SEQUENCE: public.usuario_id_seq
DROP SEQUENCE public.usuario_id_seq;
CREATE SEQUENCE public.usuario_id_seq
	INCREMENT 1
	START 3
	MINVALUE 1
	MAXVALUE 9223372036854775807
	CACHE 1;

ALTER SEQUENCE public.usuario_id_seq
	OWNER TO postgres;
		