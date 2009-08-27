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
 * Executes the requested action by calling do<Action>Action().
 * A default action method is always present.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FrontController
{
    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Construct the FrontController.
     *
     * @param Router  $router  Router object
     * @param Session $session Session object
     * @return void
    */
    public function __construct(Router $router, Session $session)
    {
        $this->router  = $router;
        $this->session = $session;
    }

    /**
     * Initializes the application.
     * Registers paths to load classes from with the autoloader.
     *
     * @return null
     */
    protected function initApplication()
    {
    }

    /**
     * Check whether selected controller and action is allowed,
     * or if anonymous access is allowed at all.
     *
     * You might just allow the login controller here
     *
     * @param string $controller
     * @param string $action
     * @return bool
     */
    protected function isAllowed()
    {
        return true;
    }

    /**
     * Main method. Initializes the application,
     * dispatches the request (selects controller and action),
     * runs the controller, and renders the view.
     * Passes back a response object with HTTP code, MIME-Header,
     * and page content.
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     * @param Authenticator $authenticator Authenticator object
     * @return void
     */
    public function execute(Request $request, Response $response, Authenticator $authenticator)
    {
        $this->request = $request;
        $this->response = $response;
        $this->authenticator = $authenticator;

        $this->initApplication();

        $this->controllerClass = $this->router->getControllerClass();
        $this->action = $this->router->getAction();

        if (!$this->isAllowed()) {
            $this->controllerClass = $this->router->getAuthenticationControllerClass();
            $this->action = $this->router->getAuthenticationAction();
        }

        if (!class_exists($this->controllerClass)) {
            throw new FrontControllerException('Controller class ' . $this->controllerClass . ' does not exist');
        }

        $class = $this->controllerClass;
        $controller = new $class();

        $controller->execute($this->request, $this->response, $this->session, $this->authenticator, $this->action);
    }
}
?>