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
abstract class DriverADuC extends Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $offset = 0;
    /**
    * This is where our channel
    */
    protected $channel = 0;
    /**
    * This is the class to use for our entry object.
    */
    protected $entryClass = "ADuCInputTable";
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    * @param int    $offset  The offset to use
    * @param object $entry   The table entry
    * @param int    $channel The channel in that entry
    *
    * @return null
    */
    public static function &factory(
        $driver, &$sensor, $offset = 0, $entry = null, $channel = 0
    ) {
        $obj = parent::factory($driver, $sensor);
        $obj->offset = (int)$offset;
        $obj->channel = (int)$channel;
        return $obj;
    }
    /**
    * Gets the extra values
    *
    * @param int $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        $extra = (array)$this->input()->get("extra");
        $return = $extra[$index + $this->offset];
        if (is_null($return)) {
            $extra = $this->get("extraDefault");
            $return = $extra[$index];
        }
        return $return;
    }
    /**
    * Changes an n-bit twos compliment number into a signed number PHP can use
    *
    * @param int   $value The incoming number
    * @param float $bits  The number of bits the incoming number is
    *
    * @return int A signed integer for PHP to use
    */
    protected function getTwosCompliment($value, $bits = 24)
    {
        if ($this->twosComplimentEnabled()) {
            /* Clear off any excess */
            $value = (int)($value & (pow(2, $bits) - 1));
            /* Calculate the top bit */
            $topBit = pow(2, ($bits - 1));
            /* Check to see if the top bit is set */
            if (($value & $topBit) == $topBit) {
                /* This is a negative number */
                $value = -(pow(2, $bits) - $value);
            }
        }
        return $value;
    }
    /**
    * Compensates for an input and bias resistance.
    *
    * The bias and input resistance values can be in Ohms, kOhms or even MOhms.  It
    * doesn't matter as long as they are both the same units.
    *
    * If $Rbias is not numeric, then it assumes it is infinte
    *
    * @param float $value The incoming number
    * @param float $Rin   The input resistor.
    * @param float $Rbias The bias resistor.
    *
    * @return float The compensated value
    */
    protected function inputBiasCompensation($value, $Rin, $Rbias)
    {
        if (!is_numeric($Rbias) || !is_numeric($Rin)) {
            return $value;
        }
        if ((float)$Rbias == 0) {
            return null;
        }
        return (float)bcdiv(bcmul($value, bcadd($Rin, $Rbias)), (float)$Rbias);
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    protected function gain($channel = null)
    {
        if (is_null($channel)) {
            $channel = $this->channel;
        }
        $channel = (int)$channel;
        if (($channel == 0) && !$this->adcOn($channel)) {
            // If channel 0 is off, get the gain from channel 1
            $channel = 1;
        }
        return (float)$this->entry()->gain($channel);
    }
    /**
    * Gets the reference multiplier
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    protected function reference($channel = null)
    {
        if (is_null($channel)) {
            $channel = $this->channel;
        }
        $channel = (int)$channel;
        if (($channel == 0) && !$this->adcOn($channel)) {
            // If channel 0 is off, get the gain from channel 1
            $channel = 1;
        }
        return (float)$this->entry()->reference($channel);
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    protected function ipRoutine($channel = null)
    {
        if (is_null($channel)) {
            $channel = $this->channel;
        }
        $channel = (int)$channel;
        return hexdec($this->entry()->immediateProcessRoutine($channel));
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    protected function twosComplimentEnabled($channel = null)
    {
        if (is_null($channel)) {
            $channel = $this->channel;
        }
        $channel = (int)$channel;
        if (($channel != 0) || !$this->adcOn(0)) {
            // If channel 0 is off, get the gain from channel 1
            return $this->entry()->twosComplimentEnabled(1);
        }
        return $this->entry()->twosComplimentEnabled(0);
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    protected function adcOn($channel)
    {
        if ($channel == 0) {
            return (bool)$this->entry()->enabled(0);
        } else if ($channel == 1) {
            return (bool)$this->entry()->enabled(1);
        }
        return false;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $A = $this->getRawData($string);
        $A = $A / $this->gain();
        $ret = $this->channels();
        $type = $this->get("storageType");
        if ($type == \HUGnet\devices\datachan\Driver::TYPE_DIFF) {
            $ret[0]["value"] = $this->getReading(
                ($A - $prev["raw"]), $deltaT, $data, $prev
            );
        } else {
            $ret[0]["value"] = $this->getReading(
                $A, $deltaT, $data, $prev
            );
        }
        $ret[0]["raw"] = $A;
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    *
    * @return float The raw value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function getRawData(&$string, $channel = 0)
    {
        if (is_string($string)) {
            $A = $this->getTwosCompliment($this->strToInt($string), 32);
        } else {
            $A = (int)$string;
        }
        return $A;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $ret = parent::channels();
        if (is_array($ret) && isset($ret[0])) {
            $ret[0]["port"] = $this->entry()->port($this->channel);
        }
        return $ret;
    }



}


?>
