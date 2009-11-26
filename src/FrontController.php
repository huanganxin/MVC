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
 * Front Controller. Routes requests to controllers and renders the view.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FrontController
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AuthenticationHandler
     */
    protected $authenticationHandler;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var ApplicationController
     */
    protected $applicationController;

    /**
     * Construct the FrontController.
     *
     * @param Request               $request               Request object
     * @param Response              $response              Response object
     * @param Session               $session               Session object
     * @param View                  $view                  View
     * @param Router                $router                Router object
     * @param AuthenticationHandler $authenticationHandler Authentication handler
     * @param Acl                   $acl                   Access Control List
     * @param ApplicationController $appController         Application Controller
     * @param ControllerFactory     $controllerFactory     Controller Factory
     * @return null
    */
    public function __construct(Request $request, Response $response, Session $session, AuthenticationHandler $authenticationHandler, Acl $acl, ApplicationController $appController, ControllerFactory $controllerFactory)
    {
        $this->request               = $request;
        $this->response              = $response;
        $this->session               = $session;
        $this->authenticationHandler = $authenticationHandler;
        $this->acl                   = $acl;
        $this->applicationController = $appController;
        $this->controllerFactory     = $controllerFactory;
    }

    /**
     * Initializes the application.
     *
     * @return null
     */
    protected function initApplication()
    {
    }

    /**
     * Main method. Initializes the application,
     * dispatches the request (selects controller and action),
     * runs the controller, and renders the view.
     * Passes back a response object with HTTP code, MIME-Header,
     * and page content.
     * Note: authentication controller will be called regardless of ACL.
     *
     * @return void
     */
    public function execute()
    {
        $this->initApplication();

        $controllerName = $this->applicationController->getControllerName($this->request);
        $class = $this->applicationController->getClass($controllerName);
        $method = $this->applicationController->getMethod($controllerName);
        $controller = $this->controllerFactory->getController($class);
        
        $result = $controller->execute($this->request, $this->response, $this->session, $this->authenticationHandler, $method);

        if ('' == $result) {
        	throw new FrontControllerException('Controller "' . $class . '" method "' . $method . '" returned empty result');
        }

// forward here 

        if ($this->applicationController->isForward($controllerName, $result)) {
        	$controller = $this->applicationController->getForward($controllerName, $result); 
// @todo allow multiple forwards, but avoid endless loops        	
        }

        $view = $this->applicationController->getView($controllerName, $result);

        return $view->render($this->request, $this->response);
    }
}
?>