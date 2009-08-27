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
 * View
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
abstract class enoView
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $masterTemplate = 'master';

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var string
     */
    protected $head = '';

    /**
     * @var string
     */
    protected $foot = '';

    /**
     * @var string
     */
    protected $content = '';


    /**
     * Constructs the view object.
     */
    public function __construct()
    {
    }


    /**
     * Set the view template name
     *
     * @param string $template The view's template name
     * @return null
     */
    public function setTemplate($template)
    {
    $this->template = $template;
    }


    /**
     * Returns the view template
     *
     * @return string
     */
    public function getTemplate()
    {
    return $this->template;
    }


    /**
     * Set the view's master template name
     *
     * @param string $masterTemplate The view's master template name
     * @return null
     */
    public function setMasterTemplate($masterTemplate)
    {
    $this->masterTemplate = $masterTemplate;
    }


    /**
     * Returns the master template
     *
     * @return string
     */
    public function getMasterTemplate()
    {
    return $this->masterTemplate;
    }


    /**
     * Template method to do the view rendering.
     *
     * @return null
     */
    protected function doRender()
    {
    }


    /**
     * Modifies the page template's DOM,
     * injecting messages (error, warning, result) and default values.
     *
     * @param string $html Page HTML
     * @return null
     * @todo message node must become first child node
     * @throws enoException XPath mismatch
     */
    protected function modifyDom($html)
    {
    if ($html == '') {
      return;
    }

    $dom = new DOMDocument();
    $dom->loadHtml($html);

    $query = new DOMXPath($dom);

    foreach ($this->response->getMessages() as $message) {
      $nodes = $query->evaluate($message->getXPath());

      if ($nodes->length != 1) {
        throw new enoException('XPath mismatch: ' . $message->getXPath() .
                    ' for message ' . $message->getText() . ' had ' .
                    $nodes->length . ' results');
      }

      $nodes->item(0)->appendChild($message->render($dom));
    }

    foreach ($this->response->getValues() as $value) {
      $nodes = $query->evaluate($value->getXPath());

      if ($nodes->length != 1) {
        throw new enoException('XPath mismatch: ' . $value->getXPath() .
          ' for value ' . $value->getValue() . ' had ' . $nodes->length .
          ' results');
      }

      $nodes->item(0)->value = $value->getValue();
    }

    return $dom->saveHtml();
    }


    /**
     * Set the master template's head part
     *
     * @param string $head The master template's foot part
     * @return null
     */
    public function setHead($head)
    {
    $this->head = $head;
    }


    /**
     * Set the master template's foot part
     *
     * @param string $foot The master template's foot part
     * @return null
     */
    public function setFoot($foot)
    {
    $this->foot = $foot;
    }


    /**
     * Set the page template content
     *
     * @param string $content The template's content
     * @return null
     */
    public function setContent($content)
    {
    $this->content = $content;
    }


    /**
     * Returns the rendered view.
     *
     * @param enoRequest  $request  Request object
     * @param enoResponse $response Response object
     * @return string
     */
    public function render(enoRequest $request, enoResponse $response)
    {
    $this->request  = $request;
    $this->response = $response;

    $this->doRender();
    return $this->modifyDom($this->head . $this->content . $this->content);
    }
}
?>