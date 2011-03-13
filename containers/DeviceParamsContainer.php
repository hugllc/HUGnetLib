<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../interfaces/OutputInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceParamsContainer extends HUGnetContainer implements OutputInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "graphUnits" => array(),        // The units to use in the graph
        "DriverInfo" => array(),        // Persistant storage for the driver
        "ProcessInfo" => array(),       // Persistant storage for the processes
        "LastContact" => 0,             // The last time the dev was contacted
        "LastModified" => 0,            // The last time we were modified.
        "LastModifiedBy" => "",         // The name of the last person to modify me
        "outputFilters" => array(),     // Any filters for the data output

        // These are for backwards compatibility and upgrading the database.
        // They will be removed in a few versions.
        "Loc" => array(),               // The location of the sensors
        "sensorType" => array(),        // The type of the sensors
        "Units" => array(),             // The units to use on the sensors
        "dType" => array(),             // The datatype of each sensor
        "Extra" => array(),             // Extra input for crunching numbers
        "Raw"   => array(),             // Array of raw setup stuff
    );

    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = false)
    {
        return parent::toString($default);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = false)
    {
        return parent::toArray($default);
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutput($cols = null)
    {
        $ret = array_merge((array)$this->ProcessInfo, (array)$this->DriverInfo);
        $headers = array("LastContact", "LastModified", "LastModifiedBy");
        foreach ($headers as $key) {
            $ret[$key] = $this->$key;
        }
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutputHeader($cols = null)
    {
        return (array)$cols;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param string $type The output plugin type
    * @param array  $cols The columns to get
    *
    * @return array
    */
    public function outputParams($type, $cols = null)
    {
        return array();
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function outputFilters($cols = null)
    {
        return array();
    }

}
?>
