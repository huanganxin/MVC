<?php

namespace spriebsch\MVC\Test;

class DefaultActionController extends \spriebsch\MVC\Controller
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