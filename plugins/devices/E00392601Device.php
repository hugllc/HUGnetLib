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
class E00392601Device extends DeviceDriverBase
    implements DeviceDriverInterface, PacketConsumerInterface
{
    /** @var int The job number for polling */
    const JOB_POLL     = 1;
    /** @var int The job number for updatedb */
    const JOB_UPDATEDB = 2;
    /** @var int The job number for analysis */
    const JOB_ANALYSIS = 3;
    /** @var int The job number for endpoint */
    const JOB_ENDPOINT = 4;
    /** @var int The job number for control */
    const JOB_CONTROL  = 5;
    /** @var int The job number for config */
    const JOB_CONFIG   = 6;
    /** @var int The job number for check */
    const JOB_CHECK    = 7;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392601",
        "Type" => "device",
        "Class" => "E00392601Device",
        "Devices" => array(
            "DEFAULT" => array(
                "0039-26-01-P" => "DEFAULT",
                "0039-26-02-P" => "DEFAULT",
                "0039-26-03-P" => "DEFAULT",
                "0039-26-04-P" => "DEFAULT",
                "0039-26-05-P" => "DEFAULT",
                "0039-26-06-P" => "DEFAULT",
                "0039-26-07-P" => "DEFAULT",
            ),
        ),
    );
    /** @var array These define what jobs this driver might see */
    protected $jobs = array(
        self::JOB_POLL     => "Poll",
        self::JOB_UPDATEDB => "Updatedb",
        self::JOB_ANALYSIS => "Analysis",
        self::JOB_ENDPOINT => "Endpoint",
        self::JOB_CONTROL  => "Control",
        self::JOB_CONFIG   => "Config",
        self::JOB_CHECK    => "Check",
    );

    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toString($default = true)
    {
        $this->Info = &$this->myDriver->DriverInfo;
        $string  = $this->myDriver->hexify($this->Info["Job"], 2);
        $string .= $this->myDriver->hexify($this->myDriver->GatewayKey, 4);
        $string .= $this->myDriver->hexifyStr(
            $this->myDriver->DriverInfo["Name"], 60
        );

        $myIP = explode(".", $this->Info["IP"]);

        for ($i = 0; $i < 4; $i++) {
            $string .= $this->myDriver->hexify($myIP[$i], 2);
        }

        $string .= $this->myDriver->hexify($this->Info["Priority"], 2);
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
        $this->Info = &$this->myDriver->DriverInfo;
        $index = 0;
        // This byte is currently not used
        $this->Info["Job"] = hexdec(substr($string, $index, 2));
        $this->Info["Function"] = $this->_getFunction($this->Info["Job"]);

        $index += 2;
        $this->Info["CurrentGatewayKey"] = hexdec(substr($string, $index, 4));

        $index += 4;
        $this->Info["Name"] = $this->myDriver->deHexify(
            trim(strtoupper(substr($string, $index, 60)))
        );
        $this->Info["Name"] = trim($this->Info["Name"]);

        $index += 60;
        $IP     = str_split(substr($string, $index, 8), 2);
        $index += 8;

        foreach ($IP as $k => $v) {
            $IP[$k] = hexdec($v);
        }
        $this->Info["IP"] = implode(".", $IP);

        $this->Info["Priority"] = hexdec(substr($string, $index, 2));
    }
    /**
    * This takes the numeric job and replaces it with a name
    *
    * @param int $job The job
    *
    * @return string
    */
    private function _getFunction($job)
    {
        if (!empty($this->jobs[$job])) {
            return $this->jobs[$job];
        }
        return "Unknown";
    }
    /**
    * This takes the numeric job and replaces it with a name
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    public function packetConsumer(PacketContainer &$pkt)
    {
        if ($pkt->Command == PacketContainer::COMMAND_GETSETUP) {
            $pkt->reply((string)$this->myDriver);
        } else if (($pkt->Command == PacketContainer::COMMAND_ECHOREQUEST)
            || ($pkt->Command == PacketContainer::COMMAND_FINDECHOREQUEST)
        ) {
            $pkt->reply($pkt->Data);
        }
    }

}

?>
