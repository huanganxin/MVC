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
 * Unit Tests for Application Controller class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ApplicationControllerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Make sure that default controller is selected when request does
	 * not specify any controllers. ACL must allow the default controller, 
	 * otherwise authentication controller will be selected.
	 * 
	 * @covers spriebsch\MVC\ApplicationController::__construct
     * @covers spriebsch\MVC\ApplicationController::getControllerName
	 */
	public function testGetControllerNameByDefaultReturnsDefaultController()
	{
		// Mock ACL that allows everything.
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->acl->expects($this->any())
                  ->method('isAllowed')
                  ->will($this->returnValue(true));
                  
        // Mock session with "anonymous" user role (_MVC_USER_ROLE session value). 
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
        $this->session->expects($this->any())
                      ->method('has')
                      ->will($this->returnValue(true));

        $this->session->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue('anonymous'));

        $this->request = $this->getMock('spriebsch\MVC\Request', array(), array(), '', false, false);

        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->assertEquals('main', $this->appController->getControllerName($this->request));
    }

    /**
     * Make sure that authentication controller is selected when given
     * controller is denied by ACL.  
     * 
     * @covers spriebsch\MVC\ApplicationController::getControllerName
     */
    public function testGetControllerNameReturnsAuthenticationControllerOnDeniedController()
    {
        // Mock ACL that denies everything
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->acl->expects($this->any())
                  ->method('isAllowed')
                  ->will($this->returnValue(false));
                  
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
        $this->request = $this->getMock('spriebsch\MVC\Request', array(), array(), '', false, false);
                              
        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->assertEquals('authentication.login', $this->appController->getControllerName($this->request));
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::registerController
     * @covers spriebsch\MVC\ApplicationController::getControllerName
     */
    public function testGetControllerNameReturnsControllerSelectedByGet()
    {
        // Mock ACL that allows everything
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->acl->expects($this->any())
                  ->method('isAllowed')
                  ->will($this->returnValue(true));

        // Mock session with "anonymous" user role (_MVC_USER_ROLE session value). 
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
        $this->session->expects($this->any())
                      ->method('has')
                      ->will($this->returnValue(true));

        $this->session->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue('anonymous'));
        
        $this->request = new Request(array('mvc_controller' => 'something'));
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerController('something', 'class', 'method');

        $this->assertEquals('something', $this->appController->getControllerName($this->request));
    }
    
    /**
     * @covers spriebsch\MVC\ApplicationController::registerController
     * @covers spriebsch\MVC\ApplicationController::getControllerName
     */
    public function testGetControllerNameReturnsControllerSelectedByPost()
    {
        // Mock ACL that allows everything
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->acl->expects($this->any())
                  ->method('isAllowed')
                  ->will($this->returnValue(true));

        // Mock session with "anonymous" user role (_MVC_USER_ROLE session value). 
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
        $this->session->expects($this->any())
                      ->method('has')
                      ->will($this->returnValue(true));

        $this->session->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue('anonymous'));
        
        $this->request = new Request(array(), array('mvc_controller' => 'something'));
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerController('something', 'class', 'method');

        $this->assertEquals('something', $this->appController->getControllerName($this->request));
    }    

    /**
     * @covers spriebsch\MVC\ApplicationController::registerController
     * @covers spriebsch\MVC\ApplicationController::getControllerName
     */
    public function testGetControllerNamePrefersPostOverGet()
    {
        // Mock ACL that allows everything
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->acl->expects($this->any())
                  ->method('isAllowed')
                  ->will($this->returnValue(true));

        // Mock session with "anonymous" user role (_MVC_USER_ROLE session value). 
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
        $this->session->expects($this->any())
                      ->method('has')
                      ->will($this->returnValue(true));

        $this->session->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue('anonymous'));
        
        $this->request = new Request(array('mvc_controller' => 'nonsense'), array('mvc_controller' => 'something'));
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerController('something', 'class', 'method');

        $this->assertEquals('something', $this->appController->getControllerName($this->request));
    }    

    /**
     * @covers spriebsch\MVC\ApplicationController::getView
     * @expectedException spriebsch\MVC\Exception
     */
    public function testGetViewThrowsExceptionWhenNoDefaultViewIsSet()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->appController->getView('something', 'success');
    }
    
    /**
     * @covers spriebsch\MVC\ApplicationController::registerView
     * @covers spriebsch\MVC\ApplicationController::setDefaultView
     * @covers spriebsch\MVC\ApplicationController::getView
     */
    public function testGetViewReturnsRegisteredViewAndViewScript()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->view = new View(__DIR__);
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->setDefaultView($this->view);
        $this->appController->registerView('something', 'success', 'a_view');

        $this->assertEquals($this->view, $this->appController->getView('something', 'success'));
        $this->assertEquals('a_view', $this->appController->getView('something', 'success')->getViewScript());
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::getView
     * @expectedException spriebsch\MVC\Exception
     */
    public function testGetViewThrowsExceptionWhenNoViewScriptIsSet()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->view = new View(__DIR__);
                
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->setDefaultView($this->view);

        $this->appController->getView('something', 'success');
    }
    
    /**
     * @covers spriebsch\MVC\ApplicationController::registerRedirect
     * @covers spriebsch\MVC\ApplicationController::setDefaultView
     * @covers spriebsch\MVC\ApplicationController::getView
     */
    public function testGetViewHonorsRedirect()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->view = new View(__DIR__);
        
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->setDefaultView($this->view);
        $this->appController->registerRedirect('something', 'success', 'a_controller');

        $this->assertEquals('a_controller', $this->appController->getView('something', 'success')->getRedirect());
    }
    
    /**
     * @covers spriebsch\MVC\ApplicationController::isRedirect
     */
    public function testIsRedirectInitiallyReturnsFalse()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
                      
        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->assertFalse($this->appController->isRedirect('something', 'success'));
    }
    
    /**
     * @covers spriebsch\MVC\ApplicationController::registerRedirect
     * @covers spriebsch\MVC\ApplicationController::isRedirect
     * @covers spriebsch\MVC\ApplicationController::getRedirect
     */
    public function testRedirectAccessors()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->view = new View(__DIR__);
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerRedirect('something', 'success', 'a_controller');

        $this->assertTrue($this->appController->isRedirect('something', 'success'));
        $this->assertEquals('a_controller', $this->appController->getRedirect('something', 'success'));
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::isForward
     */
    public function testIsForwardInitiallyReturnsFalse()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
                      
        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->assertFalse($this->appController->isForward('something', 'success'));
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::registerForward
     * @covers spriebsch\MVC\ApplicationController::isForward
     * @covers spriebsch\MVC\ApplicationController::getForward
     */
    public function testForwardAccessors()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);
                      
        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerForward('something', 'success', 'a_controller');

        $this->assertTrue($this->appController->isForward('something', 'success'));
        $this->assertEquals('a_controller', $this->appController->getForward('something', 'success'));
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::registerController
     * @covers spriebsch\MVC\ApplicationController::getClass
     * @covers spriebsch\MVC\ApplicationController::getMethod
     * @covers spriebsch\MVC\ApplicationController::doGet
     */
    public function testTranslatesControllerToClassAndMethodName()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->appController = new ApplicationController($this->session, $this->acl);
        $this->appController->registerController('main', 'controller', 'method');

        $this->assertEquals('controller', $this->appController->getClass('main'));
        $this->assertEquals('method', $this->appController->getMethod('main'));
    }

    /**
     * @covers spriebsch\MVC\ApplicationController::getClass
     * @covers spriebsch\MVC\ApplicationController::getMethod
     * @covers spriebsch\MVC\ApplicationController::doGet
     * @expectedException spriebsch\MVC\Exception
     */
    public function testThrowsExceptionWhenControllerNotRegistered()
    {
        $this->acl = $this->getMock('spriebsch\MVC\Acl', array(), array(), '', false, false);
        $this->session = $this->getMock('spriebsch\MVC\Session', array(), array(), '', false, false);

        $this->appController = new ApplicationController($this->session, $this->acl);

        $this->appController->getClass('main');
    }
}
?>