<?php
/**
 * Classes for dealing with devices
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
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../Driver.php";
/** This is our interface */
require_once dirname(__FILE__)."/../DriverInterface.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.10.2
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class MagDir extends \HUGnet\devices\outputTable\Driver
    implements \HUGnet\devices\outputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Magnitude/Direction Output Driver",
        "shortName" => "MagDir",
        "extraText" => array(
            "Control Updates / Sec",
            "Mode",
            "Direction Channel",
            "Magnitude Channel",
            "Initial Value"
        ),
        "extraDefault" => array(
            16, 0, 0, 1, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, array(0 => "Normal", 1 => "Invert Sign Output"), array(), array(), 15
        ),
        "extraDesc" => array(
            0 => "The maximum number of times per second that this should update
                  the output.",
            1 => "The mode to use",
            2 => "The control channel we should output the direction to.",
            3 => "The control channel we should output the magnitude to.",
            4 => "The initial value of the control channel",
        ),
        "extraNames" => array(
            "frequency"  => 0,
            "mode"       => 1,
            "dircontrol" => 2,
            "magcontrol" => 3,
            "initvalue"  => 4,
        ),
        "requires" => array("CC", "CC"),
        "provides" => array("CC"),
    );
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if ($name == "extraValues") {
            $chans = $this->output()->device()->controlChannels()->select();
            $ret[2] = $chans;
            $ret[3] = $chans;
        }
        return $ret;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decode($string)
    {
        $extra = $this->output()->get("extra");
        $extra[0] = $this->decodePriority(substr($string, 0, 2));
        $extra[1] = $this->decodeInt(substr($string, 2, 2), 1);
        $extra[2] = $this->decodeInt(substr($string, 4, 2), 1);
        $extra[3] = $this->decodeInt(substr($string, 6, 2), 1);
        $extra[4] = $this->decodeInt(substr($string, 8, 8), 4, true);
        $this->output()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encode()
    {
        $string  = $this->encodePriority($this->getExtra(0));
        $string .= $this->encodeInt($this->getExtra(1), 1);
        $string .= $this->encodeInt($this->getExtra(2), 1);
        $string .= $this->encodeInt($this->getExtra(3), 1);
        $string .= $this->encodeInt($this->getExtra(4), 4);
        return $string;
    }

}


?>
