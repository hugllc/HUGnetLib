<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once CODE_BASE.'base/HUGnetExtensibleContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetExtensibleContainerTest extends PHPUnit_Framework_TestCase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $this->o = new HUGnetEContainerTestClass();
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        $this->o = null;
    }


    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("Attrib1", 16, 16),
            array("Attrib1", 16, 0, "Attrib1"),
            array("Attrib1", "16Test", 16),
            array("Attrib5", "Hello", null),
            array("Attrib1", 16, 16, "", new HUGnetEContainerTestClass2()),
            array("Attrib1", 16, 0, "Attrib1", new HUGnetEContainerTestClass2()),
            array(
                "Attrib5", "Hello", "Hello",
                "", new HUGnetEContainerTestClass2()
            ),
            array(
                "Attrib5", "Hello", "Blank String",
                "Attrib5", new HUGnetEContainerTestClass2()
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $attrib This is the attribute to set
    * @param mixed  $value  The value to set it to
    * @param int    $expect The expected return
    * @param mixed  $lock   The spot to lock
    * @param object $obj    The class for extra
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet(
        $attrib,
        $value,
        $expect,
        $lock = "",
        $obj = null
    ) {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->lock($lock);
        $object->$attrib = $value;
        $this->assertSame($expect, $object->$attrib);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataRegister()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                "test",
                true,
                array(
                    "Attrib5", "Attrib6", "Attrib7", "Attrib8",
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                ),
            ),
            array(
                null,
                "test",
                false,
                null,
            ),
            array(
                new HUGnetEContainerTestClass2(),
                "atest",
                true,
                array(
                    "Attrib5", "Attrib6", "Attrib7", "Attrib8",
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                ),
            ),
        );
    }

    /**
    * test the register function
    *
    * @param mixed  $obj        The class or object to use
    * @param string $var        The variable to register the object on
    * @param bool   $expect     The return expected
    * @param array  $properties The properties we should expect in the subclass
    *
    * @return null
    *
    * @dataProvider dataRegister
    */
    public function testRegister($obj, $var, $expect, $properties)
    {
        $object = new HUGnetEContainerTestClass();
        $ret = $object->register($obj, $var);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $this->assertSame(
                $obj,
                $this->readAttribute($object, $var)
            );
            // This will tell us if the class is registered
            $this->assertSame(
                $properties,
                $obj->getProperties()
            );
        } else {
            $this->assertNull(
                $this->readAttribute($object, $var)
            );
        }
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataUnregister()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                "test",
                true,
            ),
            array(
                null,
                "test",
                true,
            ),
            array(
                null,
                "",
                false,
            ),
            array(
                new HUGnetEContainerTestClass2(),
                "atest",
                true,
            ),
        );
    }

    /**
    * test the register function
    *
    * @param mixed  $obj    The class or object to use
    * @param string $var    The variable to register the object on
    * @param bool   $expect The return expected
    *
    * @return null
    *
    * @dataProvider dataUnregister
    */
    public function testUnregister($obj, $var, $expect)
    {
        $object = new HUGnetEContainerTestClass();
        $object->register($obj, $var);
        $ret = $object->unregister($var);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $this->assertNull(
                $this->readAttribute($object, $var)
            );
        }
    }

    /**
    * data provider for testUnregisterNext
    *
    * @return array
    */
    public static function dataUnregisterNext()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                true,
            ),
            array(
                null,
                true,
            ),
            array(
                new HUGnetEContainerTestClass2(),
                true,
            ),
        );
    }

    /**
    * test the register function
    *
    * @param mixed $obj    The class or object to use
    * @param bool  $expect The return expected
    *
    * @return null
    *
    * @dataProvider dataUnregisterNext
    */
    public function testUnregisterNext($obj, $expect)
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $ret = $object->unregisterNext();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testUnregisterPrev
    *
    * @return array
    */
    public static function dataUnregisterPrev()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                true,
            ),
            array(
                null,
                true,
            ),
            array(
                new HUGnetEContainerTestClass2(),
                true,
            ),
        );
    }

    /**
    * test the register function
    *
    * @param mixed $obj    The class or object to use
    * @param bool  $expect The return expected
    *
    * @return null
    *
    * @dataProvider dataUnregisterPrev
    */
    public function testUnregisterPrev($obj, $expect)
    {
        $object = new HUGnetEContainerTestClass("", $obj2, $obj);
        $ret = $object->unregisterPrev();
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataCall()
    {
        return array(
            array(
                "myFunction",
                array(1,2,3,4),
                null,
                null,
                null,
            ),
            array(
                "args",
                array(1,2,3,4),
                new HUGnetEContainerTestClass2(),
                null,
                array(1,2,3,4),
            ),
            array(
                "args",
                array(1,2,3,4),
                null,
                new HUGnetEContainerTestClass2(),
                array(1,2,3,4),
            ),
        );
    }

    /**
    * test the register function
    *
    * @param string $function The name of the function to run
    * @param array  $args     The function arguments
    * @param object $obj      The class or object to use for next
    * @param object $obj2     The class or object to use for prev
    * @param bool   $expect   The return expected
    *
    * @return null
    *
    * @dataProvider dataCall
    */
    public function testCall($function, $args, $obj, $obj2, $expect)
    {
        $object = new HUGnetEContainerTestClass("", $obj, $obj2);
        $ret = call_user_func_array(array($object, $function), $args);
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetProperties()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                array(
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                    "Attrib5", "Attrib6", "Attrib7", "Attrib8"
                ),
            ),
            array(
                new HUGnetEContainerTestClass2(),
                array(
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                    "Attrib5", "Attrib6", "Attrib7", "Attrib8"
                ),
                "_extraNext",
            ),
            array(
                "",
                array(
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param object $obj    Extra class to load
    * @param int    $expect The expected return
    * @param string $var    The variable to check
    *
    * @return null
    *
    * @dataProvider dataGetProperties
    */
    public function testGetProperties($obj, $expect, $var = null)
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $ret = $object->getProperties($var);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetMethods()
    {
        return array(
            array(
                new HUGnetEContainerTestClass2(),
                array(
                    "setAttrib1", "setAttrib5", "args"
                ),
            ),
            array(
                new HUGnetEContainerTestClass2(),
                array(
                    "setAttrib1", "setAttrib5", "args"
                ),
                "_extraNext",
            ),
            array(
                "",
                array(
                    "setAttrib1",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param object $obj    Extra class to load
    * @param int    $expect The expected return
    * @param string $var    The variable to check
    *
    * @return null
    *
    * @dataProvider dataGetMethods
    */
    public function testGetMethods($obj, $expect, $var = null)
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $ret = $object->getMethods($var);
        $this->assertSame(
            $expect,
            $ret
        );
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "Attrib1",
                0,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib7",
                1.0,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib1",
                0,
                '',
            ),
            array(
                "Attrib7",
                null,
                $nothing,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The attribute name to get
    * @param int    $expect The expected return
    * @param object $obj    The class for extra
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $expect, $obj = null)
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $ret = $object->$name;
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataUnset()
    {
        return array(
            array(
                "Attrib1",
                null,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib7",
                null,
                new HUGnetEContainerTestClass2(),
                true
            ),
            array(
                "Attrib1",
                null,
                null,
            ),
            array(
                "Attrib7",
                null,
                null,
            ),
            array(
                "Attrib1",
                0,
                new HUGnetEContainerTestClass2(),
                "Attrib1",
            ),
            array(
                "Attrib7",
                1.0,
                new HUGnetEContainerTestClass2(),
                "Attrib7",
            ),
            array(
                "Attrib1",
                0,
                null,
                "Attrib1",
            ),
            array(
                "Attrib7",
                null,
                null,
                "Attrib7",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The attribute name to get
    * @param int    $expect The expected return
    * @param object $obj    The class for extra
    * @param mixed  $lock   The attribute to lock
    *
    * @return null
    *
    * @dataProvider dataUnset
    */
    public function testUnset($name, $expect, $obj = null, $lock = "")
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->lock($lock);
        unset($object->$name);
        $this->assertSame($expect, $object->$name);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIsset()
    {
        return array(
            array(
                "Attrib1",
                true,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib9",
                false,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib8",
                true,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib1",
                true,
                null,
            ),
            array(
                "Attrib7",
                false,
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The attribute name to get
    * @param int    $expect The expected return
    * @param object $obj    The class for extra
    *
    * @return null
    *
    * @dataProvider dataIsset
    */
    public function testIsset($name, $expect, $obj = null)
    {
        $object = new HUGnetEContainerTestClass("", $obj);
        $ret = isset($object->$name);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataClearData()
    {
        return array(
            array(
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "NotDefault",
                    "Attrib3" => "Data Where",
                    "Attrib4" => array("Hello There"),
                    "Attrib5" => "Full String",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.00253,
                    "Attrib8" => 156.9153,
                ),
                array(
                    "Attrib1" => 0,
                    "Attrib2" => "Default",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hello"),
                    "Attrib5" => "Blank String",
                    "Attrib6" => array("One Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4,
                ),
                array(),
                new HUGnetEContainerTestClass2()
            ),
            array(
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "NotDefault",
                    "Attrib3" => "Data Where",
                    "Attrib4" => array("Hello There"),
                    "Attrib5" => "Full String",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.00253,
                    "Attrib8" => 156.9153,
                ),
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Default",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hello"),
                    "Attrib5" => "Blank String",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4,
                ),
                array("Attrib1", "Attrib6"),
                new HUGnetEContainerTestClass2()
            ),
            array(
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "No longer the Default",
                    "Attrib3" => "Data Here",
                    "Attrib4" => array("Hello There"),
                ),
                array(
                    "Attrib1" => 0,
                    "Attrib2" => "No longer the Default",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hello"),
                ),
                "Attrib2",
                null
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The expected return from extra
    * @param array  $expect  The expected return from extra
    * @param mixed  $lock    The stuff to lock
    * @param object $obj     The class for extra
    *
    * @return null
    *
    * @dataProvider dataClearData
    */
    public function testClearData(
        $preload,
        $expect,
        $lock,
        $obj = null
    ) {
        $object = new HUGnetEContainerTestClass($preload, $obj);
        $object->lock($lock);
        $object->clearData();
        $obj = &$object;
        $this->assertSame(
            $expect,
            $object->toArray()
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataSetDefault()
    {
        return array(
            array("Attrib1", 16, "", 0),
            array("Attrib1", "16Test", "", 0),
            array("Attrib5", "Hello", "", null),
            array("Attrib1", 16, "", 0, new HUGnetEContainerTestClass2()),
            array(
                "Attrib5", "Hello", "", "Blank String",
                new HUGnetEContainerTestClass2()
            ),
            array("Attrib1", 16, "Attrib5", 0),
            array("Attrib5", "Hello", "Attrib5", null),
            array("Attrib1", 16, "Attrib1", 16, new HUGnetEContainerTestClass2()),
            array(
                "Attrib5", "Hello", "Attrib5", "Hello",
                new HUGnetEContainerTestClass2()
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $attrib This is the attribute to set
    * @param mixed  $value  The value to set it to
    * @param mixed  $lock   The attribute to lock
    * @param int    $expect The expected return
    * @param object $obj    The class for extra
    *
    * @return null
    *
    * @dataProvider dataSetDefault
    */
    public function testSetDefault(
        $attrib,
        $value,
        $lock,
        $expect,
        $obj = null
    ) {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->$attrib = $value;
        $object->lock($lock);
        $object->setDefault($attrib);
        $this->assertSame($expect, $object->$attrib, "$expect != ".$object->$attrib);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataLock()
    {
        return array(
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4"),
                array("Attrib1", "Attrib4"),
                new HUGnetEContainerTestClass2(),
            ),
            array(
                "Attrib1",
                array("Attrib1"),
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4", "Attrib5", "Attrib8"),
                array("Attrib1", "Attrib4", "Attrib5", "Attrib8"),
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4", "Attrib5", "Attrib8"),
                array("Attrib1", "Attrib4"),
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $lock   The stuff to lock
    * @param array  $expect The expected return
    * @param string $obj    The extra class to use
    *
    * @return null
    *
    * @dataProvider dataLock
    */
    public function testLock(
        $lock,
        $expect,
        $obj = null
    ) {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->lock($lock);
        $this->assertSame($expect, $object->locked());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataUnlock()
    {
        return array(
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4"),
                array("Attrib1"),
                array("Attrib4"),
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4"),
                "Attrib4",
                array("Attrib1"),
                "",
            ),
            array(
                array("asfd", "Attrib1", "fdscdd", "Attrib4"),
                "Attrib8",
                array("Attrib1", "Attrib4"),
                new HUGnetEContainerTestClass2(),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $lock   The stuff to lock
    * @param array  $unlock The stuff to unlock
    * @param array  $expect The expected return
    * @param object $obj    The class for extra
    *
    * @return null
    *
    * @dataProvider dataUnlock
    */
    public function testUnlock(
        $lock,
        $unlock,
        $expect,
        $obj = null
    ) {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->lock($lock);
        $object->unlock($unlock);
        $this->assertSame($expect, $object->locked());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataLocked()
    {
        return array(
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                "Attrib2",
                false,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                "Attrib5",
                true,
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                "Attrib5",
                false,
                "",
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                null,
                array("Attrib1", "Attrib4", "Attrib5"),
                new HUGnetEContainerTestClass2(),
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5", "Attrib7"),
                null,
                array("Attrib1", "Attrib4", "Attrib5", "Attrib7"),
                new HUGnetEContainerTestClass2(),
                "_extraNext"
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5", "Attrib7"),
                null,
                array("Attrib1", "Attrib4"),
                new HUGnetEContainerTestClass2(),
                "_extraPrev"
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                null,
                array("Attrib1", "Attrib4"),
                "",
            ),
            array(
                array("Attrib1", "Attrib4", "Attrib5"),
                null,
                array("Attrib1", "Attrib4"),
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $lock   The stuff to lock
    * @param string $check  The stuff to unlock
    * @param bool   $expect The expected return
    * @param object $obj    The class for extra
    * @param string $var    The variable to pass to lock
    *
    * @return null
    *
    * @dataProvider dataLocked
    */
    public function testLocked(
        $lock,
        $check,
        $expect,
        $obj = null,
        $var = null
    ) {
        $object = new HUGnetEContainerTestClass("", $obj);
        $object->lock($lock);
        $ret = $object->locked($check, $var);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructorData()
    {
        return array(
            array(
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib4" => array("Hi"),
                    "Attrib5" => "Another string",
                    "Attrib6" => array("Two Element"),
                    "Attrib8" => 4.321,
                ),
                new HUGnetEContainerTestClass2(),
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                    "Attrib5" => "Another string",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4.321,
                ),
            ),
            array(
                array(
                    "Attrib1" => 100,
                    "Attrib2" => "Hello There",
                    "Attrib3" => "Some Data",
                    "Attrib4" => array("Hello Everyone"),
                    "Attrib5" => "NonBlank String",
                    "Attrib6" => array("Three Element"),
                    "Attrib7" => 1.15,
                    "Attrib8" => 9.95,
                ),
                "",
                array(
                    "Attrib1" => 100,
                    "Attrib2" => "Hello There",
                    "Attrib3" => "Some Data",
                    "Attrib4" => array("Hello Everyone"),
                ),
            ),
            array(
                base64_encode(
                    serialize(
                        array(
                            "Attrib1" => 10,
                            "Attrib2" => "Hello",
                            "Attrib4" => array("Hi"),
                            "Attrib5" => "Another string",
                            "Attrib6" => array("Two Element"),
                            "Attrib8" => 4.321,
                        )
                    )
                ),
                new HUGnetEContainerTestClass2(),
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                    "Attrib5" => "Another string",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4.321,
                ),
            ),
            array(
                base64_encode(
                    serialize(
                        array(
                            "Attrib1" => 100,
                            "Attrib2" => "Hello There",
                            "Attrib3" => "Some Data",
                            "Attrib4" => array("Hello Everyone"),
                            "Attrib5" => "NonBlank String",
                            "Attrib6" => array("Three Element"),
                            "Attrib7" => 1.15,
                            "Attrib8" => 9.95,
                        )
                    )
                ),
                "",
                array(
                    "Attrib1" => 100,
                    "Attrib2" => "Hello There",
                    "Attrib3" => "Some Data",
                    "Attrib4" => array("Hello Everyone"),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $data   The data used to build the stuff
    * @param string $class  The class for extra
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructorData
    */
    public function testConstructorData($data, $class, $expect)
    {
        $object = new HUGnetEContainerTestClass($data, $class);
        $this->assertSame(
            $expect,
            $object->toArray()
        );
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetEContainerTestClass extends HUGnetExtensibleContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "Attrib1" => 0,
        "Attrib2" => "Default",
        "Attrib3" => "Data",
        "Attrib4" => array("Hello"),
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object We are going to put an object here */
    public $test = null;
    /** @var object We are going to put an object here */
    protected $atest = null;

    /**
    * function to check Attrib1
    *
    * @param mixed $value The value to set this to
    *
    * @return null
    */
    protected function setAttrib1($value)
    {
        $this->data["Attrib1"] = (int) $value;
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetEContainerTestClass2 extends HUGnetExtensibleContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "Attrib5" => "Blank String",
        "Attrib6" => array("One Element"),
        "Attrib7" => 1.0,
        "Attrib8" => 4,
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /**
    * function to check Attrib5
    *
    * @param mixed $value The value to set this to
    *
    * @return null
    */
    protected function setAttrib5($value)
    {
        $this->data["Attrib5"] = (string) $value;
    }

    /**
    * function to check Attrib5
    *
    * @return null
    */
    protected function args()
    {
        $ret = func_get_args();
        return $ret;
    }

}

?>
