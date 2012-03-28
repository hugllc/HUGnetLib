<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/sensors/VoltageDeviceSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCThermocoupleDeviceSensor extends VoltageDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "ADuC Thermocouple",
        "Type" => "sensor",
        "Class" => "ADuCThermocoupleDeviceSensor",
        "Flags" => array("42"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x42);
    /** @var object These are the valid values for type */
    protected $typeValues = array("ADuCThermocouple");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "ADuC Thermocouple",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "AtoD Ref Voltage (mV)"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5),
        "extraDefault" => array(1, 10, 1.2),
        "maxDecimals" => 8,
    );
    /** These are the coeffients of the thermocouple equasion */
    protected $coeffients = array(
        "k" => array(
            /*
            0 => array(
                0, 2.5173462E1, -1.1662878, -1.0833638, -8.9773540E-1,
                -3.7342377E-1, -8.6632643E-2, -1.0450598E-2, -5.1920577E-4
            ),*/
            500 => array(
                0, 2.508355E1, 7.860106E-2, -2.503131E-1, 8.315270E-2,
                -1.228034E-2, 9.804036E-4, -4.413030E-5, 1.057734E-6,
                -1.052755E-8
            ),/*
            1372 => array(
                -1.318058E2, 4.830222E1, -1.646031, 5.464731E-2, -9.650715E-4,
                8.802193E-6, -3.110810E-8
            ),*/
        ),
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x42;
        $this->default["type"] = "ADuCThermocouple";
        parent::__construct($data, $device);
        $this->set("units", '&#176;C');
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(2);

        $A = $this->getTwosCompliment($A, 32);
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        $T = $this->getThermocouple($Va, $data[0]["value"], "k");
        return round($T, $this->maxDecimals);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $V     Voltage output of thermocouple in milliVolts
    * @param float $TCold Cold junction temperature in degrees C
    * @param float $type  Thermocouple type
    *
    * @return mixed The temperature
    */
    public function getThermocouple($V, $TCold, $type = "k")
    {
        bcscale(50);
        foreach (array_keys($this->coeffients[$type]) as $k) {
            $T = 0;
            $c = &$this->coeffients[$type][$k];
            for ($i = 0; isset($c[$i]); $i++) {
                $T = bcadd($T, bcmul($c[$i], (float)bcpow($V, $i)));
            }
            $T += $TCold;
            if ($T < $k) {
                /* We are at the right scale, so break */
                break;
            }
        }
        return $T;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
