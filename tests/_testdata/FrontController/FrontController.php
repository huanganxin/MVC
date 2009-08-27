<?php

namespace spriebsch\MVC\Test\FrontController;

class FrontController extends \spriebsch\MVC\FrontController
{
    protected function isAllowed()
    {
        return false;
    }
}
?>