<?php

require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

spriebsch\MVC\Loader::init();
spriebsch\MVC\Loader::registerPath(__DIR__ . '/../src');

spriebsch\MVC\Loader::registerPath(__DIR__ . '/_testdata/Controller');
spriebsch\MVC\Loader::registerPath(__DIR__ . '/_testdata/FrontController');

?>