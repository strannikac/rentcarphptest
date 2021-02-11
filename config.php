<?php
//constants for paths
define('_ROOT_', __DIR__ . '/');
define('_URL_', '/');

define('_SERVER_URL_', 'https://domain.com/');
define('_CLIENT_URL_', 'https://domain.com/');
define('_DOMAIN_', 'domain.com');

//for templates
define('_ROOT_WEB_', _ROOT_ . 'html/');
define('_ROOT_TPL_', _ROOT_WEB_ . 'templates/');
define('_URL_WEB_', _URL_ . 'html/');

//for images
define('_ROOT_IMAGES_', _ROOT_WEB_ . 'images/');
define('_URL_IMAGES_', _URL_WEB_ . 'images/');

//for js
define('_ROOT_JS_', _ROOT_WEB_ . 'js/');
define('_URL_JS_', _URL_WEB_ . 'js/');

//for styles
define('_ROOT_CSS_', _ROOT_WEB_ . 'css/');
define('_URL_CSS_', _URL_WEB_ . 'css/');

/* Time limits defines */
define('TOKEN_LIFETIME', 1800);
define('RECOVERY_TOKEN_LIFETIME', 24 * 60 * 60);
define('CONFIRMATION_TOKEN_LIFETIME', 24 * 60 * 60);

//db connection
define('_DB_HOST_', 'localhost');
define('_DB_USER_', 'dbuser');
define('_DB_PASS_', 'dbpassword');
define('_DB_NAME_', 'dbname');
define('_DB_CHARSET_', 'utf8');

?>