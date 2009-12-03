CREATE FUNCTION plpgsql_my() RETURNS OPAQUE AS '/usr/lib/postgresql/plpgsql.so' LANGUAGE 'C';
CREATE LANGUAGE 'plpgsql' HANDLER plpgsql_my LANCOMPILER 'PL/pgSQL';
