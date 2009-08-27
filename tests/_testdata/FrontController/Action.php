<?php

namespace spriebsch\MVC\Test\FrontController;

class Action extends \spriebsch\MVC\Controller
{
    protected function doDefaultAction()
    {
        throw new DefaultActionExecutedException();
    }
}

class DefaultActionExecutedException extends \spriebsch\MVC\Exception
{
}
?>