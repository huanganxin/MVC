<?php

namespace spriebsch\MVC\Test;

class SetViewNameController extends \spriebsch\MVC\Controller
{
    protected function method()
    {
        $this->setViewName('view');
    }
}
?>