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
 * Unit Tests for Session class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 *
 * @runTestsInSeparateProcesses
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');

        $this->session = new Session();
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @expectedException spriebsch\MVC\SessionException
     * @covers spriebsch\MVC\Session::get
     */
    public function testGetThrowsExceptionForUnknownVariable()
    {
        $this->session->get('nonsense');
    }

    /**
     * @covers spriebsch\MVC\Session::get
     * @covers spriebsch\MVC\Session::set
     */
    public function testSetAndGet()
    {
        $this->session->set('key', 'value');
        $this->assertEquals('value', $this->session->get('key'));
    }

    /**
     * @covers spriebsch\MVC\Session::has
     */
    public function testHasReturnsFalseWhenKeyDoesNotExist()
    {
        $this->assertFalse($this->session->has('key'));
    }

    /**
     * @covers spriebsch\MVC\Session::has
     */
    public function testHasReturnsTrueWhenKeyExists()
    {
        $this->session->set('key', 'value');
        $this->assertTrue($this->session->has('key'));
    }

    /**
     * @covers spriebsch\MVC\Session::setName
     * @covers spriebsch\MVC\Session::getName
     */
    public function testSetAndGetName()
    {
        $this->session->setName('something');
        $this->assertEquals('something', $this->session->getName());
    }

    /**
     * @covers spriebsch\MVC\Session::isStarted
     * @covers spriebsch\MVC\Session::checkIfStarted
     */
    public function testIsStartedReturnsFalseWhenSessionWasNotStarted()
    {
        $this->assertFalse($this->session->isStarted());
    }

    /**
     * @covers spriebsch\MVC\Session::start
     * @covers spriebsch\MVC\Session::isStarted
     * @covers spriebsch\MVC\Session::checkIfStarted
     */
    public function testIsStartedReturnsTrueWhenSessionWasStarted()
    {
        $this->session->start();

        $this->assertTrue($this->session->isStarted());
    }

    /**
     * @covers spriebsch\MVC\Session::start
     * @covers spriebsch\MVC\Session::getId
     * @covers spriebsch\MVC\Session::checkIfStarted
     */
    public function testGetIdReturnsSessionId()
    {
        $this->session->start();

        $this->assertEquals(session_id(), $this->session->getId());
    }

    /**
     * @expectedException spriebsch\MVC\SessionException
     * @covers spriebsch\MVC\Session::checkIfStarted
     * @covers spriebsch\MVC\Session::getId
     */
    public function testGetIdThrowsExceptionWhenSessionNotStarted()
    {
        $this->session->getId();
    }

    /**
     * @covers spriebsch\MVC\Session::regenerateId
     */
    public function testRegenerateIdChangesSessionId()
    {
        $this->session->start();
        $id = $this->session->getId();
        $this->session->regenerateId();
        $this->assertNotEquals($id, $this->session->getId());
    }
}
?>