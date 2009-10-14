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
 * Access control list.
 * Basic strategy: everything that is not explicitly allowed will be denied.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Acl
{
    const ALLOW = true;
    const DENY = false;

    /**
     * @var bool
     */
    protected $policy = Acl::ALLOW;

    /**
     * @var array
     */
    protected $rules = array();

    /**
     * Add a rule
     *
     * @param boolean $flag Flag
     * @param string $role The role
     * @param string $controller The controller name
     * @return null
     */
    protected function addRule($flag, $role, $controller)
    {
        $this->rules[$role][$controller] = $flag;
    }

    /**
     * Checks whether a rule exists for given role and controller.
     *
     * @param string $role The role
     * @param string $controller The controller name
     * @return null
     */
    protected function hasRule($role, $controller)
    {
        return isset($this->rules[$role][$controller]);
    }

    /**
     * Returns the rule for a given role and controller.
     *
     * @param string $role The role
     * @param string $controller The controller name
     * @return bool
     */
    protected function getRule($role, $controller)
    {
        return $this->rules[$role][$controller];
    }

    /**
     * Checks whether a controller is allowed
     *
     * @param string $role The role
     * @param string $controller The controller name
     * @return bool
     */
    protected function isControllerAllowed($role, $controller)
    {
        if ($this->hasRule($role, $controller, '*')) {
            return $this->getRule($role, $controller, '*');
        }

        return $this->isRoleAllowed($role);
    }

    /**
     * Checks whether a role is allowed
     *
     * @param string $role The role
     * @return bool
     */
    protected function isRoleAllowed($role)
    {
        if ($this->hasRule($role, '*', '*')) {
            return $this->getRule($role, '*', '*');
        }

        return $this->policy;
    }

    /**
     * Set the default policy to ALLOW or DENY.
     *
     * @param bool $flag ALLOW or DENY
     * @return null
     */
    public function setPolicy($flag)
    {
        $this->policy = $flag;
    }

    /**
     * Returns the default policy.
     *
     * @return bool
     */
    public function getPolicy()
    {
        return $this->policy;
    }

    /**
     * Add a allow rule.
     *
     * @param string $role The role
     * @param string $controller The controller
     * @return null
     */
    public function allow($role, $controller = '*')
    {
        $this->addRule(Acl::ALLOW, $role, $controller);
    }

    /**
     * Add a deny rule.
     *
     * @param string $role The role
     * @param string $controller The controller
     * @return null
     */
    public function deny($role, $controller = '*')
    {
        $this->addRule(Acl::DENY, $role, $controller);
    }

    /**
     * Checks whether a controller is allowed.
     *
     * @param string $role The role
     * @param string $controller The controller
     * @return bool
     */
    public function isAllowed($role, $controller = null)
    {
        if (!is_null($controller)) {
            return $this->isControllerAllowed($role, $controller);
        }

        return $this->isRoleAllowed($role);
    }
}
?>