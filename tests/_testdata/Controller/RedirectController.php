<?php

namespace spriebsch\MVC\Test;

class RedirectController extends \spriebsch\MVC\Controller
{
    protected function method()
    {
        $this->redirect('controller');
    }
}
?>