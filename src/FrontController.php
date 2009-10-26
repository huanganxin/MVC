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
 * BE LIABLE FOR ANY DIRECT INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
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
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var string
     */
    protected $controllerMethod;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $userRole = 'anonymous';

    /**
     * Construct the FrontController.
     *
     * @param Request       $request       Request object
     * @param Response      $response      Response object
     * @param Session       $session       Session object
     * @param View          $view          View
     * @param Router        $router        Router object
     * @param Authenticator $authenticator Authenticator object
     * @param Acl           $acl           Access Control List
     * @return void
    */
    public function __construct(Request $request, Response $response, Session $session, View $view, Router $router, Authenticator $authenticator, Acl $acl)
    {
        $this->request       = $request;
        $this->response      = $response;
        $this->session       = $session;
        $this->view          = $view;
        $this->router        = $router;
        $this->authenticator = $authenticator;
        $this->acl           = $acl;
    }

    /**
     * Initializes the application.
     *
     * @return null
     */
    protected function initApplication()
    {
        $this->session->start();
 
        if ($this->session->has('_MVC_USER_ID') && $this->session->has('_MVC_USER_ROLE')) {
            $this->session->set('_MVC_TIMESTAMP', $this->request->server('REQUEST_TIME'));
            $this->userRole = $this->session->get('_MVC_USER_ROLE');
        }

// @todo check session expiration.

    }

    /**
     * Check whether selected controller and action is allowed for the current
     * user role.
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->acl->isAllowed($this->userRole, $this->controller);
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

        $this->controller = $this->router->route($this->request);

        // If selected controller is not allowed, redirect to authentication controller. 
        if (!$this->isAllowed()) {

// @todo remember selected controller & action to back-direct later
// @todo either redirect to auth controller (for anonymous) OR FAIL?
            $this->controller = $this->router->getAuthenticationController();
        }

        $this->controllerClass  = $this->router->getClassName($this->controller);
        $this->controllerMethod = $this->router->getMethodName($this->controller);

        if (!class_exists($this->controllerClass)) {
            throw new FrontControllerException('Controller class ' . $this->controllerClass . ' does not exist');
        }

        $class = $this->controllerClass;
        $controller = new $class();

        $controller->execute($this->request, $this->response, $this->session, $this->authenticator, $this->controllerMethod);

        return $this->view->render($this->response->getViewName(), $this->request, $this->response);
    }
}
?>