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
namespace HUGnet\devices\powerTable\drivers;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Battery extends \HUGnet\devices\powerTable\Driver
    implements \HUGnet\devices\powerTable\DriverInterface
{

    const LEADACID = 0;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Battery Driver",
        "shortName" => "Battery",
        "extraDesc" => array(
            0 => "The type of battery this is.",
            1 => "The priority of this battery to be charged",
            2 => "The capacity of the battery in mAs",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            0 => array(self::LEADACID => "Lead Acid"),
            1 => array(
                0 => "Highest", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 
                5 => "5", 6 => "6", 7 => "Lowest"
            ),
            2 => 10,
        ),
    );
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    protected function entryClass()
    {
        $dir = dirname(__FILE__)."/../tables/";
        $namespace = "\\HUGnet\\devices\\powerTable\\tables\\";
        $type = $this->getExtra(0);
        switch ($type) {
            case self::LEADACID:
            default:
                include_once $dir."LeadAcidBatteryTable.php";
                $class = $namespace."LeadAcidBatteryTable";
                break;
        }
        return $class;
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encodeCapacity($capacity)
    {
        return $this->encodeInt($capacity * 1000 * 3600, 4);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function decodeCapacity($string)
    {
        return $this->decodeInt($string, 4)/1000/3600;
    }
}


?>
