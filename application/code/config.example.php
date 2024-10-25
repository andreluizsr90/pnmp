<?php

define('PATH_THIS', __DIR__);
define('PATH_APP', PATH_THIS . "/app");
define('PATH_ROUTES', PATH_APP . "/routes");
define('PATH_PUBLIC', PATH_THIS . "/../html");
define('PATH_TEMP', PATH_THIS . "/temp");
define('PATH_VIEWS', PATH_APP . "/views");

define('URL_DOMAIN', 'http://' . $_SERVER["HTTP_HOST"]);
define('URL_SUBFOLDER', '');
define('URL_SITE', URL_DOMAIN . URL_SUBFOLDER);
define('URL_ASSETS', URL_SITE . '/assets');

define('CFG_VIEWS_CACHE', false);
define('CFG_VIEWS_DEBUG', false);

define('CFG_DB', [
    'driver'    => 'sqlsrv',
    'host' => '192.168.1.67',
    'port' => '1433',
    'database' => 'pnmp',
    'username' => 'sa',
    'password' => 'MyPass@word',
    'charset' => PDO::SQLSRV_ENCODING_UTF8,
    'trust_server_certificate' => 'true',
]);

date_default_timezone_set('America/BahiaLisbon');

ini_set('display_errors', 1);
ini_set('memory_limit', -1);