<?php

require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/lib/Loader.phar';

spriebsch\Loader\Autoloader::init();
spriebsch\Loader\Autoloader::registerPath(__DIR__ . '/../src');

spriebsch\Loader\Autoloader::registerPath(__DIR__ . '/_testdata/Controller');
spriebsch\Loader\Autoloader::registerPath(__DIR__ . '/_testdata/FrontController');
spriebsch\Loader\Autoloader::registerPath(__DIR__ . '/_testdata/ViewHelper');

?>
