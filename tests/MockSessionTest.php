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
 * Unit Tests for MockSession class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class MockSessionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->session = new MockSession();
    }

    /**
     * @expectedException spriebsch\MVC\SessionException
     * @covers spriebsch\MVC\MockSession::get
     */
    public function testGetThrowsExceptionForUnknownVariable()
    {
        $this->session->get('nonsense');
    }

    /**
     * @covers spriebsch\MVC\MockSession::get
     * @covers spriebsch\MVC\MockSession::set
     */
    public function testSetAndGet()
    {
        $this->session->set('key', 'value');
        $this->assertEquals('value', $this->session->get('key'));
    }

    /**
     * @covers spriebsch\MVC\MockSession::has
     */
    public function testHasReturnsFalseWhenKeyDoesNotExist()
    {
        $this->assertFalse($this->session->has('key'));
    }

    /**
     * @covers spriebsch\MVC\MockSession::has
     */
    public function testHasReturnsTrueWhenKeyExists()
    {
        $this->session->set('key', 'value');
        $this->assertTrue($this->session->has('key'));
    }

    /**
     * @covers spriebsch\MVC\MockSession::setName
     * @covers spriebsch\MVC\MockSession::getName
     */
    public function testSetAndGetName()
    {
        $this->session->setName('something');
        $this->assertEquals('something', $this->session->getName());
    }

    /**
     * @covers spriebsch\MVC\MockSession::isStarted
     */
    public function testIsStartedReturnsFalseWhenSessionWasNotStarted()
    {
        $this->assertFalse($this->session->isStarted());
    }

    /**
     * @covers spriebsch\MVC\MockSession::isStarted
     */
    public function testIsStartedReturnsTrueWhenSessionWasStarted()
    {
        $this->session->start();
        $this->assertTrue($this->session->isStarted());
    }

    /**
     * @expectedException spriebsch\MVC\SessionException
     * @covers spriebsch\MVC\MockSession::getId
     */
    public function testGetIdThrowsExceptionWhenSessionNotStarted()
    {
        $this->session->getId();
    }

    /**
     * Since we can't really check the session ID,
     * we just make sure it is 26 characters long.
     *
     * @covers spriebsch\MVC\MockSession::getId
     */
    public function testGetIdReturnsSessionId()
    {
        $this->session->start();
        $id = $this->session->getId();

        $this->assertEquals(26, strlen($id));
    }

    /**
     * @covers spriebsch\MVC\MockSession::regenerateId
     */
    public function testRegenerateIdChangesSessionId()
    {
        $this->session->start();
        $id = $this->session->getId();
        $this->session->regenerateId();
        $this->assertNotEquals($id, $this->session->getId());
    }

    public function testMockSessionDoesNotStartPhpSession()
    {
        $this->session = new MockSession();
        $this->session->start();
        $this->assertEquals('', session_id());
        $this->assertFalse(isset($_SESSION));
    }
}
?>