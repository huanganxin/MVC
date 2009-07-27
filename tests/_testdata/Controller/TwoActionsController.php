<?php

namespace spriebsch\MVC\Test;

class TwoActionsController extends \spriebsch\MVC\Controller
{
    protected function doDefaultAction()
    {
    }

    protected function doSomeAction()
    {
        throw new SomeActionExecutedException();
    }

}

class SomeActionExecutedException extends \spriebsch\MVC\Exception
{
}
?>