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

require_once 'PHPUnit/Framework.php';

/**
 * Unit Tests for Router class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers spriebsch\MVC\Router::getController
     * @expectedException spriebsch\MVC\Exception
     */
    public function testThrowsExceptionWhenGettingControllerBeforeRouting()
    {
        $router  = new Router();
        $request = new Request();

        $this->assertEquals('main.index', $router->getController());
    }

    /**
     * @covers spriebsch\MVC\Router::route
     * @covers spriebsch\MVC\Router::getController
     */
    public function testRoutesToDefaultController()
    {
        $router  = new Router();
        $request = new Request();

        $router->route($request);

        $this->assertEquals('main.index', $router->getController());
    }

    /**
     * @covers spriebsch\MVC\Router::registerController
     * @covers spriebsch\MVC\Router::getClassName
     * @covers spriebsch\MVC\Router::getMethodName
     */
    public function testTranslatesControllerToClassAndMethodName()
    {
        $router  = new Router();
        $router->registerController('main.index', 'controller', 'method');

        $this->assertEquals('controller', $router->getClassName('main.index'));
        $this->assertEquals('method', $router->getMethodName('main.index'));
    }

    /**
     * @covers spriebsch\MVC\Router::setDefaultController
     * @covers spriebsch\MVC\Router::getDefaultController
     */
    public function testControllerAccessors()
    {
        $router  = new Router();
        $router->setDefaultController('default');

        $this->assertEquals('default', $router->getDefaultController());
    }

    /**
     * @covers spriebsch\MVC\Router::setAuthenticationController
     * @covers spriebsch\MVC\Router::getAuthenticationController
     */
    public function testAuthenticationControllerAccessors()
    {
        $router  = new Router();
        $router->setAuthenticationController('default');

        $this->assertEquals('default', $router->getAuthenticationController());
    }

    /**
     * @covers spriebsch\MVC\Router::route
     * @covers spriebsch\MVC\Router::getController
     */
    public function testSelectsControllerFromGetVariables()
    {
        $router  = new Router();
        $request = new Request(array('mvc_controller' => 'controller'));

        $router->route($request);

        $this->assertEquals('controller', $router->getController());
    }

    /**
     * @covers spriebsch\MVC\Router::route
     * @covers spriebsch\MVC\Router::getController
     */
    public function testSelectsControllerFromPostVariables()
    {
        $router  = new Router();
        $request = new Request(array(), array('mvc_controller' => 'controller'));

        $router->route($request);

        $this->assertEquals('controller', $router->getController());
    }

    /**
     * @covers spriebsch\MVC\Router::route
     * @covers spriebsch\MVC\Router::getController
     */
    public function testPostOverridesGet()
    {
        $router  = new Router();
        $request = new Request(array('mvc_controller' => 'nonsense'), array('mvc_controller' => 'controller'));

        $router->route($request);

        $this->assertEquals('controller', $router->getController());
    }
}
?>