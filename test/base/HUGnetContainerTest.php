<?php
/**
 * Tests the filter class
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../base/HUGnetContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetContainerTest extends PHPUnit_Framework_TestCase
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
        $this->o = new HUGnetContainerTestClass();
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
            array("Attrib1", "16Test", 16),
            array("Attrib5", "Hello", null),
            array("Attrib1", 16, 16, false, "HUGnetContainerTestClass2"),
            array("Attrib5", "Hello", "Hello", true, "HUGnetContainerTestClass2"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $attrib      This is the attribute to set
    * @param mixed  $value       The value to set it to
    * @param int    $expect      The expected return
    * @param bool   $expectExtra Expect the output to be saved in _extra
    * @param string $class       The extra class to use
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet(
        $attrib,
        $value,
        $expect,
        $expectExtra = false,
        $class = ""
    ) {
        $o = new HUGnetContainerTestClass("", $class);
        $o->$attrib = $value;
        if ($expectExtra) {
            //$class = $this->readAttribute($o, "_extra");
            //$this->assertSame($expect, $class->$attrib);
        } else {
            $this->assertSame($expect, $o->$attrib);
        }
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetAttributes()
    {
        return array(
            array(
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                    "Attrib5", "Attrib6", "Attrib7", "Attrib8"
                ),
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
    * @param mixed $extra  Extra class to load
    * @param int   $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetAttributes
    */
    public function testgetAttributes($extra, $expect)
    {
        $o = new HUGnetContainerTestClass("", "HUGnetContainerTestClass2");
        $ret = $o->getAttributes();
        $this->assertSame(
            array(
                "Attrib1", "Attrib2", "Attrib3", "Attrib4",
                "Attrib5", "Attrib6", "Attrib7", "Attrib8"
            ),
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
                "HUGnetContainerTestClass2",
            ),
            array(
                "Attrib7",
                1.0,
                "HUGnetContainerTestClass2",
            ),
            array(
                "Attrib1",
                0,
                "",
            ),
            array(
                "Attrib7",
                null,
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The attribute name to get
    * @param int    $expect The expected return
    * @param string $class  The class for extra
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $expect, $class = "")
    {
        $o = new HUGnetContainerTestClass("", $class);
        $ret = $o->$name;
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
                "HUGnetContainerTestClass2",
            ),
            array(
                "Attrib7",
                null,
                "HUGnetContainerTestClass2",
                true
            ),
            array(
                "Attrib1",
                null,
                "",
            ),
            array(
                "Attrib7",
                null,
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name        The attribute name to get
    * @param int    $expect      The expected return
    * @param string $class       The class for extra
    * @param array  $expectExtra The expected return from extra
    *
    * @return null
    *
    * @dataProvider dataUnset
    */
    public function testUnset($name, $expect, $class = "", $expectExtra = false)
    {
        $o = new HUGnetContainerTestClass("", $class);
        unset($o->$name);
        if ($expectExtra) {
            //$obj = $this->readAttribute($o, "_extra");
            //$this->assertSame($expect, $obj->$name);
        } else {
            $this->assertSame($expect, $o->$name);
        }
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
                "HUGnetContainerTestClass2",
            ),
            array(
                "Attrib9",
                false,
                "HUGnetContainerTestClass2",
            ),
            array(
                "Attrib1",
                true,
                "",
            ),
            array(
                "Attrib7",
                false,
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The attribute name to get
    * @param int    $expect The expected return
    * @param string $class  The class for extra
    *
    * @return null
    *
    * @dataProvider dataIsset
    */
    public function testIsset($name, $expect, $class = "")
    {
        $o = new HUGnetContainerTestClass("", $class);
        $ret = isset($o->$name);
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
            array(true, "HUGnetContainerTestClass2"),
            array(false, ""),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $expectExtra The expected return from extra
    * @param string $class       The expected class
    *
    * @return null
    *
    * @dataProvider dataClearData
    */
    public function testClearData(
        $expectExtra = false,
        $class = ""
    ) {
        $o = new HUGnetContainerTestClass("", "HUGnetContainerTestClass2");
        $o->clearData();
        if ($expectExtra) {
            //$obj = $this->readAttribute($o, "_extra");
            //$this->assertSame(
            //    $this->readAttribute($obj, "default"),
            //    $this->readAttribute($obj, "data")
            //);
        } else {
            $obj = &$o;
            $this->assertSame(
                $this->readAttribute($o, "default"),
                $this->readAttribute($o, "data")
            );
        }
    }
    /**
    * data provider for testToString
    *
    * @return array
    */
    public static function dataTString()
    {
        return array(
            array("HUGnetContainerTestClass2", "DefaultBlank String"),
            array("", "Default"),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class  The class for extra
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataTString
    */
    public function testToString($class, $expect)
    {
        $o = new HUGnetContainerTestClass("", $class);
        $ret = $o->toString();
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class  The class for extra
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataTString
    */
    public function testToString2($class, $expect)
    {
        $o = new HUGnetContainerTestClass("", $class);
        $this->assertSame(
            $expect,
            (string)$o
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(
                    "Attrib1" => 0,
                    "Attrib2" => "Default",
                    "Attrib3" => new HUGnetContainerTestClass2(),
                    "Attrib4" => array("Hello"),
                    "Attrib5" => "Blank String",
                    "Attrib6" => array("One Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4,
                ),
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1" => 0,
                    "Attrib2" => "Default",
                    "Attrib3" => array(
                        "Attrib5" => "Blank String",
                        "Attrib6" => array("One Element"),
                        "Attrib7" => 1.0,
                        "Attrib8" => 4,
                    ),
                    "Attrib4" => array("Hello"),
                    "Attrib5" => "Blank String",
                    "Attrib6" => array("One Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4,
                ),
            ),
            array(
                "",
                "",
                array(
                    "Attrib1" => 0,
                    "Attrib2" => "Default",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hello"),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload What to preload the object with
    * @param string $class   The class for extra
    * @param array  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($preload, $class, $expect)
    {
        $o = new HUGnetContainerTestClass($preload, $class);
        $ret = $o->toArray();
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
    public static function dataFromArray()
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
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                ),
                array(
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
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $array       The array to use to build it
    * @param string $class       The class for extra
    * @param array  $expect      The expected return
    * @param array  $expectExtra The expected return from extra
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($array, $class, $expect, $expectExtra)
    {
        $o = new HUGnetContainerTestClass("", $class);
        $o->fromArray($array);
        $this->assertSame(
            $expect,
            $this->readAttribute($o, "data")
        );
        /*
        if (!is_null($expectExtra)) {
            $extra = $this->readAttribute($o, "_extra");
            $this->assertSame(
                $expectExtra,
                $this->readAttribute($extra, "data")
            );
        }
        */
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromString()
    {
        return array(
            array(
                serialize(
                    array(
                        "Attrib1" => 10,
                        "Attrib2" => "Hello",
                        "Attrib4" => array("Hi"),
                        "Attrib5" => "Another string",
                        "Attrib6" => array("Two Element"),
                        "Attrib8" => 4.321,
                    )
                ),
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                ),
                array(
                    "Attrib5" => "Another string",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4.321,
                ),
            ),
            array(
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
                ),
                "",
                array(
                    "Attrib1" => 100,
                    "Attrib2" => "Hello There",
                    "Attrib3" => "Some Data",
                    "Attrib4" => array("Hello Everyone"),
                ),
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string      The array to use to build it
    * @param string $class       The class for extra
    * @param array  $expect      The expected return
    * @param array  $expectExtra The expected return from extra
    *
    * @return null
    *
    * @dataProvider dataFromString
    */
    public function testFromString($string, $class, $expect, $expectExtra)
    {
        $o = new HUGnetContainerTestClass("", $class);
        $o->fromString($string);
        $this->assertSame(
            $expect,
            $this->readAttribute($o, "data")
        );
        /*
        if (!is_null($expectExtra)) {
            $extra = $this->readAttribute($o, "_extra");
            $this->assertSame(
                $expectExtra,
                $this->readAttribute($extra, "data")
            );
        }
        */
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
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                ),
                array(
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
                null,
            ),
            array(
                serialize(
                    array(
                        "Attrib1" => 10,
                        "Attrib2" => "Hello",
                        "Attrib4" => array("Hi"),
                        "Attrib5" => "Another string",
                        "Attrib6" => array("Two Element"),
                        "Attrib8" => 4.321,
                    )
                ),
                "HUGnetContainerTestClass2",
                array(
                    "Attrib1" => 10,
                    "Attrib2" => "Hello",
                    "Attrib3" => "Data",
                    "Attrib4" => array("Hi"),
                ),
                array(
                    "Attrib5" => "Another string",
                    "Attrib6" => array("Two Element"),
                    "Attrib7" => 1.0,
                    "Attrib8" => 4.321,
                ),
            ),
            array(
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
                ),
                "",
                array(
                    "Attrib1" => 100,
                    "Attrib2" => "Hello There",
                    "Attrib3" => "Some Data",
                    "Attrib4" => array("Hello Everyone"),
                ),
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $data        The data used to build the stuff
    * @param string $class       The class for extra
    * @param array  $expect      The expected return
    * @param array  $expectExtra The expected return from extra
    *
    * @return null
    *
    * @dataProvider dataConstructorData
    */
    public function testConstructorData($data, $class, $expect, $expectExtra)
    {
        $o = new HUGnetContainerTestClass($data, $class);
        $this->assertSame(
            $expect,
            $this->readAttribute($o, "data")
        );
        /*
        if (!is_null($expectExtra)) {
            $extra = $this->readAttribute($o, "_extra");
            $this->assertSame(
                $expectExtra,
                $this->readAttribute($extra, "data")
            );
        }
        */

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
class HUGnetContainerTestClass extends HUGnetContainer
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

    /**
    * function to check Attrib1
    *
    * @return null
    */
    protected function attrib1()
    {
        $this->Attrib1 = (int) $this->Attrib1;
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        $devInfo = (array) unserialize($string);
        foreach ($this->getAttributes() as $attrib) {
            if (isset($devInfo[$attrib])) {
                $this->$attrib = $devInfo[$attrib];
            }
        }
        parent::fromString($string);
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function toString()
    {
        $string = $this->Attrib2;
        return $string.parent::toString();
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
class HUGnetContainerTestClass2 extends HUGnetContainer
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
    * @return null
    */
    protected function attrib5()
    {
        $this->data["Attrib5"] = (string) $this->data["Attrib5"];
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        $devInfo = (array) unserialize($string);
        foreach ($this->getAttributes() as $attrib) {
            if (isset($devInfo[$attrib])) {
                $this->$attrib = $devInfo[$attrib];
            }
        }
        parent::fromString($string);
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function toString()
    {
        $string = $this->Attrib5;
        return $string.parent::toString();
    }

}

?>
