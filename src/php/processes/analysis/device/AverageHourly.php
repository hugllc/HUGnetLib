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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\analysis\device;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class AverageHourly extends \HUGnet\processes\analysis\Device
{
    /** This is the period */
    protected $period = 600;
    /** This is the object we use */
    private $_datacollector;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected function __construct(&$gui)
    {
        parent::__construct($gui);
    }
    /**
    * This function creates the system.
    *
    * @param object &$gui the user interface object
    *
    * @return null
    */
    public static function &factory(&$gui)
    {
        return parent::intFactory($gui);
    }
    /**
    * This runs the job.
    *
    * @param object &$device The device to use
    *
    * @return null
    */
    public function &execute(&$device)
    {
        if (!$this->ready($device)) {
            return true;
        }
        $this->ui()->out("HOURLY average plugin starting ", 3);
        $hist = &$device->historyFactory($data, false);
        // We don't want more than 100 records at a time;
        if (empty($this->conf["maxRecords"])) {
            $hist->sqlLimit = 100;
        } else {
            $hist->sqlLimit = $this->conf["maxRecords"];
        }
        $hist->sqlOrderBy = "Date asc";

        $avg = &$device->historyFactory($data, false);

        $last     = $device->getParam("LastAverageHOURLY");
        $lastTry  = $device->getParam("LastAverageHOURLYTry");
        $lastPrev = $device->getParam("LastAverage15MIN");
        $ret = $hist->getPeriod(
            (int)$last,
            $lastPrev,
            $device->get("id"),
            \AverageTableBase::AVERAGE_15MIN
        );

        $bad = 0;
        $local = 0;
        if ($ret) {
            // Go through the records
            while ($avg->calcAverage($hist, \AverageTableBase::AVERAGE_HOURLY)) {
                if ($avg->insertRow(true)) {
                    $now = $avg->Date;
                    $local++;
                    $lastTry = time();
                } else {
                    $bad++;
                }
            }
        }

        if ($bad > 0) {
            // State we did some uploading
            $this->ui()->out(
                $device->DeviceID." - ".
                "Failed to insert $bad HOURLY average records",
                1
            );
        }
        if ($local > 0) {
            // State we did some uploading
            $this->ui()->out(
                $device->DeviceID." - ".
                "Inserted $local HOURLY average records ".
                date("Y-m-d H:i:s", $last)." - ".date("Y-m-d H:i:s", $now),
                1
            );
        }
        if (!empty($now)) {
            $last = (int)$now;
        }
        $device->setParam("LastAverageHOURLY", $last);
        $device->setParam("LastAverageHOURLYTry", $lastTry);

        $this->ui()->out("HOURLY average plugin ending ", 3);
        return true;
    }
    /**
    * This function does the stuff in the class.
    *
    * @param object &$device The device to check
    *
    * @return bool True if ready to return, false otherwise
    */
    public function ready(&$device)
    {
        $last15Min = $device->getParam("LastAverage15MIN")
                    - $device->getParam("LastAverageHOURLY");
        // Run when enabled, and at most every 15 minutes.
        return $this->enable
            && (((time() - $device->getParam("LastAverageHOURLYTry")) > 3600)
            || ($last15Min > 3600));
    }
}


?>
