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

    /**
     * Redirect to another controller
     *
     * @param string $controller The controller name
     * @return null
     */
    public function setRedirect($controller)
    {
        $this->redirectController = $controller;
    }

    /**
     * Returns whether a redirect is pending
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->redirectController !== null;
    }

    /**
     * Returns the controller name to redirect to.
     *
     * @returns string
     */
    public function getRedirectController()
    {
        return $this->redirectController;
    }

    /**
     * Add an error message.
     *
     * @param spriebsch\MVC\Message\Error $error The error message
     * @return null
     */
    public function addError(\spriebsch\MVC\Message\Error $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Checks whether there were errors.
     *
     * @return int
     */
    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
    }

    /**
     * Returns the errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add a form error.
     *
     * @param spriebsch\MVC\Message\FormError $error The error message
     * @return null
     */
    public function addFormError(\spriebsch\MVC\Message\FormError $error)
    {
        $this->formErrors[$error->getFormName()][] = $error;
    }

    /**
     * Checks whether there were errors for a given form.
     *
     * @param string $formName Form name
     * @return bool
     */
    public function hasFormErrors($formName)
    {
        return isset($this->formErrors[$formName]) && (sizeof($this->formErrors[$formName]) > 0);
    }

    /**
     * Returns the errors for a given form.
     *
     * @param string $formName Form name
     * @return array
     */
    public function getFormErrors($formName)
    {
        if (!isset($this->formErrors[$formName])) {
            return array();
        }

        return $this->formErrors[$formName];
    }

    /**
     * Add a form field error
     *
     * @param spriebsch\MVC\Message\FieldError $error The error object
     * @return null
     */
    public function addFieldError(\spriebsch\MVC\Message\FieldError $error)
    {
        $this->fieldErrors[$error->getFormName()][$error->getFieldName()][] = $error;
    }

    /**
     * Checks whether a given form field has errors
     *
     * @param string $formName The form name
     * @param string $fieldName The field name
     * @return bool
     */
    public function hasFieldErrors($formName, $fieldName)
    {
        return isset($this->fieldErrors[$formName]) && isset($this->fieldErrors[$formName][$fieldName]) && (sizeof($this->fieldErrors[$formName][$fieldName]) > 0);
    }

    /**
     * Checks whether given form field has errors.
     *
     * @param string $formName The form name
     * @param string $fieldName The field name
     * @return array
     */
    public function getFieldErrors($formName, $fieldName)
    {
        if (!isset($this->fieldErrors[$formName]) || !isset($this->fieldErrors[$formName][$fieldName])) {
            return array();
        }

        return $this->fieldErrors[$formName][$fieldName];
    }

    /**
     * Set a value for a given form field.
     *
     * @param string $formName The form name
     * @param string $fieldName The field name
     * @param mixed $value The field value
     * @return null
     */
    public function setFormValue($formName, $fieldName, $value)
    {
        $this->formValues[$formName][$fieldName] = $value;
    }

    /**
     * Checks whether given form field has a value.
     *
     * @param string $formName The form name
     * @param string $fieldName The field name
     * @return bool
     */
    public function hasFormValue($formName, $fieldName)
    {
        return isset($this->formValues[$formName]) && isset($this->formValues[$formName][$fieldName]);
    }

    /**
     * Return form value for a given field.
     *
     * @param string $formName The form name
     * @param string $fieldName The field name
     * @return mixed
     */
    public function getFormValue($formName, $fieldName)
    {
        if (!isset($this->formValues[$formName]) || !isset($this->formValues[$formName][$fieldName])) {
            return '';
        }

        return $this->formValues[$formName][$fieldName];
    }
    
    public function getForms()
    {
    	return array_keys($this->formValues);
    }

    public function getFormValues($formName)
    {
        return $this->formValues[$formName];
    }
    
    /**
     * Adds a cookie
     *
     * @param sting $name Cookie name
     * @param string $value Cookie value
     * @param int $expire Expiration
     * @param string $path The cookie path
     * @param string $domain The cookie domain
     * @param bool $secure Whether the cookie is secure
     * @param bool $httpOnly Whether the cookie is HTTPOnly
     * @return null
     * @todo rename to addCookie
     */
    public function setCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        $this->cookies[] = array($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Checks whether there are cookies.
     *
     * @return bool
     */
    public function hasCookies()
    {
        return sizeof($this->cookies) > 0;
    }

    /**
     * Returns an array with all cookies.
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }
}
?>