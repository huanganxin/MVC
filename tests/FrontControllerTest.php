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
 * Unit Tests for FrontController class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException spriebsch\MVC\Exception
     */
    public function testThrowsExceptionWhenSelectedControllerDoesNotExist()
    {
        $this->request       = new Request(array('mvc_controller' => 'nonexisting'));
        $this->router        = new Router();
        $this->acl           = new Acl();

        $this->response      = $this->getMock('spriebsch\MVC\Response',      array(), array(), '', false, false);
        $this->session       = $this->getMock('spriebsch\MVC\Session',       array(), array(), '', false, false);
        $this->authenticator = $this->getMock('spriebsch\MVC\Authenticator', array(), array(), '', false, false);
        $this->view          = $this->getMock('spriebsch\MVC\View',          array(), array(), '', false, false);

        $fc = new FrontController($this->request, $this->response, $this->session, $this->view, $this->router, $this->authenticator, $this->acl);
        $fc->execute();
    }

    /**
     * Configure ACL to deny everything, thus the request must be dispatched
     * to the authentication controller.
     *
     * @expectedException spriebsch\MVC\Test\FrontController\AuthenticationExecutedException
     */
    public function testSelectsAuthenticationControllerWhenNotAllowed()
    {
        $this->router        = new Router();
        $this->router->registerController('main.index', 'spriebsch\\MVC\\Test\\FrontController\\Action', 'method');
        $this->router->registerController('authentication.login', 'spriebsch\\MVC\\Test\\FrontController\\Authentication', 'method');

        $this->request       = new Request(array('mvc_controller' => 'main.index'));

        $this->acl = new Acl();
        $this->acl->setPolicy(Acl::DENY);

        $this->response      = $this->getMock('spriebsch\MVC\Response',      array(), array(), '', false, false);
        $this->session       = $this->getMock('spriebsch\MVC\Session',       array(), array(), '', false, false);
        $this->authenticator = $this->getMock('spriebsch\MVC\Authenticator', array(), array(), '', false, false);
        $this->view          = $this->getMock('spriebsch\MVC\View',          array(), array(), '', false, false);

        $fc = new FrontController($this->request, $this->response, $this->session, $this->view, $this->router, $this->authenticator, $this->acl);
        $fc->execute();
    }

    /**
     * Instead of mocking the controller and setting up an expectation that
     * its execute() method gets called, we use a controller that throws an
     * exception in execute(). This is to work around a problem in PHPUnit 3.4
     * that prevents us from mocking the controller and assigning a namespaced
     * name to it (which we need, since we cannot inject the mocked controller
     * otherwise).
     *
     * @expectedException spriebsch\MVC\Test\FrontController\DefaultActionExecutedException
     */
    public function testCallsExecuteMethodInController()
    {
        $this->router        = new Router();
        $this->router->registerController('main.index', 'spriebsch\\MVC\\Test\\FrontController\\Action', 'method');
        $this->router->registerController('authentication.login', 'spriebsch\\MVC\\Test\\FrontController\\Authentication', 'method');

        $this->request       = new Request(array('mvc_controller' => 'main.index'));
        $this->acl           = new Acl();

        $this->response      = $this->getMock('spriebsch\MVC\Response',      array(), array(), '', false, false);
        $this->session       = $this->getMock('spriebsch\MVC\Session',       array(), array(), '', false, false);
        $this->authenticator = $this->getMock('spriebsch\MVC\Authenticator', array(), array(), '', false, false);
        $this->view          = $this->getMock('spriebsch\MVC\View',          array(), array(), '', false, false);

        $fc = new FrontController($this->request, $this->response, $this->session, $this->view, $this->router, $this->authenticator, $this->acl);
        $fc->execute();
    }
}
?>