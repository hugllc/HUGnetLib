<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BatStatus extends \HUGnet\devices\datachan\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array('BatStatus', 'RawStatus');
    /** @var These are the statuses */
    private $_status = array(
        0 => "Unknown",
        1 => "No Driver - Supplying Current",
        2 => "No Driver",
        3 => "Online",
        4 => "Offline",
        5 => "Empty",
        6 => "Error",
    );
    /** @var These are the statuses */
    private $_batstat = array(
        1 => "Offline",
        2 => "Float Charge",
        3 => "Absorption Charge",
        4 => "Unknown",
        5 => "Error",
        6 => "Discharging",
        7 => "Empty",
        8 => "Testing",
        9 => "Bulk Charge",
        10 => "Auto Finish Charge"
    );
    /** @var These are the statuses */
    private $_errors = array(
        1 => "Waiting for Calibration",  //ERROR_WAIT_CAL,
        2 => "MCU Failure", //CPUERROR,
        3 => "Hardware Overcurrent", //HWOVERCURRENT,
        4 => "Hardware Overpower", //HWOVERPOWER,
        5 => "Unrecoverable Overcurrent", //SWOVERCURRENT,
        6 => "Software Overpower", //SWOVERPOWER,
        7 => "Switch Bad", //SWITCHBAD,
        8 => "Current Sensor Bad", //CURRENTSENSORBAD,
        9 => "Power Port Error", //POWERPORTERROR,
        10 => "Power Flowing in the Wrong Direction", //POWER_FLOWING_WRONG_DIR,
        11 => "Multiple Port Errors", //MULTIPLE_PORT_ERRORS
        12 => "Bus Brownout", // BUSBROWNOUT
        13 => "Over Temperature", // SWOVERTEMP
    );
    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    * @param string $type  The data type to convert
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to, $from, $type)
    {
        $ret = false;
        if (($from == "RawStatus") && ($to == "BatStatus")) {

            $stat    = ($data & 0xF);
            $error   = ($data & 0x00F0) >> 4;
            $batstat = ($data & 0xF000) >> 12;

            if (isset($this->_status[$stat])) {
                $data = $this->_status[$stat];
            } else {
                $data = "Unknown [".$stat."]";
            }
            if (!empty($batstat)) {
                if (isset($this->_batstat[$batstat])) {
                    $data .= " (".$this->_batstat[$batstat].")";
                } else {
                    $data .= " (Unknown [$batstat])";
                }
            }
            if (!empty($error)) {
                if (isset($this->_errors[$error])) {
                    $data .= " - Error: ".$this->_errors[$error];
                } else {
                    $data .= " - Error: Unknown (".$error.")";
                }
            }
            $ret = true;
        } else if ($from == $to) {
            $ret = true;
        }
        return $ret;
    }
    /**
    * Checks to see if value the units represent is numeric
    *
    * @param string $units The units to check
    *
    * @return bool True if they are numeric, false otherwise
    */
    public function numeric($units)
    {
        return false;
    }
}
?>
