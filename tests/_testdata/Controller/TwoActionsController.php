<?php

namespace spriebsch\MVC\Test;

class TwoActionsController extends \spriebsch\MVC\Controller
{
    protected function first()
    {
    }

    protected function second()
    {
        throw new SomeActionExecutedException();
    }

}

class SomeActionExecutedException extends \spriebsch\MVC\Exception
{
}
?>