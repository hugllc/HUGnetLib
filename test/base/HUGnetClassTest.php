<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", realpath(dirname(__FILE__)."/../.."));
}

require_once dirname(__FILE__).'/../../base/HUGnetClass.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetClassTest extends PHPUnit_Framework_TestCase
{
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
        parent::setUp();
        //$this->o =& HUGnetDB::getInstance("HUGnetDBClassTest", $this->config);
        $this->o = new HUGnetClassTestStub($this->config);
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
        unset($this->o);
    }
    /**
    * Data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(array("verbose" => 12), array("verbose" => 12)),
            array(array(), array("verbose" => 0)),
        );
    }
    /**
    * test
    *
    * @param array $config The configuration to use
    * @param array $expect Associative array of properties to check
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($config, $expect)
    {
        $o = new HUGnetClassTestStub($config);
        foreach ($expect as $key => $value) {
            $this->assertAttributeSame($value, $key, $o);
        }
    }

    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataVerbose()
    {
        return array(
            array(0, 0),
            array(6, 6),
            array(15, 15),
            array(false, 0),
            array(true, 1),
        );
    }
    /**
    * test
    *
    * @param int $val    The database key to get the record from
    * @param int $expect The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataVerbose
    */
    public function testVerbose($val, $expect)
    {
        $this->o->verbose($val);
        $this->assertAttributeSame($expect, "verbose", $this->o);
    }

    /**
    * data provider for testConstructor
    *
    * @return array
    *
    * @static
    */
    public static function dataVPrint()
    {
        return array(
            array("", 3, 10, ""),
            array("This is a test", 3, 10, "(HUGnetClassTestStub) This is a test\n"),
            array("This is a test", 3, 0, ""),
        );
    }
    /**
    * test
    *
    * @param string $str     The string to print out
    * @param int    $val     The required value to print
    * @param int    $verbose The current value
    * @param int    $expect  The expected stuff printed
    *
    * @return null
    *
    * @dataProvider dataVPrint
    */
    public function testVPrint($str, $val, $verbose, $expect)
    {
        ob_start();
        $this->o->vprint($str, $val, $verbose);
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    *
    * @static
    */
    public static function dataFindClass()
    {
        return array(
            array("HUGnetClass", "/base/", true),
            array("asdf", "containers", false),
        );
    }
    /**
    * test
    *
    * @param string $class  The class or object to use
    * @param stirng $dir    The directory to search
    * @param int    $expect The expected stuff printed
    *
    * @return null
    *
    * @dataProvider dataFindClass
    */
    public function testFindClass($class, $dir, $expect)
    {
        $this->assertSame($expect, HUGnetClassTestStub::findClassTest($class, $dir));
    }
    /**
    * data provider for testIsMine
    *
    * @return array
    *
    * @static
    */
    public static function dataIsMine()
    {
        return array(
            array(new HUGnetClassTestStub(), null, true),
            array("asdf", "containers", false),
        );
    }
    /**
    * test
    *
    * @param stirng $obj    The object to use
    * @param object $class  The class to use
    * @param bool   $expect The expected stuff printed
    *
    * @return null
    *
    * @dataProvider dataIsMine
    */
    public function testIsMine($obj, $class, $expect)
    {
        $this->assertSame($expect, $this->o->isMine($obj, $class));
    }
    /**
    * test
    *
    * @return null
    *
    * @expectedException Exception
    */
    public function testThrowException()
    {
        $this->o->throwExceptionTest("Hello", -5);
    }
    /**
    * test
    *
    * @return null
    *
    */
    public function testThrowExceptionNot()
    {
        $this->o->config["silent"] = true;
        $this->o->throwExceptionTest("Not Happening", -5);
    }
    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataSetStringSize()
    {
        return array(
            array("", 6, "0", "000000"),
            array("12345678", 5, "0", "45678"),
            array("12", 4, null, "0012"),
        );
    }

    /**
    * test
    *
    * @param string $value  The string to play with
    * @param int    $size   The size to cut the string to
    * @param string $pad    The value to pad the string with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSetStringSize
    */
    public function testSetStringSize($value, $size, $pad, $expect)
    {
        if (is_null($pad)) {
            $ret = HUGnetClass::stringSize($value, $size);
        } else {
            $ret = HUGnetClass::stringSize($value, $size, $pad);
        }
        $this->assertSame($expect, $value, '$value not changed correctly');
        $this->assertSame($expect, $ret, "Return was not correct");
    }
    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataDehexify()
    {
        return array(
            array("4142434445", "ABCDE"),
            array("6162636465", "abcde"),
        );
    }


    /**
    * test
    *
    * @param string $str    The string to play with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDehexify
    */
    public function testDehexify($str, $expect)
    {
        $bin = HUGnetClass::dehexify($str);
        $this->assertSame($expect, $bin);
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexify()
    {
        return array(
            array(1, null, "01"),
            array(-1, 4, "FFFF"),
            array(1024, 2, "00"),
            array(1024, 4, "0400"),
        );
    }
    /**
    * test
    *
    * @param int    $value  The number to play with
    * @param int    $width  The width of the output string
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataHexify
    */
    public function testHexify($value, $width, $expect)
    {
        if (is_null($width)) {
            $ret = HUGnetClass::hexify($value);
        } else {
            $ret = HUGnetClass::hexify($value, $width);
        }
        $this->assertEquals($expect, $ret);
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexifyStr()
    {
        return array(
            array("\0\r\n", "000D0A"),
            array("123", "313233"),
            array("ABC", "414243"),
        );
    }
    /**
    * test
    *
    * @param string $str    The string to play with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataHexifyStr
    */
    public function testHexifyStr($str, $expect)
    {
        $ret = HUGnetClass::hexifyStr($str);
        $this->assertEquals($expect, $ret);
    }
}

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetClassTestStub extends HUGnetClass
{
    /**
    * Load a class file if possible
    *
    * @param string $class The class or object to use
    * @param stirng $dir   The directory to search
    *
    * @return null
    */
    public function findClassTest($class, $dir = "")
    {
        return parent::findClass($class, $dir);
    }
    /**
    * Throws an exception
    *
    * @param string $msg  The message
    * @param int    $code The error code
    *
    * @return null
    */
    public function throwExceptionTest($msg, $code)
    {
        $this->throwException($msg, $code);
    }

}
?>
