<?php
/**
 * Sensor driver for Digital Inputs
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2015 Hunt Utilities Group, LLC
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
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../Driver.php";
/**
 * This class deals with digital inputs.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class DigitalInput extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Digital Input",
        "shortName" => "Digital",
        "unitType" => "LogicLevel",
        "storageUnit" => 'State',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
        "Port",
        "Logic"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(),
            array()
        ),
        "extraDefault" => array(2, 0),
        "extraDesc" => array(
            "The input port number",
            "The type of logic, normal or inverted",
        ),
        "extraNames" => array(
            "port0"      => 0,
            "logic"      => 1,
        ),
        "maxDecimals" => 0,
        "inputSize" => 4,
        "total" => true,
        "requires" => array("DI"),
        "provides" => array("DC"),
    );

    /*
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
            $param[0] = $this->input()->device()->get(
                "DigitalInputs"
            );
            $param[1] = $this->input()->device()->get(
                "LogicStates"
            );
        }
        return $param;
    }


    /**
    * This function returns the digital input logic level
    *
    * @param int   $A      Data value from port
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        
        $bits = 32;
        /* Clear off any excess */
        $A = (int)($A & (pow(2, $bits) - 1));
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($A & $topBit) == $topBit) {
            /* This is a negative number */
            $A = -(pow(2, $bits) - $A);
        }

        return (int)$A;
    }

    /**
    * Returns the reversed reading
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if (is_null($value) || ($value < 0)) {
            return null;
        }

        return (int)$value;
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
    * @param int    $index   The index to start in the extra
    *
    * @return array
    */
    protected function pDecode(&$string, $index)
    {
        $extra = $this->input()->get("extra");
        $extra[$index] = $this->decodeInt(substr($string, 6, 2), 1);
        $index++;
        $extra[$index] = $this->decodeInt(substr($string, 8, 2), 1);
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
        
        $string  = "00"; /* place holder for subdriver */
        $tring  .= "01"; /* priority level */
        $string .= $this->encodeInt($this->getExtra($index), 1);
        $index++;
        $string .= $this->encodeInt($this->getExtra($index), 1);
        return $string;
    }


}

?>
