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
    protected $defaultController = 'main.index';

    /**
     * @var string
     */
    protected $authenticationController = 'authentication.login';

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array
     */
    protected $map = array();

    /**
     * Construct the Router.
     *
     * @param array $map Controller/Action map
     */
    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * Helper method for getClass() and getMethod() that
     * either returns the class or the method name.
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
     * Add a mapping of a controller name to the class/method to call.
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
     * @return string
     */
    public function getController()
    {
        if (is_null($this->controller)) {
            throw new RouterException('Route has not been calculated yet. Call route() first.');
        }

        return $this->controller;
    }

    /**
     * Returns the class name for given controller name.
     * Must not be called before route() has been called.
     *
     * @param string $controller Controller name
     * @return string
     */
    public function getClassName($controller)
    {
        return $this->doGet($controller, true);
    }

    /**
     * Returns the class name for given controller name.
     * Must not be called before route() has been called.
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
     * @return void
     */
    public function route($request)
    {
        $this->controller = $this->getDefaultController();

        if ($request->hasGet('mvc_controller')) {
            $this->controller = $request->get('mvc_controller');
        }

        if ($request->hasPost('mvc_controller')) {
            $this->controller = $request->post('mvc_controller');
        }
    }
}
?>