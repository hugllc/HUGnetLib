<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This is the base class */
require_once CODE_BASE."/devices/Driver.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverTestBase extends \PHPUnit_Framework_TestCase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is the object we are testing */
    protected $o = null;

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
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testFactory()
    {
        $this->assertSame(
            "HUGnet\devices\drivers\\".$this->class, get_class($this->o)
        );
    }

    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataInternalType()
    {
        return array(
            array("packetTimeout", "int"),
            array("totalSensors", "int"),
            array("physicalSensors", "int"),
            array("virtualSensors", "int"),
            array("historyTable", "string"),
            array("averageTable", "string"),
            array("loadable", "boolean"),
            array("bootloader", "boolean"),
            array("DigitalInputs", "array"),
            array("DigitalOutputs", "array"),
            array("DaughterBoards", "array"),
            array("DataChannels", "int"),
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field The field to check
    * @param string $type  The type it should be
    *
    * @return null
    *
    * @dataProvider dataInternalType
    */
    final public function testInternalType($field, $type)
    {
        $name = $this->o->get($field);
        $this->assertInternalType($type, $name, "$field must be a $type");
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataStringSize()
    {
        return array(
            array("historyTable", 80, 5),
            array("averageTable", 80, 5),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMaxSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field);
        $this->assertLessThanOrEqual(
            $max, strlen($name), "$field must be less than $max chars"
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMinSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field);
        $this->assertGreaterThanOrEqual(
            $min, strlen($name), "$field must be more than $min characters"
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataIntSize()
    {
        return array(
            array("packetTimeout", 10, 2),
            array("totalSensors", 30, 0),
            array("physicalSensors", 20, 0),
            array("virtualSensors", 30, 0),
            array("DataChannels", 50, 0),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataIntSize
    */
    final public function testIntMaxSize($field, $max, $min)
    {
        if (!is_null($max)) {
            $name = (string)$this->o->get($field);
            $this->assertLessThanOrEqual(
                $max, $name, "$field must be less than $max chars"
            );
        }
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataIntSize
    */
    final public function testIntMinSize($field, $max, $min)
    {
        if (!is_null($min)) {
            $name = (string)$this->o->get($field);
            $this->assertGreaterThanOrEqual(
                $min, $name, "$field must be more than $min characters"
            );
        }
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataValues()
    {
        return array(
            array(
                "type", array(
                    'unknown', 'script', 'bootloader', 'endpoint', 'controller',
                    'fastvirtual', 'slowvirtual', 'test'
                ),
            ),
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param array  $values The type it should be
    *
    * @return null
    *
    * @dataProvider dataValues
    */
    final public function testValues($field, $values)
    {
        $name = $this->o->get($field, 1);
        $this->assertTrue(
            in_array($name, (array)$values),
            "$field must be one of ".implode(",", (array)$values)
        );
    }
    /**
    * Check the number of sensors
    *
    * @return null
    *
    * @dataProvider dataIntSize
    */
    final public function testNumberOfSensors()
    {
        $total = (int)$this->o->get("totalSensors");
        $phy = (int)$this->o->get("physicalSensors");
        $vir = (int)$this->o->get("virtualSensors");
        $this->assertEquals(
            $total, ($phy + $vir),
            "physicalSensors and virtualSensors must add up to totalSensors"
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
                "ThisIsABadName",
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $expect)
    {
        $this->assertSame($expect, $this->o->get($name));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataNetworkFunctions()
    {
        return array(
            array("config"),
            array("poll"),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name The name of the variable to test.
    *
    * @return null
    *
    * @dataProvider dataNetworkFunctions
    */
    public function testNetworkFunctions($name)
    {
        if (method_exists($this->o, $name)) {
            $ret = $this->o->$name();
            $this->assertInternalType(
                "array", $ret, "Return of $name must be an array"
            );
            $this->assertGreaterThanOrEqual(
                1, count($ret), "Return of $name must have at least 1 element"
            );
            foreach ($ret as $key => $pkt) {
                $this->assertInternalType(
                    "array", $pkt,
                    "Element $key returned by $name must be an array"
                );
                $this->assertInternalType(
                    "string", $pkt["Command"],
                    "'Command' must be a string in element array $key"
                    ." returned by $name"
                );
                $this->assertGreaterThanOrEqual(
                    2, strlen($pkt["Command"]),
                    "'Command' must be at least two characters in element array $key"
                    ." returned by $name"
                );
            }
        } else {
            $this->assertFalse(method_exists($this->o, $name));
        }
    }
}
?>
