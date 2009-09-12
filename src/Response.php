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
 * A data object to collect all output data.
 * Used to transfer data from controllers to the views.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Response
{
    /**
     * @var integer
     */
    protected $httpStatus = 200;

    /**
     * @var string
     */
    protected $characterSet = 'UTF-8';

    /**
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * @var string
     */
    protected $viewName = 'default';

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var array
     */
    protected $formErrors = array();

    /**
     * @var array
     */
    protected $fieldErrors = array();

    /**
     * @var string
     */
    protected $redirectController;

    /**
     * @var array
     */
    protected $formValues = array();

    /**
     * @var array
     */
    protected $cookies = array();

    /**
     * Sets the HTTP status.
     *
     * @param integer $status HTTP status
     * @return null
     */
    public function setStatus($status)
    {
        $this->httpStatus = $status;
    }

    /**
     * Returns the HTTP status.
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Set the character set.
     *
     * @param string $charSet Character set
     * @return null
     */
    public function setCharacterSet($characterSet)
    {
        $this->characterSet = $characterSet;
    }

    /**
     * Returns the character set.
     *
     * @return string
     */
    public function getCharacterSet()
    {
        return $this->characterSet;
    }

    /**
      * Sets the content type (MIME type).
     *
     * @param string $contentType MIME type
     * @return null
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Returns the content type (MIME type).
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets the view name.
     *
     * @param string $viewName View name
     * @return null
     */
    public function setViewName($viewName)
    {
        $this->viewName = $viewName;
    }

    /**
     * Returns the view name.
     *
     * @return string
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * Check wether data is available for a given key
     *
     * @param string $key Key
     * @return mixed
     */
    public function hasData($key)
    {
       return isset($this->data[$key]);
    }

    /**
     * Set data to be displayed in the view
     *
     * @param string $key   Key
     * @param mixed  $value Value
     * @return null
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
    * Returns data to be displayed in the view
    *
    * @param string $key Key
    * @return mixed
    */
    public function getData($key)
    {
        if (!isset($this->data[$key])) {
            return '';
        }

        return $this->data[$key];
    }

    public function setRedirect($controller)
    {
        $this->redirectController = $controller;
    }

    public function isRedirect()
    {
        return $this->redirectController !== null;
    }

    public function getRedirectController()
    {
        return $this->redirectController;
    }

    /**
     * Add an error message.
     */
    public function addError(\spriebsch\MVC\Message\Error $error)
    {
        $this->errors[] = $error;
    }

    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addFormError(\spriebsch\MVC\Message\FormError $error)
    {
        $this->formErrors[$error->getFormName()][] = $error;
    }

    public function hasFormErrors($formName)
    {
        return isset($this->formErrors[$formName]) && (sizeof($this->formErrors[$formName]) > 0);
    }

    public function getFormErrors($formName)
    {
        if (!isset($this->formErrors[$formName])) {
            return array();
        }

        return $this->formErrors[$formName];
    }

    public function addFieldError(\spriebsch\MVC\Message\FieldError $error)
    {
        $this->fieldErrors[$error->getFormName()][$error->getFieldName()][] = $error;
    }

    public function hasFieldErrors($formName, $fieldName)
    {
        return isset($this->fieldErrors[$formName]) && isset($this->fieldErrors[$formName][$fieldName]) && (sizeof($this->fieldErrors[$formName][$fieldName]) > 0);
    }

    public function getFieldErrors($formName, $fieldName)
    {
        if (!isset($this->fieldErrors[$formName]) || !isset($this->fieldErrors[$formName][$fieldName])) {
            return array();
        }

        return $this->fieldErrors[$formName][$fieldName];
    }

    public function setFormValue($formName, $fieldName, $value) {
        $this->formValues[$formName][$fieldName] = $value;
    }

    public function hasFormValue($formName, $fieldName) {
        return isset($this->formValues[$formName]) && isset($this->formValues[$formName][$fieldName]);
    }

    public function getFormValue($formName, $fieldName) {
        if (!isset($this->formValues[$formName]) || !isset($this->formValues[$formName][$fieldName])) {
            return '';
        }

        return $this->formValues[$formName][$fieldName];
    }

    public function setCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        $this->cookies[] = array($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function hasCookies()
    {
        return sizeof($this->cookies) > 0;
    }

    public function getCookies()
    {
        return $this->cookies;
    }
}
?>