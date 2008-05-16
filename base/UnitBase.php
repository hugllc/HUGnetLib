<?php
/**
 * Main sensor driver.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Units
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
 * Base class for sensors.
 *
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class UnitBase
{
    /** prefix factors for SI units */
    private $_si = array(
        "Y"      => 24,
        "Z"      => 21,
        "E"      => 18,
        "P"      => 15,
        "T"      => 12,
        "G"      => 9,
        "M"      => 6,
        "k"      => 3,
        "h"      => 2,
        "da"     => 1,
        ""       => 0,
        "d"      => -1,
        "c"      => -2,
        "m"      => -3,
        "&#956;" => -6,
        "n"      => -9,
        "p"      => -12,
        "f"      => -15,
        "a"      => -18,
        "z"      => -21,
        "y"      => -24,
    ); 
    private $_siName = array(
        "Y"      => "yotta",
        "Z"      => "zetta",
        "E"      => "exa",
        "P"      => "peta",
        "T"      => "tera",
        "G"      => "giga",
        "M"      => "mega",
        "k"      => "kilo",
        "h"      => "hecto",
        "da"     => "deca",
        ""       => "",
        "d"      => "deci",
        "c"      => "centi",
        "m"      => "milli",
        "&#956;" => "micro",
        "n"      => "nano",
        "p"      => "pico",
        "f"      => "femto",
        "a"      => "atto",
        "z"      => "zepto",
        "y"      => "yocto",
    ); 
    /** Unit data will be stored here */
    public $units = array();

    /**
     * Constructor.
     *
     * This just sets up the variables if they are passed to it.
     *
     */
    function __construct()
    {
        $this->siSetup();
    }

    /**
     * sets up SI units
     *
     * @return none
     */
    public function siSetup()
    {
        if (!is_array($this->units)) return;
        foreach ($this->units as $unit => $value) {
            if (!is_array($value["siPrefix"])) continue;
            foreach ($value["siPrefix"] as $prefix) {
                $prefix = trim($prefix);
                $this->units[$unit]["convert"][$prefix.$unit] = "shift:".$this->siGetShift("", $prefix);
                $this->units[$prefix.$unit] = array(
                    "longName" => $this->siGetName($prefix).$value["longName"],
                    "varType" => $value["varType"],
                    "convert" => array($unit => "shift:".$this->siGetShift($prefix, "")),
                );
                foreach ($value["siPrefix"] as $p) {
                    if ($p == $prefix) continue;
                    $this->units[$prefix.$unit]["convert"][$p.$unit] = "shift:".$this->siGetShift($prefix, $p);
                }
            }
        }
    }

    /**
     * This function returns eactly what it is given.
     *
     * @param float  $val   The input value
     * @param int    $time  The time in seconds between this record and the last.
     * @param string $type  The type of data (diff, raw, etc)
     * @param mixed  $extra The extra information from the sensor.
     *
     * @return float
     *
     */
    public function unity ($val, $time, $type, $extra) 
    {
        return $val;
    }
    
    /**
     * This shifts the decimal places in SI units
     *
     * @param string $from The prefix to shift from
     * @param string $to   The prefix to shift to
     *
     * @return float
     */
    public function siGetShift ($from, $to) 
    {
        return (int)($this->siGetExp($to) - $this->siGetExp($from));
    }
    /**
     * This shifts the decimal places in SI units
     *
     * @param float  $val  The input value
     * @param string $from The prefix to shift from
     * @param string $to   The prefix to shift to
     *
     * @return float
     */
    public function shift ($val, $shift) 
    {
        return (float)($val * pow(10, $shift));
    }

    /**
     * This shifts the decimal places in SI units
     *
     * @param float  $val  The input value
     * @param string $from The prefix to shift from
     * @param string $to   The prefix to shift to
     *
     * @return float
     */
    public function siShift ($val, $from, $to) 
    {
        $shift = (int)($this->siGetExp($to) - $this->siGetExp($from));
        return $this->shift($val, $shift);
    }

    /**
     * get the si exponent
     *
     * @param string $prefix The prefix to use
     *
     * @return int
     */
    public function siGetExp($prefix)
    {
        if (isset($this->_si[$prefix])) return $this->_si[$prefix];
        return 0;
    }
    /**
     * get the si name
     *
     * @param string $prefix The prefix to use
     *
     * @return int
     */
    public function siGetName($prefix)
    {
        if (isset($this->_siName[$prefix])) return $this->_siName[$prefix];
        return "";
    }
    
}

?>
