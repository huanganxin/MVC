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
	 * @var authenticationHandler
	 */
	protected $authenticationHandler;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;
    
    /**
     * @var string
     */
    protected $defaultControllerName = 'main';

    /**
     * @var string
     */
    protected $authenticationControllerName = 'authentication.login';

    /**
     * @var View
     */
    protected $defaultView;

    /**
     * Controller/action map.
     *
     * Is an associative array of the format 
     * controller name => array(controller class, action method)
     *
     * @var array
     */
    protected $controllers = array();

    /**
     * @var array
     */
    protected $viewScripts = array();

    /**
     * @var array
     */
    protected $forwards = array();

    /**
     * @var array
     */
    protected $redirects = array();
    
    /**
     * Construct the Application Controller.
     *
     * @param Session     $session 
     * @param Acl         $acl
     * @param ViewFactory $viewFactory
     * @return null
     */
    public function __construct(AuthenticationHandler $authenticationHandler, Acl $acl, ViewFactory $viewFactory)
    {
    	$this->authenticationHandler = $authenticationHandler;
        $this->acl = $acl;
        $this->viewFactory = $viewFactory;
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
        if (!isset($this->controllers[$controller])) {
            throw new RouterException('Controller ' . $controller . ' is not registered');
        }

        list($class, $method) = $this->controllers[$controller];

        if ($classFlag) {
            return $class;
        } else {
            return $method;
        }
    }

    /**
     * Sets the default controller.
     * The default controller is called when no controller is selected.
     *
     * @param string $controllerName
     * @return void
     */
    public function setDefaultControllerName($controllerName)
    {
        $this->defaultControllerName = $controllerName;
    }

    /**
     * Sets the authentication controller that is called
     * when the selected controller is not allowed.
     *
     * @param string $controller
     * @return void
     */
    public function setAuthenticationControllerName($controllerName)
    {
        $this->authenticationControllerName = $controllerName;
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
        $this->controllers[$controller] = array($class, $method);
    }

    /**
     * Returns the class name for given controller name (the one from the URL).
     *
     * @param string $controller Controller name
     * @return string
     */
    public function getClass($controller)
    {
        return $this->doGet($controller, true);
    }

    /**
     * Returns the method name for given controller name (the one from the URL).
     *
     * @param string $controller Controller name
     * @return string
     */
    public function getMethod($controller)
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
    public function getControllerName(Request $request)
    {
        // Fallback: route to default controller and action.
        $controllerName = $this->defaultControllerName;

        // GET parameter overrides the default controller.
        if ($request->hasGet('mvc_controller')) {
            $controllerName = $request->get('mvc_controller');
        }

        // POST parameter overrides GET parameter.
        if ($request->hasPost('mvc_controller')) {
            $controllerName = $request->post('mvc_controller');
        }
        
        $role = $this->authenticationHandler->getRole();

        // If that controller is not allowed, select authentication controller.
        if (!$this->acl->isAllowed($role, $controllerName)) {
            $controllerName = $this->authenticationControllerName;	
        }

// @todo remember selected controller & action to back-direct later
// @todo either redirect to auth controller (for anonymous) OR FAIL?
        
        return $controllerName;
    }

    /**
     * Returns the view to display.
     * 
     * @return string
     * @todo select view instance based on controller name
     */
    public function getView($controllerName, $result)
    {
    	// On redirect, load default view object and tell it to redirect.  
    	if (isset($this->redirects[$controllerName][$result])) {
            $view = $this->viewFactory->getView();
    		$view->setRedirect($this->redirects[$controllerName][$result]);
    		return $view;
    	}
   	
    	// If no view script is configured, we don't know what to do. 
    	if (!isset($this->views[$controllerName][$result])) {
    		throw new Exception('Controller "' . $controllerName . '" result "' . $result . '" has no view script');
    	}

        // Get the "real" view object (depending on the view script).
        $view = $this->viewFactory->getView($this->views[$controllerName][$result]);

    	return $view;
    }

    /**
     * Add a view for given controller result.
     *
     * @param string $controllerName
     * @param string $controllerResult
     * @param string $viewName
     * @return null
     */
    public function registerView($controllerName, $controllerResult, $viewName)
    {
    	$this->views[$controllerName][$controllerResult] = $viewName;
    }

    /**
     * Add forwarding to another controller for given controller result.
     *  
     * @param string $controllerName
     * @param string $controllerResult
     * @param string $forwardController
     * @return null
     */
    public function registerForward($controllerName, $controllerResult, $forwardController)
    {
        $this->forwards[$controllerName][$controllerResult] = $forwardController;
    }

    /**
     * Get controller name to forward to for given controller result.
     * 
     * @param $controllerName
     * @param $controllerResult
     * @return string
     */
    public function getForward($controllerName, $controllerResult)
    {
        return $this->forwards[$controllerName][$controllerResult];
    }

    /**
     * Checks whether given controller result requires a forward.
     * 
     * @param string $controllerName
     * @param string $controllerResult
     * @return bool
     */
    public function isForward($controllerName, $controllerResult)
    {
        return isset($this->forwards[$controllerName][$controllerResult]);
    }

    /**
     * Add a (browser) redirect to another controller for given controller result.
     * 
     * @param string $controllerName
     * @param string $controllerResult
     * @param string $redirectController
     * @return null
     */
    public function registerRedirect($controllerName, $controllerResult, $redirectController)
    {
        $this->redirects[$controllerName][$controllerResult] = $redirectController;
    }

    /**
     * Returns the redirect controller name for given controller result.
     *
     * @param string $controllerName
     * @param string $controllerResult
     * @return string
     */
    public function getRedirect($controllerName, $controllerResult)
    {
        return $this->redirects[$controllerName][$controllerResult];
    }

    /**
     * Checks whether given controller result requires a (browser) redirect
     * to another controller.
     * 
     * @param string $controllerName
     * @param string $controllerResult
     * @return bool
     */
    public function isRedirect($controllerName, $controllerResult)
    {
        return isset($this->redirects[$controllerName][$controllerResult]);
    }
}
?>