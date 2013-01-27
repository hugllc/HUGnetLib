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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is a required class */
require_once CODE_BASE.'/ui/CLI.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevicesTest extends \PHPUnit_Framework_TestCase
{
    /** Files that have been created */
    private $_files = array();
    /** This is the system object */
    protected $system;
    /** THis is the config */
    protected $config;
    /** THis is the ui object */
    protected $ui;

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
        $this->config = array(
            'uuid' => '743d933e-2d6e-4f62-9768-42ad204026d2',
            'GatewayKey' => '1',
            'servers' => array(
                'default' => array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "default",
                ),
            ),
            'network' => array(
                'default' => array (
                    'driver' => 'Local',
                    'quiet' => true,
                ),
                "noLocal" => true,
            ),
            'quiet' => false,
            'verbose' => -1,
            'debug' => false,
            'test' => false,
        );
        $this->ui     = \HUGnet\ui\CLI::factory($this->config);
        $this->system = $this->ui->system();
        $this->pdo    = $this->system->dbconnect()->getPDO("default");
        // Create tables to be filled
        $tables = array(
            "Datacollectors", "DeviceInputs", "DeviceOutputs", "DeviceProcesses",
            "Devices", "InputTable", "E00393700Average", "E00393700History",
            "ETESTHistory", "ETESTAverage"
        );
        foreach ($tables as $name) {
            $table = $this->system->table($name);
        }
        // This fills the tables
        $sql = file_get_contents(TEST_INTEGRATION_BASE."/sql/HUGnet_1.sql");
        foreach (explode("--", $sql) as $query) {
            $this->pdo->exec($query);
        }
        // This creates our device
        $dev = array(
            "id" => 0xFEF88E,
            "DeviceID" => "FEF88E",
        );
        $this->system->network()->device($dev);
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
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataGetDevice()
    {
        return array(
            array(
                0x1016,
                array(
                    "id" => 0x1016,
                    "DeviceID" => "001016",
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param int    $device The device to get
    * @param string $expect The system object we are expecting
    *
    * @return null
    *
    * @dataProvider dataGetDevice
    */
    public function testGetDevice($device, $expect)
    {
        $obj = $this->system->device($device);
        $ret = array();
        $this->assertInstanceOf("\\HUGnet\\Device", $obj, "Wrong object given");
        foreach (array_keys((array)$expect) as $name) {
            $ret[$name] = $obj->get($name);
        }
        $this->assertEquals($expect, $ret, "Wrong Device given");
    }

}
?>
