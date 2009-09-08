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
 * The Router decides which controller and action to invoke.
 * It uses the request variables mvc_controller and mvc_action, with fallback
 * values default for both.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Router
{
    /**
     * @var string
     */
    protected $controllerNamespace = '\\spriebsch\\MVC\\Controller';

    /**
     * @var string
     */
    protected $defaultControllerName = 'standard';

    /**
     * @var string
     */
    protected $defaultAction = 'default';

    /**
     * @var string
     */
    protected $authenticationControllerName = 'authentication';

    /**
     * @var string
     */
    protected $authenticationAction = 'default';

    /**
     * @var Request
     */
    protected $request;

    /**
     * Construct the Router.
     *
     * @param Request $request Request object
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the class name for given controller.
     *
     * @param string $controller
     * @return string
     */
    protected function getClassname($controller)
    {
        return $this->controllerNamespace . '\\' . ucfirst($controller);
    }

    /**
     * Set Controller Namespace.
     * All controller classes must be in this namespace.
     *
     * @param string $namespace
     * @return void
     */
    public function setControllerNamespace($namespace)
    {
        $this->controllerNamespace = $namespace;
    }

    public function setDefaultControllerName($controller)
    {
        $this->defaultControllerName = $controller;
    }

    public function setDefaultAction($action)
    {
        $this->defaultAction = $action;
    }

    public function setAuthenticationControllerName($controller)
    {
        $this->authenticationControllerName = $controller;
    }

    public function setAuthenticationAction($action)
    {
        $this->authenticationAction = $action;
    }

    /**
     * Returns the name of the authentication controller.
     *
     * @return string
     */
    public function getAuthenticationControllerClass()
    {
        return $this->getClassname($this->authenticationControllerName);
    }

    /**
     * Returns the action name of the authentication controller action.
     *
     * @return string
     */
    public function getAuthenticationAction()
    {
        return $this->authenticationAction;
    }

    /**
     * Get controller class.
     * The default router uses the mvc_controller URL parameter
     * and falls back to 'Default' if it not present.
     *
     * @return string
     */
    public function getControllerClass()
    {
        $controller = $this->defaultControllerName;

        if ($this->request->hasGet('mvc_controller')) {
            $controller = $this->request->get('mvc_controller');
        }

        if ($this->request->hasPost('mvc_controller')) {
            $controller = $this->request->post('mvc_controller');
        }

        return $this->getClassname($controller);
    }

    /**
     * Get action. The default router uses the eno_action URL parameter
     * and falls back to 'Default' if it not present.
     *
     * @return string
     */
    public function getAction()
    {
        $action = $this->defaultAction;

        if ($this->request->hasGet('mvc_action')) {
            $action = $this->request->get('mvc_action');
        }

        if ($this->request->hasPost('mvc_action')) {
            $action = $this->request->post('mvc_action');
        }

        return $action;
    }
}
?>