<?php

namespace spriebsch\MVC\Test;

class NotAllowedController extends \spriebsch\MVC\Controller
{
    protected function actionMethod()
    {
        throw new Exception('Action method executed');
    }

    protected function isAllowed($method)
    {
        return false;
    }
}
?>