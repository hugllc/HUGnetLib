<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../interfaces/HUGnetDataRow.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HistoryContainer extends HUGnetContainer implements HUGnetDataRow
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "DeviceKey"  => 0,               // The device that made this record
        "Date"       => "",              // The date for this record
        "elements"   => array(),         // The data
        "deltaT"     => 0,               // The time difference
        "UTCOffset"  => 0,               // The time offset from UTC
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** The number of data elements */
    private $_elements = 19;

    /** @var int The delta time between this element and the next */
    public $DeltaT = null;
    /** @var string The date of this record */
    public $Date = null;
    /** @var object This is where we store our sqlDriver */
    protected $myDevice = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var object This is where we store our sensor driver listing */
    protected $drivers = array();
    /**
    * Builds the class
    *
    * @param array $row     The database row to import
    * @param obj   &$device The device to attach to this record
    *
    * @return null
    */
    public function __construct($row, &$device)
    {
        $this->myConfig = &ConfigContainer::singleton();
        $this->_registerDataPointPlugins();
        $this->DeltaT =& $this->data["deltaT"];
        $this->Date =& $this->data["Date"];
        $this->myDevice = &$device;
        if (is_array($row)) {
            $this->fromArray($row);
        }
    }
    /**
    * Registers all of hte sensor plugins
    *
    * @return null
    */
    private function _registerDataPointPlugins()
    {
        $this->drivers = array();
        foreach ((array)$this->myConfig->plugins->getClass("datapoint") as $d) {
            foreach ($d["Units"] as $u) {
                $this->drivers[$u] = $d["Class"];
            }
        }
    }
    /**
    * Builds the class
    *
    * @param array $row     The database row to import
    * @param obj   &$device The device to attach to this record
    *
    * @return null
    */
    public function &factory($row, &$device)
    {
        $class = __CLASS__;
        return new $class($row, $device);
    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        $fields = array(
            "DeviceKey", "Date", "deltaT", "UTCOffset"
        );
        foreach ($fields as $field) {
            if (isset($array[$field])) {
                $this->$field = $array[$field];
            }
        }
        $this->data["elements"] = array();
        for ($i = 0; $i < $this->_elements; $i++) {
            $field = "Data".$i;
            $this->data["elements"][$i] = &$this->dataPointFactory(
                array(
                    "value" => $array[$field],
                    "units" => $this->myDevice->sensors->sensor($i)->units,
                    "type" => $this->myDevice->sensors->sensor($i)->dataType,
                )
            );
        }
    }

    /**
    * Returns an array of all of the data
    *
    * @param bool $default Not used here
    *
    * @return null
    */
    public function toArray($default = false)
    {
        $array["DeviceKey"] = $this->DeviceKey;
        $array["Date"]      = $this->Date;
        $array["deltaT"]    = $this->deltaT;
        foreach (array_keys((array)$this->data["elements"]) as $key) {
            $data = $this->data["elements"][$key]->value();
            if (!is_null($data)) {
                $array["Data".$key] = $data;
            }
        }
        $array["UTCOffset"]    = $this->UTCOffset;
        return $array;
    }

    /**
    * Creates a sensor from data given
    *
    * @param array $data The data to us to build the class
    *
    * @return Reference to the datapoint on success, null on failure
    */
    protected function &dataPointFactory($data)
    {
        $units = $data["units"];
        if (isset($this->drivers[$units])) {
            $class = $this->drivers[$units];
        } else {
            $class = $this->drivers["DEFAULT"];
        }
        return new $class($data);
    }

}
?>
