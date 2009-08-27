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
require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

/**
 * Unit Tests for Router class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');
        Loader::registerPath(__DIR__ . '/_testdata/Controller');
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    public function testRoutesToDefaultController()
    {
        $request = new Request();
        $router = new Router($request);
        $this->assertEquals('\spriebsch\MVC\Default', $router->getControllerClass());
    }

    public function testRouterSelectsControllerFromGetRequest()
    {
        $request = new Request(array('mvc_controller' => 'something'));
        $router = new Router($request);
        $this->assertEquals('\spriebsch\MVC\Something', $router->getControllerClass());
    }

    public function testRouterSelectsControllerFromPostRequest()
    {
        $request = new Request(array(), array('mvc_controller' => 'something'));
        $router = new Router($request);
        $this->assertEquals('\spriebsch\MVC\Something', $router->getControllerClass());
    }

    public function testControllerSelectedByPostOverridesGet()
    {
        $request = new Request(array('mvc_controller' => 'wrong'), array('mvc_controller' => 'something'));
        $router = new Router($request);
        $this->assertEquals('\spriebsch\MVC\Something', $router->getControllerClass());
    }

    public function testRoutesToDefaultAction()
    {
        $request = new Request();
        $router = new Router($request);
        $this->assertEquals('default', $router->getAction());
    }

    public function testRouterSelectsActionFromGetRequest()
    {
        $request = new Request(array('mvc_action' => 'something'));
        $router = new Router($request);
        $this->assertEquals('something', $router->getAction());
    }

    public function testRouterSelectsActionFromPostRequest()
    {
        $request = new Request(array(), array('mvc_action' => 'something'));
        $router = new Router($request);
        $this->assertEquals('something', $router->getAction());
    }

    public function testActionSelectedByPostOverridesGet()
    {
        $request = new Request(array('mvc_action' => 'wrong'), array('mvc_action' => 'something'));
        $router = new Router($request);
        $this->assertEquals('something', $router->getAction());
    }

    public function testGetAuthenticationControllerClass()
    {
        $router = new Router(new Request());
        $this->assertEquals('\spriebsch\MVC\Authentication', $router->getAuthenticationControllerClass());
    }

    public function testGetAuthenticationActionClass()
    {
        $router = new Router(new Request());
        $this->assertEquals('default', $router->getAuthenticationAction());
    }
}
?>