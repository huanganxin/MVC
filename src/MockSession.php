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
 * Mock session acts like session, but does not interact with the global
 * environment by accessing $_SESSION or sending headers.
 * No real session is involved here, so there will be no persistence.
 *
 * Mock session calculates its own session id, since no built-in session
 * is involved.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class MockSession extends Session
{
    protected $variables = array();
    protected $sessionId;

    /**
     * Calculates a mock session id.
     *
     * @return string
     */
    protected function calculateSessionId()
    {
        return substr(md5(uniqid()), 0, 26);
    }

    /**
     * Sets a session variable.
     *
     * @param mixed $key   Session variable name
     * @param mixed $value Session variable value
     * @return void
     */
    public function set($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * Returns a session variable.
     *
     * @param mixed $key   Session variable name
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->variables[$key])) {
            throw new SessionException('Unknown session variable ' . $key);
        }

        return $this->variables[$key];
    }

    /**
     * Checks whether a session variable exists.
     *
     * @param mixed $key Session variable name
     * @return bool
     */
    public function has($key)
    {
        return isset($this->variables[$key]);
    }

    /**
     * Starts the sesssion.
     *
     * @return void
     */
    public function start()
    {
        $this->started = true;
        $this->sessionId = $this->calculateSessionId();
    }

    /**
     * Sets the session name.
     *
     * @param string $name Session name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the session name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the sesssion id.
     *
     * @return string
     */
    public function getId()
    {
        $this->checkIfStarted();

        return $this->sessionId;
    }

    /**
     * Regenerate session id to make session fixation harder.
     *
     * @return void
     */
    public function regenerateId()
    {
        $this->checkIfStarted();

        $this->sessionId = $this->calculateSessionId();
    }
}
?>