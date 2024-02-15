<?php

define('DB_USER_VIEWER', 'root');
define('DB_PASSWORD_VIEWER', '');
define('DB_HOST_VIEWER', 'localhost');
define('DB_NAME_VIEWER', 'tv');
define('URL_WEBSITE_VIEWER', 'http://localhost/');

define('VERSION', /*'2.0'*/ rand(1, 9999999));

define('PATH',  $_SERVER['DOCUMENT_ROOT']);
define('URL_PATH', '');

/* Actualise la TV toute les 30s */
define('TV_REFRESH', true);

define('WEATHER_API_KEY', 'OPEN WEATHER MAP API KEY');
define('WEATHER_LATITUDE', '43.5156');
define('WEATHER_LONGITUDE', '5.4510');
