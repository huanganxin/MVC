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

class View
{
    /**
     * @var string
     */
    protected $viewName;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $head = 'head.php';

    /**
     * @var string
     */
    protected $foot = 'foot.php';

    /**
     * @var array
     */
    protected $viewHelpers = array('ViewHelper');

    /**
     * Constructs the View object.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Returns the fully qualified class name for a view helper.
     *
     * @param string $viewHelper
     * @return null
     */
    protected function getViewHelperName($viewHelper)
    {
        return '\\spriebsch\\MVC\\ViewHelper\\' . ucfirst($viewHelper);
    }

    /**
     * Sets the cookies
     *
     * @return null
     */
    protected function setCookies()
    {
        if (!$this->response->hasCookies()) {
            return;
        }

        foreach ($this->response->getCookies() as $cookie) {
            list($name, $value, $expire, $path, $domain, $secure, $httpOnly) = $cookie;
            setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    /**
     * Sends the headers
     *
     * @return null
     */
    protected function sendHeaders()
    {
        header('Status: ' . $this->response->getStatus());
        // header('Content-Type: ' . $this->response->getContentType());
// @todo set content type and encoding header
    }

    /**
     * Escapes data for HTML output.
     *
     * @todo make sure charset is okay for escaping (get from response?)
     */
    protected function escapeData($data)
    {
        return htmlentities($this->rawData($data));
    }

    /**
     * Returns raw data from the Response object.
     *
     * @param string $key
     * @return mixed
     */
    protected function rawData($key)
    {
        return $this->response->getData($key);
    }

    /**
     * Calculates the filename for a view script.
     *
     * @return string
     */
    protected function getFilename()
    {
        return $this->directory . '/' . $this->viewName . '.php';
    }

    /**
     * Calculates the head filename for a view script.
     *
     * @return string
     */
    protected function getHeadFilename()
    {
        return $this->directory . '/' . $this->head;
    }

    /**
     * Calculates the foot filename for a view script.
     *
     * @return string
     */
    protected function getFootFilename()
    {
        return $this->directory . '/' . $this->foot;
    }

    /**
     * Apply view helpers to replace __viewhelper.name.parameters__ tags.
     * @todo switch to viewhelper.
     *
     * @param string $body
     * @return string
     */
    protected function postProcess($body)
    {
        preg_match_all('/__(.*)__/U', $body, $matches);

        foreach ($matches[1] as $match) {
            $parts = explode('.', $match);

            $helper = $parts[0];
            $parameters = substr($match, strlen($helper) + 1);

            $replacement = call_user_func_array(array($this, $helper), array($parameters));

            $body = str_replace('__' . $match . '__', $replacement, $body);
        }

        return $body;
    }

    /**
     * Set the head template.
     *
     * @param string $file The (relative) file name
     * @return null
     */
    public function setHead($file)
    {
        $this->head = $file;
    }

    /**
     *
     * @param string $file The (relative) file name
     * @return null
     */
    public function setFoot($file)
    {
        $this->foot = $file;
    }

    /**
     * Delegates method calls to view helpers.
     * The view helper class name is the method name with uppercased first letter.
     *
     * @param string $method
     * @param string $parameters
     * @return string
     */
    public function __call($method, $parameters)
    {
        $className = $this->getViewHelperName($method);

// @todo make sure view helper is registered

        $viewHelper = new $className($this->request, $this->response);
        return $viewHelper->execute($parameters);
    }

    /**
     * Register a view helper.
     *
     * @param string $className
     */
    public function registerViewHelper($className)
    {
        $this->viewHelpers[] = $className;
    }

    /**
     * Render the view by including the view script.
     *
     * @param string $viewName
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function render($viewName, Request $request, Response $response)
    {
        $this->viewName = $viewName;

        $this->request  = $request;
        $this->response = $response;

        if ($this->response->isRedirect()) {
// @todo: use view helper to generate url
            header('Location: index.php?mvc_controller=' . $this->response->getRedirectController());
            return;
// @todo when no controller, but only action set, what happens?
        }

        $head = $this->getHeadFilename();
        $body = $this->getFilename();
        $foot = $this->getFootFilename();

        if (!file_exists($head)) {
            throw new Exception('View head in file ' . $head . ' not found');
        }

        if (!file_exists($body)) {
            throw new Exception('View ' . $this->viewName . ' in file ' . $body . ' not found');
        }

        if (!file_exists($foot)) {
            throw new Exception('View foot in file ' . $foot . ' not found');
        }

        $this->setCookies();
        $this->sendHeaders();

        ob_start();
        require $head;
        require $body;
        require $foot;
        $body = ob_get_clean();

        return $this->postProcess($body);
    }
}
?>