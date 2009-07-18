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
 * A data object that wraps all input data.
 * The application itself should never access superglobals, only this object.
 *
 * When instantiating Request (usually in the Front Controller or the
 * application's startup file), pass the original superglobals as arguments.
 * Then, throughout the application, do not work with superglobals, but
 * use $request->get('parameter'), $request->post('parameter'),
 * $request->cookie('name'), $request->files('name'),
 * $request->server('key'), $request->env('name').
 *
 * Request returns an empty string when a given parameter does not exist.
 *
 * In addition, you can use hasGet('...'), hasPost('...') etc. to check whether
 * a given request parameter, cookie, etc. exists.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Request
{
    /**
     * @var array
     */
    protected $data = array();

    /**
    * Constructs the Request object.
    *
    * @param array $get    $_GET array
    * @param array $post   $_POST array
    * @param array $cookie $_COOKIE array
    * @param array $files  $_FILES array
    * @param array $server $_SERVER array
    * @param array $env    $_ENV array
    */
    public function __construct($get = array(), $post = array(), $cookie = array(), $files = array(), $server = array(), $env = array())
    {
        $this->data['get']    = $get;
        $this->data['post']   = $post;
        $this->data['cookie'] = $cookie;
        $this->data['files']  = $files;
        $this->data['server'] = $server;
        $this->data['env']    = $env;
    }

    /**
     * Generic getter and has<Variable> method.
     *
     * @throws UnknownVariableException
     *
     * @param string $method     Name of the called method
     * @param array  $parameters Parameter array
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'has') {
            $variable = lcfirst(substr($method, 3));
        } else {
            $variable = $method;
        }

        if (!in_array($variable, array('get', 'post', 'cookie', 'files', 'server', 'env'))) {
            throw new UnknownVariableException('Input variable ' . $variable . ' does not exist');
        }

        if (substr($method, 0, 3) == 'has') {
            return isset($this->data[$variable][$parameters[0]]);
        }

        return $this->data[$variable][$parameters[0]];
    }
}
?>
