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
 * Unit Tests for Acl class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class AclTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->acl = new Acl();
    }

    /**
     * The default policy must be "allow". 
     */
    public function testDefaultPolicyIsAllow()
    {
        $this->assertEquals(Acl::ALLOW, $this->acl->getPolicy());
    }

    /**
     * Tests getPolicy() and setPolicy().
     */
    public function testPolicyAccessors()
    {
        $this->acl->setPolicy(Acl::DENY);
        $this->assertEquals(Acl::DENY, $this->acl->getPolicy());
    }

    /**
     * setPolicy() must throw an exception on illegal value.
     *
     * @expectedException spriebsch\MVC\Exception
     */
    public function testSetPolicyThrowsExceptionOnIllegalValue()
    {
        $this->acl->setPolicy('nonsense');
    }

    /**
     * Access must be denied when the role has been denied.
     */
    public function testDenyRoleDeniesAccessToThatRole()
    {
        $this->acl->deny('role');

        $this->assertEquals(Acl::DENY, $this->acl->isAllowed('role'));
    }

    /**
     * Access to any resource must be denied when the role has been denied.
     */
    public function testDenyRoleDeniesAnythingToThatRole()
    {
        $this->acl->deny('role');

        $this->assertEquals(Acl::DENY, $this->acl->isAllowed('role', 'resource'));
        $this->assertEquals(Acl::DENY, $this->acl->isAllowed('role', 'anything'));
    }

    /**
     * Other roles must still be allowed when one role is denied.   
     */
    public function testDenyRoleAllowsOtherRoles()
    {
        $this->acl->deny('role');

        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('another role'));
    }
    
    /**
     * When no rules are specified, all roles must be allowed.
     */
    public function testAllowsAllRolesByDefault()
    {
        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role'));
        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('another role')); 
    }

    /**
     * When no rules are specified, all resources must be allowed. 
     */
    public function testAllowsAllResourcesByDefault()
    {
        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role', 'resource'));
        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role', 'another resource'));
    }

    /**
     * When a resource is denied for a role, the role must still be allowed.  
     */
    public function testDenyResourceStillAllowsTheRole()
    {
        $this->acl->deny('role', 'resource');

        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role'));
    }
    
    /**
     * When a resource is denied, access must be denied for this resource.   
     */
    public function testDenyResourceDeniesThatResource()
    {
        $this->acl->deny('role', 'resource');

        $this->assertEquals(Acl::DENY, $this->acl->isAllowed('role', 'resource'));
    }

    /**
     * When a resource is denied, access must be allowed for other resources.   
     */
    public function testDenyResourceAllowsOtherResources()
    {
        $this->acl->deny('role', 'resource');

        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role', 'another resource'));
    }

    /**
     * When a role is denied, but the resource allowed, access to the role
     * must be denied.
     */
    public function testDenyRoleAndAllowResourceDeniesRole()
    {
        $this->acl->deny('role');
        $this->acl->allow('role', 'resource');

        $this->assertEquals(Acl::DENY, $this->acl->isAllowed('role'));
    }

    /**
     * When a role is denied, but the resource allowed, access to the resource 
     * must be granted. 
     */
    public function testDenyRoleAndAllowResourceAllowsResource()
    {
        $this->acl->deny('role');
        $this->acl->allow('role', 'resource');

        $this->assertEquals(Acl::ALLOW, $this->acl->isAllowed('role', 'resource'));
    }
}
?>