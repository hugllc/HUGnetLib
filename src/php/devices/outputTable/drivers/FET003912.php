<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverAVR.php";
/** This is our interface */
require_once dirname(__FILE__)."/../DriverInterface.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.10.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class FET003912 extends \HUGnet\devices\outputTable\DriverAVR
    implements \HUGnet\devices\outputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "General Purpose IO",
        "shortName" => "GPIO",
        "extraText" => array(
            "Control Updates / Sec",
            "Port",
            "Mode"
        ),
        "extraDefault" => array(
            128, 0, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4,
            array(
                0 => "Port 1",
                1 => "Port 2",
                2 => "Port 3",
                3 => "Port 4",
            ),
            array(
                0 => "Digital",
                1 => "Analog",
                2 => "High Impedance",
            ),
        ),
        "extraDesc" => array(
            0 => "The maximum number of times per second that this should update
                  the output.",
            1 => "The port we should go out",
            2 => "The mode for the port.",
        ),
        "extraNames" => array(
            "frequency" => 0,
            "port"      => 1,
            "mode"      => 2,
        ),
        "min" => -256,
        "max" => +256,
        "zero" => 0,
        "requires" => array("DO"),
        "provides" => array("CC"),
    );

    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $extra = (array)$this->output()->get("extra");
        $extra[0] = $this->decodePriority(substr($string, 0, 2));
        $extra[2] = $this->decodeInt(substr($string, 2, 2), 1);
        $extra[1] = $this->decodeInt(substr($string, 4, 2), 1);
        $this->output()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $string  = $this->encodePriority($this->getExtra(0));
        $string .= $this->encodeInt($this->getExtra(2), 1);
        $string .= $this->encodeInt($this->getExtra(1), 1);
        return $string;
    }
    /**
    * Returns the port this data channel is attached to
    *
    * @return array
    */
    protected function port()
    {
        $value = $this->getExtra(1);
        return "Port".($value + 1);
    }

}


?>
