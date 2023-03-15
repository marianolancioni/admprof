-- CREA ESQUEMA DE AUDITORIA
CREATE SCHEMA auditoria AUTHORIZATION postgres;

---------------------------------------------------------------------
-- Crea la función que se ocupará de crear el código personalizado 
-- para la tabla a auditar (recibida por parámetro)
--
-- La función creada se asociará al trigger de la tabla a auditar.
--
-- La función tiene como convensión [nombreTabla]_audit_upd() y
-- se alojará en el esquema de Auditoría como una Función Disparadora
---------------------------------------------------------------------

-- FUNCTION: auditoria.acp_audit_create_function(name)
-- DROP FUNCTION auditoria.acp_audit_create_function(name);
CREATE OR REPLACE FUNCTION auditoria.acp_audit_create_function(
	name)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
	function_declaration TEXT;
	tablename ALIAS FOR $1;
	tabla text;
	cantidad int;
	posicion int;
BEGIN
	cantidad = character_length(tablename);
	posicion = strpos(tablename, '.')+1;
	tabla = substring(tablename from posicion for ((cantidad-posicion)+1));

   function_declaration := $FUNCTION$
        CREATE OR REPLACE FUNCTION TG_TABLE_NAME_audit_upd() RETURNS TRIGGER STRICT LANGUAGE plpgsql 
        AS $PROC$
        DECLARE
                rows_affected INTEGER;
        BEGIN
                IF TG_OP = 'INSERT' THEN
                   INSERT INTO TG_TABLE_NAME_audit (audit_iduserapp, audit_identity, audit_action, audit_new) VALUES (NEW.last_user_app_id, NEW.id, 'I', NEW );
                   RETURN NEW;
        	    ELSIF (TG_OP = 'DELETE') THEN
                   INSERT INTO TG_TABLE_NAME_audit (audit_iduserapp, audit_identity, audit_action, audit_old) VALUES (OLD.last_user_app_id, OLD.id, 'D', OLD );
                ELSIF (TG_OP = 'UPDATE') THEN
                   INSERT INTO TG_TABLE_NAME_audit (audit_iduserapp, audit_identity, audit_action, audit_old, audit_new) VALUES (NEW.last_user_app_id, NEW.id, 'U', OLD , NEW );
                   RETURN NEW;
               ELSE
                   RAISE EXCEPTION 'TG_OP % is none of INSERT, UPDATE or DELETE.', TG_OP;
               END IF;
               GET DIAGNOSTICS rows_affected = ROW_COUNT;
               IF rows_affected = 1 THEN
                   IF TG_OP IN ('INSERT', 'UPDATE') THEN
                       RETURN NEW;
                   ELSE
                       RETURN OLD;
                   END IF;
               ELSE
                   RAISE EXCEPTION 'INSERT fallo en auditoria';
               END IF;
        END
        $PROC$;
$FUNCTION$;

    function_declaration := replace (function_declaration, 'TG_TABLE_NAME', 'auditoria.'||tabla);
	RAISE NOTICE '%',function_declaration;	
    EXECUTE function_declaration;/**/
/**/

END
$BODY$;

ALTER FUNCTION auditoria.acp_audit_create_function(name)
    OWNER TO postgres;


---------------------------------------------------------------------
-- La función auditoria.acp_audit_table(name) permite activar la 
-- auditoría sobre una tabla (recibida como parámetro a la función).
--
-- Esta función creará una tabla en el esquema de auditoría con igual
-- nombre a la que se audita y con sufijo _audit.
-- Además creará la función disparadora correspondiente con el código 
-- personalizado a la tabla que se audita. Esta función es asociada a
-- un trigger de la tabla para que se dispare ante cualquier operación
-- de alta, baja o modificación.
---------------------------------------------------------------------
-- FUNCTION: auditoria.acp_audit_table(name)
-- DROP FUNCTION auditoria.acp_audit_table(name);
CREATE OR REPLACE FUNCTION auditoria.acp_audit_table(
	name)
    RETURNS character varying
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE 
	tablename ALIAS FOR $1;
	tabla text;
	cantidad int;
	posicion int;
BEGIN
	cantidad = character_length(tablename);
	posicion = strpos(tablename, '.')+1;
	tabla = substring(tablename from posicion for ((cantidad-posicion)+1));
	-- INSERTA LA TABLA PARA EL HISTÓRICO
	EXECUTE 'CREATE TABLE auditoria.' || tabla || '_audit (' ||
			'id serial, ' ||
			'audit_timestamp TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT current_timestamp, ' ||
			'audit_iduserapp integer, ' || 
			'audit_identity integer NOT NULL, ' ||
			'audit_action    char(1) NOT NULL CHECK( audit_action IN (''I'', ''U'', ''D'')), ' ||
			'audit_old ' || tablename || ',' ||
			'audit_new ' || tablename || ',' ||
			'CONSTRAINT '|| tabla ||'_pkey PRIMARY KEY (id)'||
			')';
	
	-- CREA LA FUNCION (alojada en esquema de auditoria como función disparadora)
	PERFORM auditoria.acp_audit_create_function(tablename);
	
	-- CREA EL TRIGGER SOBRE LA TABLA 
	-- (ejecuta la función disparadora asociada a la tabla que se encuentra alojada en el esquema de auditoría)
	EXECUTE 'CREATE TRIGGER ' || tabla || '_audit_upd ' || 
			' BEFORE INSERT OR UPDATE OR DELETE ON ' || tablename || 
			' FOR EACH ROW EXECUTE PROCEDURE auditoria.' || tabla || '_audit_upd()';
						
	/**/
	/*
	EXECUTE 'CREATE TRIGGER ' || tablename || '_audit_upd ' || 
			' BEFORE INSERT OR UPDATE OR DELETE ON ' || tablename || 
			' FOR EACH ROW EXECUTE PROCEDURE ' || tablename || '_audit_upd()';
	*/
			
    RETURN 'CREATED TABLE ' || tablename || '_audit. CREATED FUNCTION ' || tablename || '_audit_upd. CREATED TRIGGER ' || tabla || '_audit_upd ON ' || tablename; 
END
$BODY$;

ALTER FUNCTION auditoria.acp_audit_table(name)
    OWNER TO postgres;
	

---------------------------------------------------------------------
-- La función auditoria.acp_audit_userid_delete permite actualizar un 
-- registro de auditoría para escribir en operaciones de DELETE el ID
-- del usuario a nivel de Aplicación que realizó la operación de 
-- borrado.
--
-- Esta función es invocada automáticamente a nivel de aplicación
-- frente a operaciones de DELETE sobre la tabla auditada.
---------------------------------------------------------------------
-- FUNCTION: auditoria.acp_audit_userid_delete(character varying, integer, integer)
-- DROP FUNCTION auditoria.acp_audit_userid_delete(character varying, integer, integer);
CREATE OR REPLACE FUNCTION auditoria.acp_audit_userid_delete(
	tablename character varying, 
	id_entity int, 
	id_user_app integer)
    RETURNS integer
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
	rows_affected INTEGER;
	
BEGIN
	EXECUTE 'UPDATE auditoria.' || tablename || '_audit SET audit_iduserapp=' || id_user_app || 'WHERE audit_identity = ' || id_entity || ' and audit_action = ''D''';
	GET DIAGNOSTICS rows_affected = ROW_COUNT;
	RETURN rows_affected;
	EXCEPTION
		WHEN sqlstate '42P01' THEN
			RETURN -1;
END
$BODY$;

ALTER FUNCTION auditoria.acp_audit_userid_delete(tablename character varying, id_entity integer, id_user_app integer)
    OWNER TO postgres;
