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

return array(
    'spriebsch\MVC\FrontController'         => 'FrontController.php',
    'spriebsch\MVC\Router'                  => 'Router.php',
    'spriebsch\MVC\Request'                 => 'Request.php',
    'spriebsch\MVC\Response'                => 'Response.php',
    'spriebsch\MVC\Session'                 => 'Session.php',
    'spriebsch\MVC\MockSession'             => 'MockSession.php',
    'spriebsch\MVC\Controller'              => 'Controller.php',
    'spriebsch\MVC\ControllerFactory'       => 'ControllerFactory.php',
    'spriebsch\MVC\Authenticator'           => 'Authenticator.php',
    'spriebsch\MVC\PasswdFileAuthenticator' => 'PasswdFileAuthenticator.php',
    'spriebsch\MVC\Renderer'                => 'Renderer.php',
    'spriebsch\MVC\Message'                 => 'Message.php',
    'spriebsch\MVC\Message\Error'           => 'Message/Error.php',
    'spriebsch\MVC\Message\FormError'       => 'Message/FormError.php',
    'spriebsch\MVC\Message\FieldError'      => 'Message/FieldError.php',
    'spriebsch\MVC\View'                    => 'View.php',
    'spriebsch\MVC\JsonView'                => 'JsonView.php',
    'spriebsch\MVC\ViewFactory'             => 'ViewFactory.php',
    'spriebsch\MVC\ViewHelper'              => 'ViewHelper.php',
    'spriebsch\MVC\ViewHelper\Ul'           => 'ViewHelper/Ul.php',
    'spriebsch\MVC\ViewHelper\Table'        => 'ViewHelper/Table.php',
    'spriebsch\MVC\ViewHelper\Menu'         => 'ViewHelper/Menu.php',
    'spriebsch\MVC\ViewHelper\Url'          => 'ViewHelper/Url.php',
    'spriebsch\MVC\ViewHelper\FormErrors'   => 'ViewHelper/FormErrors.php',
    'spriebsch\MVC\ViewHelper\FieldErrors'  => 'ViewHelper/FieldErrors.php',
    'spriebsch\MVC\ViewHelper\Errors'       => 'ViewHelper/Errors.php',
    'spriebsch\MVC\Acl'                     => 'Acl.php',
    'spriebsch\MVC\ApplicationController'   => 'ApplicationController.php',
    'spriebsch\MVC\TableDataGateway'        => 'TableDataGateway.php',
);
?>