<?php

namespace spriebsch\MVC\Test\FrontController;

class TestFC extends \spriebsch\MVC\FrontController
{
	protected $controllerInstance;

	public function setControllerInstance(\spriebsch\MVC\Controller $controller)
	{
		$this->controllerInstance = $controller;
	}

    protected function getControllerInstance($controllerClass)
    {
    	if ($this->controllerInstance === null) {
    		throw new \spriebsch\MVC\Exception('Controller not injected');
    	}
    	
        return $this->controllerInstance;
    } 
}
?>