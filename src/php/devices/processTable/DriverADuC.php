<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\processTable;
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
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
    private $_offset = 0;
    /**
    * This is where our table entry is stored
    */
    private $_entry = null;
    /**
    * This is where our channel
    */
    private $_channel = 0;
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
        $class = '\\HUGnet\\processTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            $obj = new $class($sensor, $offset);
        }
        if (!is_object($obj)) {
            include_once dirname(__FILE__)."/drivers/EmptyProcess.php";
            $obj = new \HUGnet\devices\processTable\drivers\EmptyProcess($sensor);
        }
        $obj->_entry = $entry;
        $obj->_channel = (int)$channel;
        return $obj;
    }
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    * @param int    $offset  The offset for getExtra
    *
    * @return null
    */
    protected static function &intFactory(&$sensor, $offset = 0)
    {
        $object = parent::intFactory($sensor);
        if (is_int($offset)) {
            $object->_offset = $offset;
        }
        return $object;
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
        $extra = (array)$this->process()->get("extra");
        $return = $extra[$index + $this->_offset];
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
        /* Clear off any excess */
        $value = (int)($value & (pow(2, $bits) - 1));
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($value & $topBit) == $topBit) {
            /* This is a negative number */
            $value = -(pow(2, $bits) - $value);
        }
        return $value;
    }
    /**
    * Compensates for an input and bias resistance.
    *
    * The bias and input resistance values can be in Ohms, kOhms or even MOhms.  It
    * doesn't matter as long as they are both the same units.
    *
    * @param float $value The incoming number
    * @param float $Rin   The input resistor.
    * @param float $Rbias The bias resistor.
    *
    * @return float The compensated value
    */
    protected function inputBiasCompensation($value, $Rin, $Rbias)
    {
        if ((float)$Rbias == 0) {
            return null;
        }
        return (float)bcdiv(bcmul($value, bcadd($Rin, $Rbias)), (float)$Rbias);
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function &_entry()
    {
        if (!is_object($this->_entry)) {
            $this->_entry = \HUGnet\outputs\ADuCInputTable::factory(
                $this, array()
            );
        }
        return $this->_entry;
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
            $channel = $this->_channel;
        }
        return $this->_entry()->gain($channel);
    }


}


?>
