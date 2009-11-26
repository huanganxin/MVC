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
 * Generic authentication handler
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
abstract class AuthenticationHandler
{
	/**
	 * @var Session
	 */
	protected $session;

    /**
     * Construct the object.
     *
     * @param Session     $session 
     * @return null
     */
    public function __construct(Session $session)
    {
    	$this->session = $session;
    }

    /**
     * Checks if credentials (username, password) are valid.
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    abstract public function isValid($username, $password);

    /**
     * Checks whether the session is authenticated. 
     * 
     * @return bool
     */
    abstract public function isAuthenticated();

    /**
     * Authenticates a session. Called when the user logs in.
     * 
     * @param string $username
     * @return null
     */
    abstract public function authenticate($username, array $roles);

    /**
     * Unauthenticates a session. Should be called when user logs out.
     *
     * @return null
     */
    abstract public function unauthenticate(); 
    
    /**
     * Returns the authenticated username, or null if session is not authenticated.
     * 
     * @return string
     */
    abstract public function getUsername();
    
    /**
     * Returns array of roles of authenticated user.
     *
     * @return array
     */
    abstract public function getRoles();
}?>