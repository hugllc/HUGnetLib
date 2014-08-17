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
namespace HUGnet\devices\functions\drivers;
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
 * @since      0.14.3
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Temperature extends \HUGnet\devices\functions\Driver
    implements \HUGnet\devices\functions\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName"  => "Temperature Sensor",
        "shortName" => "Temp",
        "type"      => "Simple",
        "extraText" => array(
            "Driver"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(),
        ),
        "extraDefault" => array(
            "NoOp",
        ),
        "extraDesc" => array(
            "The temperature sensor type to use"
        ),
        "extraNames" => array(
            "driver" => 0,
        ),
    );
    /**
    * This is where we get the temperature sensor types for this device
    */
    protected $archType = array(
        "0039-12" => array(
            "AVRBC2322640"       => "BC Components 2322640 Thermistor",
            "AVRB57560G0103F000" => "EPCOS B57560G0103F000 Thermistor",
            "AVRIMCSolar"        => "IMC Solar Thermistor",
        ),
        "0039-28" => array(
            "AVRBC2322640"       => "BC Components 2322640 Thermistor",
            "AVRB57560G0103F000" => "EPCOS B57560G0103F000 Thermistor",
            "AVRIMCSolar"        => "IMC Solar Thermistor",
        ),
        "0039-37" => array(
            "ADuCThermocouple" => "Thermocouple",
            "ADuCMF51E"        => "Cantherm MF51E Thermistor",
            "ADuCScaledTemp"   => "Scaled Temperature Sensor",
            "ADuCUSSensorRTD"  => "USSensor RTD",
            "ADuCVishayRTD"    => "Vishay RTD",
        ),
        "all" => array(
        ),
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
            $arch = $this->fct()->device()->get("arch");
            $ret[0] = array_merge(
                (array)$this->archType["all"], (array)$this->archType[$arch]
            );
        } else if ($name == "extraDefault") {
            $arch  = $this->fct()->device()->get("arch");
            $array = (array)$this->archType[$arch];
            reset($array);
            $key = (string)key($array);
            if (trim($key) != "") {
                $ret[0] = $key;
            }
        }
        return $ret;
    }
    /**
    * Applies this function
    *
    * @return null
    */
    public function execute()
    {
        $arch = $this->fct()->device()->get("arch");
        $ret  = false;
        switch ($arch) {
        case "0039-12":
            $ret = $this->_executeAVR();
            break;
        case "0039-28":
            $ret = $this->_executeAVR();
            break;
        case "0039-37":
            $ret = $this->_execute003937();
            break;
        }
        return $ret;
    }
    /**
    * Applies this function to 0039-28
    *
    * @return null
    */
    private function _executeAVR()
    {
        include_once dirname(__FILE__)."/../../inputTable/Driver.php";
        $input = $this->fct()->device()->input("free");
        $input->set("id", 0xF9);
        $input->set("location", $this->fct()->get("name"));
        $entry = (array)$input->toArray(false)["tableEntry"];
        $ports = $input->ports();
        foreach ($ports as $key => $port) {
            $cnt = $this->checkPort($port, array("AI"));
            if ($cnt >= 0) {
                break;
            }
        }
        $driver = $this->getExtra(0);
        $driverID = \HUGnet\devices\inputTable\Driver::getDriverID($driver);
        list($sid, $sub) = explode(":", $driverID);
        $entry = array_merge(
            $entry,
            array(
                "driver" => hexdec($sid),
                "MUX" => $key,
            )
        );
        $input->table()->set("tableEntry", $entry);
        return $input->store(true);
    }
    /**
    * Applies this function to 0039-37
    *
    * @return null
    */
    private function _execute003937()
    {
        include_once dirname(__FILE__)."/../../inputTable/Driver.php";
        $input = $this->fct()->device()->input("free");
        $input->set("id", 0xF9);
        $input->set("location", $this->fct()->get("name"));
        
        $entry = (array)$input->toArray(false)["tableEntry"];
        foreach (array(0, 1) as $index) {
            $ports = $input->ports($index);
            foreach ($ports as $key => $port) {
                if ($this->portAvailable($port)) {
                    $cnt = $this->portCheck($port, array("AI"));
                    if ($cnt >= 0) {
                        break;
                    }
                }
            }
            if ($cnt >= 0) {
                break;
            }
        }
        $driver = $this->getExtra(0);
        $driverID = \HUGnet\devices\inputTable\Driver::getDriverID($driver);
        list($sid, $sub) = explode(":", $driverID);
        $entry = array_merge(
            $entry,
            array(
                "driver0" => hexdec($sid),
                "ADC".$index."EN" => 1,
                "ADC".$index."CH" => $key,
            )
        );
        $input->table()->set("tableEntry", $entry);
        return $input->store(true);
    }
}


?>
