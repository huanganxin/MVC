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
 * View factory class.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ViewFactory
{
	protected $directory;
	
    protected $views = array();
    protected $viewHelpers = array();
    protected $additionalViewHelpers = array();

    protected $defaultViewHelpers = array(
        'errors'      => 'spriebsch\\MVC\\ViewHelper\\Errors',
        'formerrors'  => 'spriebsch\\MVC\\ViewHelper\\FormErrors',
        'fielderrors' => 'spriebsch\\MVC\\ViewHelper\\FieldErrors',
        'menu'        => 'spriebsch\\MVC\\ViewHelper\\Menu',
        'ul'          => 'spriebsch\\MVC\\ViewHelper\\Ul',
        'options'     => 'spriebsch\\MVC\\ViewHelper\\Options',
        'url'         => 'spriebsch\\MVC\\ViewHelper\\Url',
        'table'       => 'spriebsch\\MVC\\ViewHelper\\Table',
    );
    
    /**
     * Constructs the factory.
     *
     * @param View $view
     * @param string $directory
     * @return void
     */
    public function __construct(View $view, $directory)
    {
        $this->defaultView = $view;
        $this->directory = $directory;
    }

    /**
     * We also expect the classname because we use \-prefixed class names
     * throughout and get_class() returns a non-prefixed class name.
     * 
     * @param string $class Class name of the view instance
     * @param View $view View instance
     * @param string $script The view script name
     * @return null
     */
    protected function registerViewHelpers($class, View $view, $script)
    {
    	if (isset($this->viewHelpers[$class])) {
            $helpers = $this->viewHelpers[$class];
    	} else {
    		$helpers = $this->defaultViewHelpers;

    		if (isset($this->additionalViewHelpers[$script])) {
    		    $helpers = array_merge($helpers, $this->additionalViewHelpers[$script]);
    		}
    	}
    	
    	foreach ($helpers as $helper => $class) {
    		$view->registerViewHelper($helper, $class);
    	}
    }

    /**
     * Returns a view instance to handle given view script.
     * By default, the default view instance is used,
     * however, the factory can be configured to create
     * another view instance. 
     *
     * @param $script The view script's name
     * @return unknown_type
     */
    public function getView($script = null)
    {
        $view = $this->defaultView;
        $class = get_class($view); 

        // Do we need a special view instance for that script?
    	if ($script !== null && isset($this->views[$script])) {
    		$class = $this->views[$script];
    		$view = new $class($this->directory);
    	}

    	// Configure the view with the view script.
    	if ($script !== null) {
            $view->setViewScript($script);
    	}
    	
    	// Register the view helpers for this view instance.
        $this->registerViewHelpers($class, $view, $script);
        
        return $view;
    }
}
?>