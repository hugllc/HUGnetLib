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
class E00391200Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00391200",
        "Type" => "device",
        "Class" => "E00391200Device",
        "Flags" => array(
            "0039-11-02-B:0039-12-00-A:DEFAULT",
            "0039-11-02-B:0039-12-01-A:DEFAULT",
            "0039-11-02-B:0039-12-02-A:DEFAULT",
            "0039-11-02-B:0039-12-01-B:DEFAULT",
            "0039-11-02-B:0039-12-02-B:DEFAULT",
            "0039-11-03-B:0039-12-00-A:DEFAULT",
            "0039-11-03-B:0039-12-01-A:DEFAULT",
            "0039-11-03-B:0039-12-02-A:DEFAULT",
            "0039-11-03-B:0039-12-01-B:DEFAULT",
            "0039-11-03-B:0039-12-02-B:DEFAULT",
            "0039-20-02-C:0039-12-02-A:DEFAULT",
            "0039-20-02-C:0039-12-02-B:DEFAULT",
            "0039-20-03-C:0039-12-02-A:DEFAULT",
            "0039-20-03-C:0039-12-02-B:DEFAULT",
            "0039-20-07-C:0039-12-02-A:DEFAULT",
            "0039-20-07-C:0039-12-02-B:DEFAULT",
            "DEFAULT:0039-12-00-A:DEFAULT",
            "DEFAULT:0039-12-01-A:DEFAULT",
            "DEFAULT:0039-12-02-A:DEFAULT",
            "DEFAULT:0039-12-01-B:DEFAULT",
            "DEFAULT:0039-12-02-B:DEFAULT",
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
        parent::__construct($obj, $string);
        $this->myDriver->DriverInfo["NumSensors"] = 9;
        $this->fromSetupString($string);
    }

}

?>
