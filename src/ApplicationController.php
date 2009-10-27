<?php
/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    MVC
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\MVC;

/**
 * The Application Controller is selects views based on the results of the 
 * input (MVC) controllers and forwards to other controllers.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ApplicationController
{
	/**
	 * @var Session
	 */
	protected $session;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var string
     */
    protected $defaultView = 'main';
	
    /**
     * @var string
     */
    protected $defaultController = 'main';

    /**
     * @var string
     */
    protected $authenticationController = 'authentication.login';

    /**
     * @var string
     */
    protected $errorView = 'error';

    /**
     * Controller/action map.
     *
     * Is an associative array of the format 
     * controller name => array(controller class, action method)
     *
     * @var array
     */
    protected $map = array();

    protected $views = array();

    protected $forwards = array();

    protected $redirect = array();
    
    /**
     * Construct the Application Controller.
     *
     * @param array $map Controller/Action map
     * @return null
     */
    public function __construct(Session $session, Acl $acl, array $map = array())
    {
    	$this->session = $session;
        $this->acl = $acl;
    	$this->map = $map;
    }

    /**
     * Helper method for getClass() and getMethod() that
     * either returns the class or the method name of selected controller name
     * (the one from the URL) by looking it up in the $map.
     *
     * @param string $controller
     * @param bool $classFlag
     * @return string
     */
    protected function doGet($controller, $classFlag)
    {
        if (!isset($this->map[$controller])) {
            throw new RouterException('Controller ' . $controller . ' is not registered');
        }

        list($class, $method) = $this->map[$controller];

        if ($classFlag) {
            return $class;
        } else {
            return $method;
        }
    }
    
    /**
     * Sets the default view (the "main page" of the application).
     *
     * @param string $view
     * @return void
     */
    public function setDefaultView($defaultView)
    {
        $this->defaultView = $defaultView;
    }

    /**
     * Returns the default view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return $this->defaultView;
    }

    /**
     * Sets the global error view (the "500" page).
     *
     * @param string $view
     * @return void
     */
    public function setErrorView($errorView)
    {
        $this->errorView = $errorView;
    }

    /**
     * Returns the error view.
     *
     * @return string
     */
    public function getErrorView()
    {
        return $this->errorView;
    }

    /**
     * Sets the default controller.
     * The default controller is called when no controller is selected.
     *
     * @param string $controller
     * @return void
     */
    public function setDefaultController($controller)
    {
        $this->defaultController = $controller;
    }

    /**
     * Returns the default controller.
     *
     * @return string Default controller name
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * Sets the authentication controller that is called
     * when the selected controller is not allowed.
     *
     * @param string $controller
     * @return void
     */
    public function setAuthenticationController($controller)
    {
        $this->authenticationController = $controller;
    }

    /**
     * Returns the authentication controller.
     *
     * @return string Authentication controller name
     */
    public function getAuthenticationController()
    {
        return $this->authenticationController;
    }

    /**
     * Add a mapping of a controller name (the one from the URL)
     * to the controller class/action method to call.
     *
     * @param string $controller Controller name
     * @param string $class      Controller class
     * @param string $method     Controller method
     * @return void
     */
    public function registerController($controller, $class, $method)
    {
        $this->map[$controller] = array($class, $method);
    }

    /**
     * Returns the class name for given controller name (the one from the URL).
     *
     * @param string $controller Controller name
     * @return string
     */
    public function getClassName($controller)
    {
        return $this->doGet($controller, true);
    }

    /**
     * Returns the method name for given controller name (the one from the URL).
     *
     * @param string $controller Controller name
     * @return string
     */
    public function getMethodName($controller)
    {
        return $this->doGet($controller, false);
    }

    /**
     * Returns controller name read from mvc_controller URL parameter
     * (POST has precedence over GET). If mvc_controller is not given,
     * falls back to default controller.
     *
     * @param Request $request
     * @return null
     */
    public function route($request)
    {
        // Fallback: route to default controller and action.
        $controller = $this->getDefaultController();

        // GET parameter overrides the default controller.
        if ($request->hasGet('mvc_controller')) {
            $controller = $request->get('mvc_controller');
        }

        // POST parameter overrides GET parameter.
        if ($request->hasPost('mvc_controller')) {
            $controller = $request->post('mvc_controller');
        }
        
// @todo use user role here. where to get from?        
        if (!$this->session->has('_MVC_USER_ROLE') || !$this->acl->isAllowed($this->session->get('_MVC_USER_ROLE'), $controller)) {
            $controller = $this->authenticationController;	
        }

// @todo remember selected controller & action to back-direct later
// @todo either redirect to auth controller (for anonymous) OR FAIL?
        
        return $controller;
    }
    
    /**
     * Set the view object.
     *
     * @param $view
     * @return unknown_type
     * @todo allow different view objects based on app state
     */
    public function setViewObject(View $view)
    {
    	$this->view = $view;
    }

    /**
     * Returns the view to display.
     * 
     * @return string
     */
    public function getView($controllerName, $result)
    {
    	if ($this->view === null) {
    		throw new Exception('No view object is set');
    	}
    	
    	if (isset($this->redirects[$controllerName][$result])) {
    		$this->view->setRedirect($this->redirects[$controllerName][$result]);
    		return $this->view;
    	}

    	if (!isset($this->views[$controllerName][$result])) {
    		throw new Exception('No view for controller ' . $controllerName . ' result ' . $result);
    	}
    	
        $name = $this->views[$controllerName][$result];
        
        $this->view->setViewName($name);

    	return $this->view;
    }
    
    public function addView($controllerName, $result, $viewName)
    {
    	$this->views[$controllerName][$result] = $viewName;
    }
   
    public function addForward($controllerName, $result, $forwardController)
    {
        $this->forwards[$controllerName][$result] = $forwardController;
    }

    public function getForward($controllerName, $result)
    {
        return $this->forwards[$controllerName][$result];
    }
    
    public function isForward($controllerName, $result)
    {
        return isset($this->forwards[$controllerName][$result]);
    }
    
    public function addRedirect($controllerName, $result, $redirectController)
    {
        $this->redirects[$controllerName][$result] = $redirectController;
    }

    public function getRedirect($controllerName, $result)
    {
        return $this->redirects[$controllerName][$result];
    }

    public function isRedirect($controllerName, $result)
    {
        return isset($this->redirects[$controllerName][$result]);
    }
}
?>