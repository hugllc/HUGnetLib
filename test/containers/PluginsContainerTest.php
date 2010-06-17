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


require_once dirname(__FILE__).'/../../containers/PluginsContainer.php';

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
class PluginsContainerTest extends PHPUnit_Framework_TestCase
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
        //$this->o = new PluginsContainer();
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
        //unset($this->o);        var_dump($this->plugins);

    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(
                    "dir" => dirname(__FILE__)."/../files/plugins/",
                    "extension" => "php",
                ),
                array(
                    "dir" => dirname(__FILE__)."/../files/plugins/",
                    "extension" => ".php",
                ),
                array (
                    'analysis' => array (
                        'testAnalysis' => array (
                            'Name' => 'testAnalysis',
                            'Type' => 'analysis',
                            'Class' => 'TestAnalysisPlugin',
                            'Flags' => array (
                                0 => 'testAnalysis',
                            ),
                        ),
                        'TestAnalysis2' => array (
                            'Name' => 'TestAnalysis2',
                            'Type' => 'analysis',
                            'Class' => 'TestAnalysisPlugin2',
                            'Flags' => array (
                                0 => 'TestAnalysis2',
                            ),
                        ),
                    ),
                    'datapoint' => array (
                        'moreUnit' => array (
                            'Name' => 'Test2DataPoint',
                            'Type' => 'datapoint',
                            'Class' => 'Test2DataPoint',
                            'Flags' => array (
                                0 => 'moreUnit',
                                1 => 'DEFAULT',
                            ),
                        ),
                        'DEFAULT' => array (
                            'Name' => 'Test2DataPoint',
                            'Type' => 'datapoint',
                            'Class' => 'Test2DataPoint',
                            'Flags' => array (
                                0 => 'moreUnit',
                                1 => 'DEFAULT',
                            ),
                        ),
                        'firstUnit' => array (
                            'Name' => 'TestDataPoint',
                            'Type' => 'datapoint',
                            'Class' => 'TestDataPoint',
                            'Flags' =>
                            array (
                                0 => 'firstUnit',
                                1 => 'testUnit',
                            ),
                        ),
                        'testUnit' => array (
                            'Name' => 'TestDataPoint',
                            'Type' => 'datapoint',
                            'Class' => 'TestDataPoint',
                            'Flags' =>
                            array (
                                0 => 'firstUnit',
                                1 => 'testUnit',
                            ),
                        ),
                    ),
                    'deviceProcess' => array (
                        'DeviceProcessAnalysis' => array (
                            'Name' => 'DeviceProcessAnalysis',
                            'Type' => 'deviceProcess',
                            'Class' => 'TestDeviceProcessPlugin',
                            'Flags' => array (
                                0 => 'DeviceProcessAnalysis',
                            ),
                        ),
                        'DeviceProcessAnalysis2' => array (
                            'Name' => 'DeviceProcessAnalysis2',
                            'Type' => 'deviceProcess',
                            'Class' => 'TestDeviceProcessPlugin2',
                            'Flags' => array (
                                0 => 'DeviceProcessAnalysis2',
                            ),
                        ),
                    ),
                    'device' => array (
                        "0039-24-67-C:0039-CE-01-A:DEFAULT" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),

                        "0039-24-67-C:0039-CF-01-A:0.1.2" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),

                        "0039-24-67-C:0039-CF-01-A:0.2.3" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),

                        "DEFAULT:0039-CB-01-A:DEFAULT" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),

                        "DEFAULT:0039-CD-01-A:DEFAULT" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),

                        "0039-25-67-C:0039-CC-01-A:BAD" => array (
                            'Name' => 'testDriver',
                            'Type' => 'device',
                            'Class' => 'TestDriverPlugin',
                            "Flags" => array(
                                "0039-24-67-C:0039-CE-01-A:DEFAULT",
                                "0039-24-67-C:0039-CF-01-A:0.1.2",
                                "0039-24-67-C:0039-CF-01-A:0.2.3",
                                "DEFAULT:0039-CB-01-A:DEFAULT",
                                "DEFAULT:0039-CD-01-A:DEFAULT",
                                "0039-25-67-C:0039-CC-01-A:BAD",
                            ),
                        ),
                    ),
                    'periodic' => array (
                        'testPeriodic' => array (
                            'Name' => 'testPeriodic',
                            'Type' => 'periodic',
                            'Class' => 'TestPeriodicPlugin',
                            'Flags' => array (
                                0 => 'testPeriodic',
                            ),
                        ),
                        'testPeriodic2' => array (
                            'Name' => 'testPeriodic2',
                            'Type' => 'periodic',
                            'Class' => 'TestPeriodicPlugin2',
                            'Flags' => array (
                                0 => 'testPeriodic2',
                            ),
                        ),
                    ),
                    'sensor' => array (
                        'DEFAULT' => array (
                            'Name' => 'Test1Sensor',
                            'Type' => 'sensor',
                            'Class' => 'Test1Sensor',
                            'Flags' => array (
                                0 => 'DEFAULT',
                            ),
                        ),
                        '02' => array (
                            'Name' => 'Test2Sensor',
                            'Type' => 'sensor',
                            'Class' => 'Test2Sensor',
                            'Flags' => array (
                                0 => '02',
                                1 => '03:Hello',
                                2 => '04',
                                3 => '05',
                            ),
                        ),
                        '03:Hello' => array (
                            'Name' => 'Test2Sensor',
                            'Type' => 'sensor',
                            'Class' => 'Test2Sensor',
                            'Flags' =>
                            array (
                                0 => '02',
                                1 => '03:Hello',
                                2 => '04',
                                3 => '05',
                            ),
                        ),
                        '04' => array (
                            'Name' => 'Test2Sensor',
                            'Type' => 'sensor',
                            'Class' => 'Test2Sensor',
                            'Flags' => array (
                                0 => '02',
                                1 => '03:Hello',
                                2 => '04',
                                3 => '05',
                            ),
                        ),
                        '05' => array (
                            'Name' => 'Test2Sensor',
                            'Type' => 'sensor',
                            'Class' => 'Test2Sensor',
                            'Flags' => array (
                                0 => '02',
                                1 => '03:Hello',
                                2 => '04',
                                3 => '05',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "dir" => dirname(__FILE__)."/../files/plugins/",
                    "extension" => ".inc.php",
                ),
                array(
                    "dir" => dirname(__FILE__)."/../files/plugins/",
                    "extension" => ".inc.php",
                ),
                array (
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload The values to preload into the object
    * @param mixed $data    The expected data variable
    * @param mixed $plugins The expected plugins variable
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $data, $plugins)
    {
        $o = new PluginsContainer($preload);
        $this->assertAttributeSame($data, "data", $o);
        $this->assertAttributeSame($plugins, "plugins", $o);
    }


    /**
    * data provider for testGetPlugin
    *
    * @return array
    */
    public static function dataGetPlugin()
    {
        return array(
            array(
                array(
                    "dir" => dirname(__FILE__)."/../files/plugins/",
                    "extension" => "php",
                ),
                "periodic",
                array (
                    'testPeriodic' => array (
                        'Name' => 'testPeriodic',
                        'Type' => 'periodic',
                        'Class' => 'TestPeriodicPlugin',
                        'Flags' => array (
                            0 => 'testPeriodic',
                        ),
                    ),
                    'testPeriodic2' => array (
                        'Name' => 'testPeriodic2',
                        'Type' => 'periodic',
                        'Class' => 'TestPeriodicPlugin2',
                        'Flags' => array (
                            0 => 'testPeriodic2',
                        ),
                    ),
                ),

            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to preload
    * @param string $type    The type to get
    * @param mixed  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataGetPlugin
    */
    public function testGetPlugin($preload, $type, $expect)
    {
        $o = new PluginsContainer($preload);
        $this->assertSame($expect, $o->getPlugin($type));
    }


    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $o = new PluginsContainer($preload);
        $o->$var = $value;
        $data = $this->readAttribute($o, "data");
        $this->assertSame($expect, $data[$var]);
    }
    /**
    * data provider for testToString
    *
    * @return array
    */
    public static function data2String()
    {
        return array(
            array(array(), "YTowOnt9"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The data to preload into the class
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider data2String
    */
    public function testToString($preload, $expect)
    {
        $o = new PluginsContainer($preload);
        $o->fromAny($preload);
        $this->assertSame($expect, $o->toString());
    }
    /**
    * data provider for testToString
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(array(), array()),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload The data to preload into the class
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($preload, $expect)
    {
        $o = new PluginsContainer($preload);
        $o->fromAny($preload);
        $this->assertSame($expect, $o->toArray());
    }


}

?>
