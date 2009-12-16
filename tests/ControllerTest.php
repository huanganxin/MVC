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
 * Unit Tests for Controller class.
 * Since Controller is abstract, we test through concrete subclasses.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    protected $response;
    protected $session;
    protected $authenticator;

    protected function setUp()
    {
        $this->request     = $this->getMock('spriebsch\MVC\Request');
        $this->response    = $this->getMock('spriebsch\MVC\Response');
        $this->session     = $this->getMock('spriebsch\MVC\Session');
        $this->authAdapter = $this->getMock('spriebsch\MVC\authenticationAdapter', array(), array(), '', false, false);
    }

    /**
     * @expectedException spriebsch\MVC\ControllerException
     */
    public function testExecuteThrowsExceptionWhenActionMethodDoesNotExist()
    {
        $controller = new Test\DefaultActionController();
        $controller->execute($this->request, $this->response, $this->session, $this->authAdapter, 'nonsense');
    }

    /**
     * Make sure that the requested controller method is called.
     *
     * @expectedException spriebsch\MVC\Test\SomeActionExecutedException
     */
    public function testExecuteCallsGivenAction()
    {
        $controller = new Test\TwoActionsController();
        $controller->execute($this->request, $this->response, $this->session, $this->authAdapter, 'second');
    }

    /**
     * Test through a subclass of Controller with a isAllowed() method that
     * always returns false.
     *
     * @covers spriebsch\MVC\Controller::execute
     */
    public function testNotAllowedController()
    {
        $controller = new Test\NotAllowedController();
        $controller->execute($this->request, $this->response, $this->session, $this->authAdapter, 'actionMethod');
    }
}
?>