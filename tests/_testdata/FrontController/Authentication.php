<?php

namespace spriebsch\MVC\Test\FrontController;

class Authentication extends \spriebsch\MVC\Controller
{
    protected function method()
    {
        throw new AuthenticationExecutedException();
    }
}

class AuthenticationExecutedException extends \spriebsch\MVC\Exception
{
}
?>