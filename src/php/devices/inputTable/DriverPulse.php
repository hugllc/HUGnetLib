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
namespace HUGnet\devices\inputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our units class */
require_once dirname(__FILE__)."/Driver.php";
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class DriverPulse extends Driver
{
    /** This is where our port is stored */
    protected $portExtra = 1;
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $param = parent::get($name);
        switch ($name) {
        case "extraValues":
            $param = (array)$param;
            $param[$this->portExtra] = $this->input()->device()->get(
                "DigitalInputs"
            );
        }
        return $param;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $extra = $this->pDecode($string, 0);
        $this->input()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        return $this->pEncode(0);
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string &$string The string to decode
    * @param array  &$extra  The extra stuff to use
    * @param int    $index   The index to start in the extra
    *
    * @return array
    */
    protected function pDecode(&$string, $index)
    {
        $extra = $this->input()->get("extra");
        $extra[$index] = $this->decodeInt(substr($string, 2, 2), 1);
        $index++;
        $extra[$index] = $this->decodeInt(substr($string, 4, 2), 1);
        $index++;
        $extra[$index] = $this->decodeInt(substr($string, 6, 2), 1);
        $string = substr($string, 8);
        return $extra;
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param int $index The index to start in the extra
    * 
    * @return array
    */
    protected function pEncode($index = 0)
    {
        $string = sprintf("%02X",  $driver,
            $this->subdriver[$driver][$drivers[1]]
        );
        $string  = $this->encodeInt(0, 1); // This is a placeholder for subdriver
        $string .= $this->encodeInt($this->getExtra($index), 1);
        $index++;
        $string .= $this->encodeInt($this->getExtra($index), 1);
        $index++;
        $string .= $this->encodeInt($this->getExtra($index), 1);
        return $string;
    }
}


?>
