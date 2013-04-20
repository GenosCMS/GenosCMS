<?php
/*
|--------------------------------------------------------------------------
| Base de datos
|--------------------------------------------------------------------------
|
| 'cache_add_salt' = Encriptar nombre de los archivos de caché.? 
| 'cache_salt'     = Es la cadena de encriptación para los archivos de caché.
| 'cache_suffix'   = Es el sufijo que tendrás los archivos al guardarce en caché.
|
*/
$_CONF['db']['driver'] = 'mysql';
$_CONF['db']['host'] = '127.0.0.1';
$_CONF['db']['user'] = 'root';
$_CONF['db']['pass'] = '336699@db';
$_CONF['db']['name'] = 'genos_cms';
$_CONF['db']['prefix'] = 'gn_';
$_CONF['db']['port'] = '';

/*
|--------------------------------------------------------------------------
| Caché
|--------------------------------------------------------------------------
|
| 'cache_add_salt' = Encriptar nombre de los archivos de caché.? 
| 'cache_salt'     = Es la cadena de encriptación para los archivos de caché.
| 'cache_suffix'   = Es el sufijo que tendrás los archivos al guardarce en caché.
|
*/
$_CONF['cache.cache_add_salt'] = false;
$_CONF['cache.cache_salt'] = '';
$_CONF['cache.cache_suffix'] = '.php';

/*
|--------------------------------------------------------------------------
| Sufijo de la URL
|--------------------------------------------------------------------------
|
| Esta opción le permite añadir un sufijo a todas las URL generadas por Polaris.
| Para obtener más información, consulte la guía del usuario:
|
| http://polarisframework.com/docs/general/urls.html
*/

/*$_CONF['url_suffix'] = '';*/

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| Esto determina qué conjunto de archivos de idioma se debe utilizar. 
| Asegúrese de que hay una traducción disponible si va a usar algo
| que no sea Español.
|
*/
$_CONF['core.language']	= 'es_MX';

/*
|--------------------------------------------------------------------------
| Caracteres permitidos en la URL
|--------------------------------------------------------------------------
|
| Esto le permite espesificar con una expresión regular qué caracteres
| están permitidos en las URL. Cuando alguien trata de ingresar una URL
| con caracteres no permitidos se enviará un mensaje de error.
|
| Como medida de seguridad se recomienda restringir caracteres que puedan
| representar una amenza.
|
| Si se siente con suerte, deje en blanco para permitir que todos los 
| caracteres.
|
| NO CAMBIE ESTO A MENOS QUE ENTIENDA LAS REPERCUSIONES!!!
|
*/
//$_CONF['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Variables relacionadas con cookies
|--------------------------------------------------------------------------
|
| 'cookie_domain' = Establesca .su-dominio.com para las cookies en subdominos.
| 'cookie_path'   = Normalmente será una barra diagonal.
| 'cookie_prefix' = Establezca un prefijo si es necesario para evitar colisiones.
| 'cookie_secure' = Las cookies sólo se establecerán si una conexión segura HTTPS existe.
|
*/
$_CONF['cookie_domain']	= '';
$_CONF['cookie_path']		= '/';
$_CONF['cookie_prefix']	= '';
$_CONF['cookie_secure']	= false;

$_CONF['core.is_installed'] = true;


// GZIp
$_CONF['core.use_gzip'] = false;
$_CONF['core.gzip_level'] = 1;