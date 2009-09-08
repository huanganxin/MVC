<?php

namespace spriebsch\MVC\Test\FrontController;

class Action extends \spriebsch\MVC\Controller
{
    protected function method()
    {
        throw new DefaultActionExecutedException();
    }
}

class DefaultActionExecutedException extends \spriebsch\MVC\Exception
{
}
?>