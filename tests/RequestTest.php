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
 * Unit Tests for Request class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException spriebsch\MVC\UnknownVariableException
     * @covers spriebsch\MVC\Request
     */
    public function testThrowsExceptionForNonexistingVariable()
    {
        $request = new Request();
        $request->nonsense('key');
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testHasGetReturnsTrueForExistingValue()
    {
        $request = new Request(array('key' => 'value'));
        $this->assertTrue($request->hasGet('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testHasGetReturnsFalseForExistingValue()
    {
        $request = new Request();
        $this->assertFalse($request->hasGet('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testHasGetReturnsFalseForEmptyGetValue()
    {
        $request = new Request(array('key' => ''));
        $this->assertFalse($request->hasGet('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testHasPostReturnsFalseForEmptyGetValue()
    {
        $request = new Request(array(), array('key' => ''));
        $this->assertFalse($request->hasPost('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testGetReturnsExistingValue()
    {
        $request = new Request(array('key' => 'value'));
        $this->assertEquals('value', $request->get('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testGetReturnsEmptyStringForNonExistingValue()
    {
        $request = new Request();
        $this->assertEquals('', $request->get('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testPostReturnsExistingValue()
    {
        $request = new Request(array(), array('key' => 'value'));
        $this->assertEquals('value', $request->post('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testCookieReturnsExistingValue()
    {
        $request = new Request(array(), array(), array('key' => 'value'));
        $this->assertEquals('value', $request->cookie('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testFilesReturnsExistingValue()
    {
        $request = new Request(array(), array(), array(), array('key' => 'value'));
        $this->assertEquals('value', $request->files('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testServerReturnsExistingValue()
    {
        $request = new Request(array(), array(), array(), array(), array('key' => 'value'));
        $this->assertEquals('value', $request->server('key'));
    }

    /**
     * @covers spriebsch\MVC\Request
     */
    public function testEnvReturnsExistingValue()
    {
        $request = new Request(array(), array(), array(), array(), array(), array('key' => 'value'));
        $this->assertEquals('value', $request->env('key'));
    }
}
?>