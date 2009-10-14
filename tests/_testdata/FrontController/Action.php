<?php

namespace spriebsch\MVC\Test\FrontController;

class Action extends \spriebsch\MVC\Controller
{
    public function execute(\spriebsch\MVC\Request $request, \spriebsch\MVC\Response $response, \spriebsch\MVC\Session $session, \spriebsch\MVC\Authenticator $authenticator, $method)
    {
        throw new ActionExecutedException();
    }
}

class ActionExecutedException extends \spriebsch\MVC\Exception
{
}
?>