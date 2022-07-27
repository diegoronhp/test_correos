--
-- Estructura de tabla para la tabla `cuenta_correo`
--

DROP TABLE IF EXISTS cuenta_correo;
CREATE TABLE IF NOT EXISTS cuenta_correo(
  id_cuenta_correo int(11) NOT NULL AUTO_INCREMENT,
  alias_cuenta_correo varchar(50) NOT NULL,
  direccion_email varchar(200) NOT NULL,
  password_email varchar(200) NOT NULL,
  servidor_smtp varchar(100) NOT NULL,
  puerto_smtp int(11) NOT NULL,
  servidor_imap varchar(100) NOT NULL,
  puerto_imap int(11) NOT NULL,
  PRIMARY KEY (id_cuenta_correo)
);


--
-- Insercion de registros iniciales para la tabla `cuenta_correo`
--

INSERT INTO cuenta_correo (id_cuenta_correo, alias_cuenta_correo, direccion_email, password_email, servidor_smtp, puerto_smtp, servidor_imap, puerto_imap) VALUES
(1, 'OWA 365', 'correoprueba1@registraduria.gov.co', 'R3gis4620*2022', 'relaycorreo.registraduria.gov.co', 587, '{outlook.office365.com:993/ssl/novalidate-cert}', 993),
(2, 'GMAIL', 'mservicioune@gmail.com', 'byckuhjldiubfxas', 'smtp.gmail.com', 587, '{imap.gmail.com:993/ssl/novalidate-cert}', 993),
(3, 'HOTMAIL', 'monitoreo7x24une@hotmail.com', 'Regis4620**', 'smtp.office365.com', 587, '{outlook.office365.com:993/ssl/novalidate-cert}', 993),
(4, 'ON PREMISE MEDELLIN', 'correoprueba3@registraduria.gov.co', 'Regis4620**', 'relaycorreo.registraduria.gov.co', 587, '{relaycorreo.registraduria.gov.co:993/ssl/novalidate-cert}', 993),
(5, 'ON PREMISE BOGOTA', 'monitoreo7x24@registraduria.gov.co', 'Bogota2022+', 'relaycorreo.registraduria.gov.co', 587, '{relaycorreo.registraduria.gov.co:993/ssl/novalidate-cert}', 993);


--
-- Estructura de tabla para la tabla `prueba_envio_recepcion`
--


DROP TABLE IF EXISTS prueba_envio_recepcion;
CREATE TABLE IF NOT EXISTS prueba_envio_recepcion(
  id_prueba_env_rec int(11) NOT NULL AUTO_INCREMENT,
  nombre_prueba varchar(100) NOT NULL,
  id_correo_origen int(11) NOT NULL,
  id_correo_destino int(11) NOT NULL,
  tiempo_limite int(11) NOT NULL,
  PRIMARY KEY (id_prueba_env_rec),
  FOREIGN KEY (id_correo_origen) REFERENCES cuenta_correo(id_cuenta_correo),
  FOREIGN KEY (id_correo_destino) REFERENCES cuenta_correo(id_cuenta_correo)
);



--
-- Insercion de registros iniciales para la tabla `prueba_envio_recepcion`
--

INSERT INTO `prueba_envio_recepcion` (`id_prueba_env_rec`, `nombre_prueba`, `id_correo_origen`, `id_correo_destino`, `tiempo_limite`) VALUES
  (1, 'TEST DE CORREO (OWA 365 => GMAIL)', 1, 2, 240),
  (2, 'TEST DE CORREO (OWA 365 => HOTMAIL)', 1, 3, 240),
  (3, 'TEST DE CORREO (OWA 365 => ON PREMISE MEDELLIN)', 1, 4, 180),
  (4, 'TEST DE CORREO (OWA 365 => ON PREMISE BOGOTA)', 1, 5, 180),
  (5, 'TEST DE CORREO (GMAIL => OWA 365)', 2, 1, 240),
  (6, 'TEST DE CORREO (GMAIL => HOTMAIL)', 2, 3, 240),
  (7, 'TEST DE CORREO (GMAIL => ON PREMISE MEDELLIN)', 2, 4, 180),
  (8, 'TEST DE CORREO (GMAIL => ON PREMISE BOGOTA)', 2, 5, 180),
  (9, 'TEST DE CORREO (HOTMAIL => OWA 365)', 3, 1, 240),
  (10, 'TEST DE CORREO (HOTMAIL => GMAIL)', 3, 2, 240),
  (11, 'TEST DE CORREO (HOTMAIL => ON PREMISE MEDELLIN)', 3, 4, 180),
  (12, 'TEST DE CORREO (HOTMAIL => ON PREMISE BOGOTA)', 3, 5, 180),
  (13, 'TEST DE CORREO (ON PREMISE MEDELLIN => OWA 365)', 4, 1, 240),
  (14, 'TEST DE CORREO (ON PREMISE MEDELLIN => GMAIL)', 4, 2, 240),
  (15, 'TEST DE CORREO (ON PREMISE MEDELLIN => HOTMAIL)', 4, 3, 240),
  (16, 'TEST DE CORREO (ON PREMISE MEDELLIN => ON PREMISE BOGOTA)', 4, 5, 180),
  (17, 'TEST DE CORREO (ON PREMISE BOGOTA => OWA 365)', 5, 1, 240),
  (18, 'TEST DE CORREO (ON PREMISE BOGOTA => GMAIL)', 5, 2, 240),
  (19, 'TEST DE CORREO (ON PREMISE BOGOTA => HOTMAIL)', 5, 3, 240),
  (20, 'TEST DE CORREO (ON PREMISE BOGOTA => ON PREMISE MEDELLIN)', 5, 4, 180);



--
-- Estructura de tabla para la tabla `prueba_ejecutada`
--

DROP TABLE IF EXISTS prueba_ejecutada;
CREATE TABLE IF NOT EXISTS prueba_ejecutada(
  id_prueba_ejecutada int(11) NOT NULL AUTO_INCREMENT,
  franja_ejecucion varchar(100) NOT NULL,
  fecha_envio datetime NOT NULL,
  diagnostico_envio varchar(100) NOT NULL,
  error_envio varchar(5000) NOT NULL,
  fecha_recepcion datetime DEFAULT NULL,
  diagnostico_recibo varchar(100) NOT NULL,
  error_recibo varchar(5000) NOT NULL,
  tiempo_latencia varchar(2000) NOT NULL,
  observaciones_prueba text NOT NULL,
  id_prueba_env_rec int(11) NOT NULL,
  PRIMARY KEY (id_prueba_ejecutada),
  FOREIGN KEY (id_prueba_env_rec) REFERENCES prueba_envio_recepcion(id_prueba_env_rec)
);