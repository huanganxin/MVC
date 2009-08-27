<?php

$phar = new Phar('_phar/MVC.phar');
$phar->buildFromDirectory('src');
$phar->setStub(file_get_contents('PharStub.php'));

?>
