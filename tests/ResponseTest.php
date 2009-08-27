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
 * Unit Tests for Response class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers spriebsch\MVC\Response::getStatus
     */
    public function testHttpStatusIs200()
    {
        $response = new Response();
        $this->assertEquals(200, $response->getStatus());
    }

    /**
     * @covers spriebsch\MVC\Response::setStatus
     * @covers spriebsch\MVC\Response::getStatus
     */
    public function testHttpStatusAccessors()
    {
        $response = new Response();
        $response->setStatus(404);
        $this->assertEquals(404, $response->getStatus());
    }

    /**
     * @covers spriebsch\MVC\Response::getCharacterSet
     */
    public function testCharacterSetIsUTF8()
    {
        $response = new Response();
        $this->assertEquals('UTF-8', $response->getCharacterSet());
    }

    /**
     * @covers spriebsch\MVC\Response::setCharacterSet
     * @covers spriebsch\MVC\Response::getCharacterSet
     */
    public function testCharacterSetAccessors()
    {
        $response = new Response();
        $response->setCharacterSet('iso8859-15');
        $this->assertEquals('iso8859-15', $response->getCharacterSet());
    }

    /**
     * @covers spriebsch\MVC\Response::getContentType
     */
    public function testContentTypeIsTextHtml()
    {
        $response = new Response();
        $this->assertEquals('text/html', $response->getContentType());
    }

    /**
     * @covers spriebsch\MVC\Response::setContentType
     * @covers spriebsch\MVC\Response::getContentType
     */
    public function testContentTypeAccessors()
    {
        $response = new Response();
        $response->setContentType('text/json');
        $this->assertEquals('text/json', $response->getContentType());
    }

    /**
     * @covers spriebsch\MVC\Response::hasData
     */
    public function testHasDataReturnsFalseForNonExistingKey()
    {
        $response = new Response();
        $this->assertFalse($response->hasData('nonexisting'));
    }

    /**
     * @covers spriebsch\MVC\Response::hasData
     */
    public function testHasDataReturnsTrueForExistingKey()
    {
        $response = new Response();
        $response->setData('key', 'value');
        $this->assertTrue($response->hasData('key'));
    }

    /**
     * @covers spriebsch\MVC\Response::getData
     */
    public function testGetDataReturnsEmptyStringForNonExistingKey()
    {
        $response = new Response();
        $this->assertEquals('', $response->getData('nonexisting'));
    }

    /**
     * @covers spriebsch\MVC\Response::setData
     * @covers spriebsch\MVC\Response::getData
     */
    public function testGetDataAccessors()
    {
        $response = new Response();
        $response->setData('key', 'value');
        $this->assertEquals('value', $response->getData('key'));
    }

    /**
     * @covers spriebsch\MVC\Response::setViewName
     * @covers spriebsch\MVC\Response::getViewName
     */
    public function testViewNameAccessors()
    {
        $response = new Response();
        $response->setViewName('viewname');
        $this->assertEquals('viewname', $response->getViewName());
    }
}
?>