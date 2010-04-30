<?php
/**
 * This is the driver code for the HUGnet data collector (0039-26).
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// This is our base class
require_once dirname(__FILE__).'/../../base/DeviceDriverBase.php';
// This is the interface we are implementing
require_once dirname(__FILE__).'/../../interfaces/DeviceDriverInterface.php';
require_once dirname(__FILE__).'/../../interfaces/PacketConsumerInterface.php';

/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class E00392100Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392100",
        "Type" => "device",
        "Class" => "E00392100Device",
        "Devices" => array(
            "DEFAULT" => array(
                "0039-21-01-A" => "DEFAULT",
                "0039-21-02-A" => "DEFAULT",
            ),
        ),
    );
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        $this->myDriver = &$obj;
        $this->myDriver->DriverInfo = array();
        $this->myDriver->DriverInfo["NumSensors"] = 16;
        $this->myDriver->DriverInfo["PacketTimeout"] = 2;
        $this->fromString($string);
    }

    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toString($default = true)
    {
        $string = "";
        return $string;

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
        $this->myDriver->DriverInfo["TimeConstant"] = hexdec(substr($string,0 , 2));
        $this->myDriver->sensors->fromTypeString(substr($string, 2));
    }

    /**
    * Reads the setup out of the device
    *
    * This device needs to be checked more often.  This changes the check time from
    * hours to minutes.
    *
    * @param int $interval The interval to check, in hours
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTime($interval = 10)
    {
        return (strtotime($this->myDriver->LastConfig) < (time() - $interval*60));
    }

}

?>
