<?php

$_CONF['core.path'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_CONF['core.host'] . $_CONF['core.folder'];

/*
|--------------------------------------------------------------------------
| Archivos estáticos
|--------------------------------------------------------------------------
|
| URL donde se encuentran alojados sus archivos estáticos.
|
*/
$_CONF['core.url_static'] = $_CONF['core.path'] . 'static/';
$_CONF['core.url_static_script'] = $_CONF['core.url_static'] . 'js/';
$_CONF['core.url_static_style'] = $_CONF['core.url_static'] . 'css/';
$_CONF['core.url_static_image'] = $_CONF['core.url_static'] . 'img/';

/**
 * Agrear a la DB
 */
$_CONF['admin.admin_folder'] = 'admin';

/**
 * Agregar a la DB y colocar por defecto?
 */
$_CONF['core.url_full_path'] = false; // Añadir la dirección URL completa a las url internas?
$_CONF['core.url_suffix'] = ''; // Sufijo para las URL
$_CONF['core.url_ajax_suffix'] = '.php'; // Sufijo para las URL de peticiones AJAX
$_CONF['core.url_rewrite_mod'] = true; // Activar Rewrite con .httacess