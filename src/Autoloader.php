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
 * @package    Loader
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\Loader;

if (!defined('__SPRIEBSCH_LOADER_AUTOLOAD')) {

define('__SPRIEBSCH_LOADER_AUTOLOAD', true);

/**
 * A class loader (autoloader) that can handle multiple directories 
 * (class paths). Each directory must contain a file _ClassMap.php
 * that defines where to load classes from.
 *
 * Use Autoloader::registerPath() to add a "classpath", a directory to load classes
 * from. This directory must contain a file $_ClassMap.php
 * (see Autoloader::registerPath()). Call Autoloader::init() to register the autoloader.
 * Now you can go ahead and just use any class that is listed in a class map.
 * It is of course possible to use multiple class paths, when the Loader 
 * searches through them for a class, no filesystem access is involved, but
 * only the map itself is being searched in memory.
 *
 * Class maps can be auto-generated from the available source code.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
final class Autoloader
{
    /**
     * @var array
     */
    static private $classPaths = array();

    /**
     * @var array
     */
    static private $classMaps = array();

    /**
     * Throws an exception on object construction.
     * The class must be used statically.
     *
     * @throws spriebsch\Loader\CannotInstantiateLoaderException
     *
     * @return null
     */
    public function __construct()
    {
        throw new CannotInstantiateLoaderException('This class cannot be instantiated');
    }

    /**
     * Registers the autoload method an additional PHP autoload handler.
     *
     * @return null
     */
    static public function init()
    {
        spl_autoload_register(array('spriebsch\Loader\Autoloader', 'autoload'));
    }

	// @codeCoverageIgnoreStart

    /**
     * Reset the autoloader.
     * Should be used for unit testing purposes only.
     *
     * @return null
     */
    static public function reset()
    {
        self::$classPaths = array();
        self::$classMaps = array();
    }

	// @codeCoverageIgnoreEnd

    /**
     * Register a path to autoload classes from.
     * In the given directory, a file _ClassMap.php must be present
     * that contains an array $_classMap holding key/value pairs with
     * classname as the key and relative path to the classfile as value.
     *
     * @throws spriebsch\Loader\ClassMapNotFoundException
     * @throws spriebsch\Loader\InvalidClassMapException
     *
     * @param string $classPath Path to load classes from
     * @return null
     */
    static public function registerPath($classPath)
    {
        if (substr($classPath, -1) != '/') {
            $classPath .= '/';
        }

        $classMap = $classPath . '_ClassMap.php';

        if (!file_exists($classMap)) {
            throw new ClassMapNotFoundException($classMap . ' not found');
        }

        include $classMap;

        if (!isset($_classMap) || !is_array($_classMap)) {
            throw new InvalidClassMapException('$_classMap in ' . $classMap . ' is not an array');
        }

        self::$classPaths[] = $classPath;
        self::$classMaps[] = $_classMap;
    }

    /**
     * Autoloads classes from given classpaths.
     * Searches through all class maps for the class to load.
     *
     * @param string $class Class name to load
     * @return null
     */
    static public function autoload($class)
    {
        if (substr($class, 0, 1) == '\\') {
            $class = substr($class, 1);
        }

        $count = count(self::$classMaps);

        for ($i = 0; $i < $count; $i++) {
            if (isset(self::$classMaps[$i][$class])) {
                include self::$classPaths[$i] . self::$classMaps[$i][$class];
            }
        }
    }
}

class Exception extends \Exception
{
}

class CannotInstantiateLoaderException extends Exception
{
}

class ClassMapNotFoundException extends Exception
{
}

class InvalidClassMapException extends Exception
{
}

}
?>
