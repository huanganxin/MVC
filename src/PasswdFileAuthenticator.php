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
 * Authenticator that authenticated against a password file.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @todo add pluggable authentication adapter that does the actual work
 * @todo add authentication by session?
 */
class PasswdFileAuthenticator extends Authenticator
{
    /**
     * @var string
     */
    protected $salt = 'sadfoisfroq42hrewfawiuep423rweaifdsjawüeoiare';

    /**
     * @var array
     */
    protected $passwords = array();

    /**
     * Constructs the object.
     *
     * @param string $passwordFile
     * @return null
     */
    public function __construct($passwordFile)
    {
        if (!file_exists($passwordFile)) {
            throw new Exception('Password file ' . $passwordFile . ' not found');
        }

        $this->passwords = unserialize(file_get_contents($passwordFile));

        if ($this->passwords === false) {
            throw new Exception('Illegal Password file ' . $passwordFile);
        }
    }

    /**
     * Hash a password.
     *
     * @param string $password
     * @return string
     */
    protected function hashPassword($password)
    {
        return sha1($this->salt . $password);
    }

    /**
     * Performs the authentication
     *
     * @return bool
     */
    protected function doAuthenticate()
    {
        if (!isset($this->passwords[$this->username])) {
            $this->authenticated = false;
            return;
        }

        $this->authenticated = ($this->passwords[$this->username] == $this->hashPassword($this->password));
    }
}
?>