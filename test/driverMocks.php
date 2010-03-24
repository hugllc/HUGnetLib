<?php
/**
 * Tests the driver class
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
 */
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once HUGNET_INCLUDE_PATH."/driver.php";
require_once HUGNET_INCLUDE_PATH."/drivers/endpoints/eDEFAULT.php";

/**
 * This is a dummy endpoint driver to test the driver class with
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @see        driver, eDEFAULT
 */
class TestDriver extends eDEFAULT
{

    /** history table */
    protected $history_table = "testhistory";
    /** location table
    *  @deprecated This is now stored in the 'params' field in the devices table
    */
    protected $location_table = "testlocation";
    /** Average Table */
    protected $average_table = "testaverage";
    /** Raw history Table */
    protected $raw_history_table = "testhistory_raw";
    /** @var rray shows the devices we can handle */
    var $devices = array(
        "testFW" => array(
            "testHW1" => "DEFAULT",
            "testHW2" => "0.1.2,0.2.3",
       ),
        "DEFAULT" => array(
            "testHW3" => "DEFAULT",
            "ABCD-EF-01-A" => "DEFAULT",
       ),
        "testFW2" => array(
            "testHW4" => "BAD",
       ),
    );
    /**
    * Some Function
    *
    * @param array $arg1 Must be an array
    * @param mixed $arg2 Added to $arg1 array
    * @param mixed $arg3 Added to $arg1 array
    *
    * @return null
    */
    public function test($arg1, $arg2, $arg3)
    {
        if (is_array($arg1)) {
            $arg1['arg2'] = $arg2;
            $arg1['arg3'] = $arg3;
        }
        return $arg1;
    }
    /**
    * Some Function
    *
    * @param mixed $arg1 Function simply returns this value
    *
    * @return mixed
    */
    public function testCall($arg1)
    {
        return $arg1;
    }
    /**
    * Some Function
    *
    * @param array &$Info Infomation about the device to use
    *
    * @return array
    */
    public function interpConfig(&$Info)
    {
        $Info['HWName'] = "Phantom Test Hardware";
        return $Info;
    }
    /**
    * Some Function
    *
    * @return array
    */
    public function getError()
    {
        return array("Errno" => 1, "Error" => "Test Error");
    }
    /**
    * Some Function
    *
    * @param object &$driver An object of class Driver.
    *
    * @return null
    */
    public function __construct(&$driver = null, $config = null)
    {
        if (is_object($driver)) {
            parent::__construct($driver);
        }
    }
    /**
    * Some Function
    *
    * @return null
    */
    public function __toString()
    {
        return "object(".get_class($this).")";
    }

    /**
    * Registers this plugin
    *
    * @param object &$plugins This is the object to register to
    *
    * @return null
    */
    public function register(&$plugins)
    {
        if (!is_object($plugins)) {
            return;
        }
        $plugins->addGeneric(
            array(
                "Name" => "TestDriver",
                "Type" => "driver",
                "Class" => "TestDriver"
            )
        );
    }

}
/**
 * This is a dummy endpoint driver to test the driver class with
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @see        driver, eDEFAULT
 */
class TestDriverNoDrivers extends eDEFAULT
{
    /**
    * Constructor
    *
    * @return null
    */
    public function __construct()
    {
        unset($this->devices);
    }

    /**
    * Registers this plugin
    *
    * @param object &$plugins This is the object to register to
    *
    * @return null
    */
    public function register(&$plugins)
    {
        if (!is_object($plugins)) {
            return;
        }
        $plugins->addGeneric(
            array(
                "Name" => "TestDriverNoDrivers",
                "Type" => "driver",
                "Class" => "TestDriverNoDrivers"
            )
        );
    }
    /**
    * This is just fot isController.  It just needs to exist
    *
    * @return none
    */
    public function checkProgram()
    {
    }
}

?>
