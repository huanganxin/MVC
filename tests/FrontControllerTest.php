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
 * Unit Tests for FrontController class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configure ACL to deny everything, thus the request must be dispatched
     * to the authentication controller.
     * 
     * @covers spriebsch\MVC\FrontController
     */
    public function testExecute()
    {
        $this->request           = $this->getMock('spriebsch\MVC\Request',               array(), array(), '', false, false);
        $this->response          = $this->getMock('spriebsch\MVC\Response',              array(), array(), '', false, false);
        $this->session           = $this->getMock('spriebsch\MVC\Session',               array(), array(), '', false, false);
        $this->authAdapter       = $this->getMock('spriebsch\MVC\AuthenticationAdapter', array(), array(), '', false, false);
        $this->controllerFactory = $this->getMock('spriebsch\MVC\ControllerFactory',     array(), array(), '', false, false);
        $this->viewFactory       = $this->getMock('spriebsch\MVC\ViewFactory',           array(), array(), '', false, false);
        $this->acl               = $this->getMock('spriebsch\MVC\Acl',                   array(), array(), '', false, false);
        $this->appController     = $this->getMock('spriebsch\MVC\ApplicationController', array(), array(), '', false, false);
        $this->view              = $this->getMock('spriebsch\MVC\View',                  array(), array(), '', false, false);
        $this->controller        = $this->getMock('spriebsch\MVC\Controller',            array(), array(), '', false, false);


        // Controller's execute method must be called once and return 'success'.
        $this->controller->expects($this->once())
                          ->method('execute')
                          ->will($this->returnValue('success'));

        // View's render method must be called once. 
        $this->view->expects($this->once())
                    ->method('render');
                    // ->with($this->equalTo());
// @todo request and response as parameters

        // The controller factory returns the controller object.
        $this->controllerFactory->expects($this->once())
                                ->method('getController')
                                ->will($this->returnValue($this->controller));

        // The application controller returns a controller name, class, and
        // method that we ignore anyway.
        $this->appController->expects($this->once())
                            ->method('getControllerName')
                            ->will($this->returnValue('controller'));

        $this->appController->expects($this->once())
                            ->method('getClass')
                            ->will($this->returnValue('class'));

        $this->appController->expects($this->once())
                            ->method('getMethod')
                            ->will($this->returnValue('method'));

        $this->appController->expects($this->once())
                            ->method('getView')
                            ->will($this->returnValue($this->view));

        
        $fc = new FrontController($this->request, $this->response, $this->session, $this->authAdapter, $this->acl, $this->appController, $this->controllerFactory);
        $fc->execute();
    }
}
?>
