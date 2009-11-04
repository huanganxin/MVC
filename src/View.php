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
 * View.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class View
{
    /**
     * @var string
     */
    protected $viewScript;
    
    protected $redirectController;

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
    protected function sendCookies()
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
        return $this->directory . '/' . $this->viewScript . '.php';
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
    protected function applyViewHelpers($body)
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
     * Populate values in HTML forms. 
     *
     * @param string $body
     * @return string
     * @todo fix for radio buttons and dropdown boxes!
     */
    protected function populateForms($body)
    {
    	$forms = $this->response->getForms();
    	
    	// If response object holds no form data, we are done.
    	if (sizeof($forms) == 0) {
    		return $body;
    	}
    	
	    $dom = new \DOMDocument();
	    $dom->loadHtml($body);

        $query = new \DOMXPath($dom);

	    foreach ($forms as $form) {
	    	foreach ($this->response->getFormValues($form) as $field => $value) {
	    		$nodes = $query->evaluate("//form[@name='" . $form . "']//*[@name='" . $field . "']");
	    		
	    		if ($nodes->length == 0) {
	    			throw new Exception('No field ' . $field . ' in form ' . $form);
        		}
        		
        		$node = $nodes->item(0);
        		
                switch ($node->nodeName) {
                	case 'input':
                        $node->setAttribute('value', $value);
               		break;
               		
                	case 'textarea':
                        $node->firstChild->nodeValue = $value;
                    break;
                    
                	default:
                		throw new Exception('Unknown tag ' . $nodes->item(0)->nodeName . ' in form ' . $form);
// @todo DEV only.                		
                }
	    	}
	    }

        return $dom->saveHtml();
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

        if (!class_exists($className)) {
        	throw new Exception('View helper ' . $method . '(' . $className . ') does not exist');
        }

        $viewHelper = new $className($this->request, $this->response);
        return $viewHelper->execute($parameters);
    }
    
    public function setViewScript($viewScript)
    {
    	$this->viewScript = $viewScript;
    }

    public function getViewScript()
    {
        return $this->viewScript;
    }
    
    public function setRedirect($controllerName)
    {
    	$this->redirectController = $controllerName;
    }

    public function getRedirect()
    {
        return $this->redirectController;
    }
    
    /**
     * Render the view by including the view script.
     *
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function render(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;

        if ($this->redirectController !== null) {
        	header('Location: ' . $this->url($this->redirectController));
            return '';
        }

        if ($this->viewScript === null) {
            throw new Exception('View script not set');
        }

        $head = $this->getHeadFilename();
        $body = $this->getFilename();
        $foot = $this->getFootFilename();

        if (!file_exists($head)) {
            throw new Exception('View head in file ' . $head . ' not found');
        }

        if (!file_exists($body)) {
            throw new Exception('View ' . $this->viewScript . ' in file ' . $body . ' not found');
        }

        if (!file_exists($foot)) {
            throw new Exception('View foot in file ' . $foot . ' not found');
        }

        $this->sendCookies();
        $this->sendHeaders();

        ob_start();
        require $head;
        require $body;
        require $foot;
        $body = ob_get_clean();

// @todo make this a filter that can be registered (per view script) 
        $body = $this->populateForms($body);
        
        return $this->applyViewHelpers($body);
    }
}
?>