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
 * Unit Tests for Response class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->response = new Response();
    }

    /**
     * @covers spriebsch\MVC\Response::isRedirect
     */
    public function testIsRedirectInitiallyReturnsFalse()
    {
        $this->assertFalse($this->response->isRedirect());
    }

    /**
     * @covers spriebsch\MVC\Response::getStatus
     */
    public function testHttpStatusIs200()
    {
        $this->assertEquals(200, $this->response->getStatus());
    }

    /**
     * @covers spriebsch\MVC\Response::setStatus
     * @covers spriebsch\MVC\Response::getStatus
     */
    public function testHttpStatusAccessors()
    {
        $this->response->setStatus(404);
        $this->assertEquals(404, $this->response->getStatus());
    }

    /**
     * @covers spriebsch\MVC\Response::getCharacterSet
     */
    public function testCharacterSetIsUTF8()
    {
        $this->assertEquals('UTF-8', $this->response->getCharacterSet());
    }

    /**
     * @covers spriebsch\MVC\Response::setCharacterSet
     * @covers spriebsch\MVC\Response::getCharacterSet
     */
    public function testCharacterSetAccessors()
    {
        $this->response->setCharacterSet('iso8859-15');
        $this->assertEquals('iso8859-15', $this->response->getCharacterSet());
    }

    /**
     * @covers spriebsch\MVC\Response::getContentType
     */
    public function testContentTypeIsTextHtml()
    {
        $this->assertEquals('text/html', $this->response->getContentType());
    }

    /**
     * @covers spriebsch\MVC\Response::setContentType
     * @covers spriebsch\MVC\Response::getContentType
     */
    public function testContentTypeAccessors()
    {
        $this->response->setContentType('text/json');
        $this->assertEquals('text/json', $this->response->getContentType());
    }

    /**
     * @covers spriebsch\MVC\Response::hasData
     */
    public function testHasDataReturnsFalseForNonExistingKey()
    {
        $this->assertFalse($this->response->hasData('nonexisting'));
    }

    /**
     * @covers spriebsch\MVC\Response::hasData
     */
    public function testHasDataReturnsTrueForExistingKey()
    {
        $this->response->setData('key', 'value');
        $this->assertTrue($this->response->hasData('key'));
    }

    /**
     * @covers spriebsch\MVC\Response::getData
     */
    public function testGetDataReturnsEmptyStringForNonExistingKey()
    {
        $this->assertEquals('', $this->response->getData('nonexisting'));
    }

    /**
     * @covers spriebsch\MVC\Response::setData
     * @covers spriebsch\MVC\Response::getData
     */
    public function testGetDataAccessors()
    {
        $this->response->setData('key', 'value');
        $this->assertEquals('value', $this->response->getData('key'));
    }

    /**
     * @covers spriebsch\MVC\Response::setViewName
     * @covers spriebsch\MVC\Response::getViewName
     */
    public function testViewNameAccessors()
    {
        $this->response->setViewName('viewname');
        $this->assertEquals('viewname', $this->response->getViewName());
    }

    /**
     * @covers spriebsch\MVC\Response::setRedirect
     * @covers spriebsch\MVC\Response::getRedirectController
     */
    public function testRedirectAccessors()
    {
        $this->response->setRedirect('controller');

        $this->assertEquals('controller', $this->response->getRedirectController());
    }

    /**
     * @covers spriebsch\MVC\Response::hasErrors
     */
    public function testHasErrorsInitiallyReturnsFalse()
    {
        $this->assertFalse($this->response->hasErrors());
    }

    /**
     * @covers spriebsch\MVC\Response::getErrors
     */
    public function testGetErrorsInitiallyReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->response->getErrors());
    }

    /**
     * @covers spriebsch\MVC\Response::hasFormErrors
     */
    public function testHasFormErrorsInitiallyReturnsFalse()
    {
        $this->assertFalse($this->response->hasFormErrors('form'));
    }

    /**
     * @covers spriebsch\MVC\Response::getFormErrors
     */
    public function testGetFormErrorsInitiallyReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->response->getFormErrors('form'));
    }

    /**
     * @covers spriebsch\MVC\Response::hasFieldErrors
     */
    public function testHasFieldErrorsInitiallyReturnsFalse()
    {
        $this->assertFalse($this->response->hasFieldErrors('form', 'field'));
    }

    /**
     * @covers spriebsch\MVC\Response::getFieldErrors
     */
    public function testGetFieldErrorsInitiallyReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->response->getFieldErrors('form', 'field'));
    }

    /**
     * @covers spriebsch\MVC\Response::addError
     * @covers spriebsch\MVC\Response::getErrors
     */
    public function testErrorsAccessors()
    {
        $error = new \spriebsch\MVC\Message\Error('message');

        $this->response->addError($error);
        $this->assertEquals(array($error), $this->response->getErrors());
    }

    /**
     * @covers spriebsch\MVC\Response::addFormError
     * @covers spriebsch\MVC\Response::getFormErrors
     */
    public function testFormErrorsAccessors()
    {
        $error = new \spriebsch\MVC\Message\FormError('message', 'form');

        $this->response->addFormError($error);
        $this->assertEquals(array($error), $this->response->getFormErrors('form'));
    }

    /**
     * @covers spriebsch\MVC\Response::addFieldError
     * @covers spriebsch\MVC\Response::getFieldErrors
     */
    public function testFieldErrorsAccessors()
    {
        $error = new \spriebsch\MVC\Message\FieldError('message', 'form', 'field');

        $this->response->addFieldError($error);
        $this->assertEquals(array($error), $this->response->getFieldErrors('form', 'field'));
    }

    /**
     * @covers spriebsch\MVC\Response::getCookies
     */
    public function testGetCookiesInitiallyReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->response->getCookies());
    }

    /**
     * @covers spriebsch\MVC\Response::hasCookies
     */
    public function testHasCookiesInitiallyReturnsFalse()
    {
        $this->assertFalse($this->response->hasCookies());
    }

    /**
     * @covers spriebsch\MVC\Response::hasCookies
     */
    public function testHasCookiesReturnsTrueWhenCookiesAreSet()
    {
        $this->response->setCookie('name', 'value', 23, '/some/path', 'domain', true, true);
        $this->assertTrue($this->response->hasCookies());
    }

    /**
     * @covers spriebsch\MVC\Response::setCookie
     * @covers spriebsch\MVC\Response::getCookies
     */
    public function testCookieAccessors()
    {
        $this->response->setCookie('name', 'value', 23, '/some/path', 'domain', true, true);
        $this->assertEquals(array(array('name', 'value', 23, '/some/path', 'domain', true, true)), $this->response->getCookies());
    }

    /**
     * @covers spriebsch\MVC\Response::hasFormValue
     */
    public function testHasFormValueIntiallyReturnsFalse()
    {
        $this->assertFalse($this->response->hasFormValue('form', 'field'));
    }

    /**
     * @covers spriebsch\MVC\Response::getFormValue
     */
    public function testGetFormValueReturnsEmptyStringForNonexistingValue()
    {
        $this->assertEquals('', $this->response->getFormValue('form', 'field'));
    }

    /**
     * @covers spriebsch\MVC\Response::setFormValue
     * @covers spriebsch\MVC\Response::getFormValue
     */
    public function testFormValueAccessors()
    {
        $this->response->setFormValue('form', 'field', 'value');
        $this->assertEquals('value', $this->response->getFormvalue('form', 'field'));
    }
}
?>