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
 * Unit Tests for View class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 *
 * @runTestsInSeparateProcesses
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException spriebsch\MVC\Exception
     * @covers spriebsch\MVC\View
     */
    public function testThrowsExceptionWhenHeadIsMissing()
    {
        $request = $this->getMock('\\spriebsch\\MVC\\Request');
        $response = $this->getMock('\\spriebsch\\MVC\\Response');

        $view = new View(__DIR__ . '/_testdata/View/no_head');
        $view->setViewScript('body');
        $view->render($request, $response);
    }

    /**
     * @expectedException spriebsch\MVC\Exception
     * @covers spriebsch\MVC\View
     */
    public function testThrowsExceptionWhenFootIsMissing()
    {
        $request = $this->getMock('\\spriebsch\\MVC\\Request');
        $response = $this->getMock('\\spriebsch\\MVC\\Response');

        $view = new View(__DIR__ . '/_testdata/View/no_foot');
        $view->setViewScript('body');
        $view->render($request, $response);
    }

    /**
     * @expectedException spriebsch\MVC\Exception
     * @covers spriebsch\MVC\View
     */
    public function testThrowsExceptionWhenBodyIsMissing()
    {
        $request = $this->getMock('\\spriebsch\\MVC\\Request');
        $response = $this->getMock('\\spriebsch\\MVC\\Response');

        $view = new View(__DIR__ . '/_testdata/View/no_body');
        $view->setViewScript('body');
        $view->render($request, $response);
    }

    /**
     * @covers spriebsch\MVC\View
     */
    public function testSomething()
    {
        $request = $this->getMock('\\spriebsch\\MVC\\Request');
        $response = $this->getMock('\\spriebsch\\MVC\\Response');

        $view = new View(__DIR__ . '/_testdata/View/all');
        $view->setViewScript('body');
        $this->assertEquals('headbodyfoot', $view->render($request, $response));
    }
}
?>