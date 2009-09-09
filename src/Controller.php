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
 * Controller class.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
abstract class Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * Convenience delegate method.
     *
     * @param string $name
     * @return void
     */
    protected function setViewName($name)
    {
        $this->response->setViewName($name);
    }

    /**
     * Convenience delegating method.
     *
     * @param string $controller
     */
    protected function redirect($controller = 'standard')
    {
        $this->response->setRedirect($controller);
    }

    /**
     * Initializes the controller
     *
     * @return void
     */
    protected function init()
    {
    }

    /**
     * Checks whether the action method is allowed.
     * ACL are already being checked in front controller.
     * Always returns true, subclass can override this method
     * and implement authentication checks.
     *
     * @param string $action Action name (not action method name)
     * @return bool
     */
    protected function isAllowed($method)
    {
        return true;
    }

    /**
     * Executes the requested action method.
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     * @param Session  $session  Session object
     * @param string   $action   Name of the action to perform
     * @return mixed
     * @throws spriebsch\MVC\ControllerException when requested action does not exist
     */
    public function execute(Request $request, Response $response, Session $session, Authenticator $authenticator, $method)
    {
        $this->request       = $request;
        $this->response      = $response;
        $this->session       = $session;
        $this->authenticator = $authenticator;

        $this->init();

        if (!method_exists($this, $method)) {
            throw new ControllerException(get_class($this) . ': Method ' . $method . ' does not exist');
        }

        if ($this->isAllowed($method)) {
            return $this->$method();
        }
    }
}
?>