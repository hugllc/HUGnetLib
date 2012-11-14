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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Average extends \HUGnet\processes\analysis\Device
{
    /** This is the period */
    protected $period = 600;
    /** This is the period */
    private $_averages = array(
        "15MIN" => array(
            "base" => null,
            "prev" => "LastHistory",
            "type" => \HUGnet\db\Average::AVERAGE_15MIN,
            "history" => true,
            "time" => "Y-m-d H:i",
            "timeout" => 300,
        ),
        "HOURLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_15MIN,
            "prev" => "LastAverage15MIN",
            "type" => \HUGnet\db\Average::AVERAGE_HOURLY,
            "history" => false,
            "time" => "Y-m-d H",
            "timeout" => 3600,
        ),
        "DAILY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_HOURLY,
            "prev" => "LastAverageHOURLY",
            "type" => \HUGnet\db\Average::AVERAGE_DAILY,
            "history" => false,
            "time" => "Y-m-d",
            "timeout" => 3600,
        ),
        "WEEKLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_DAILY,
            "prev" => "LastAverageDAILY",
            "type" => \HUGnet\db\Average::AVERAGE_WEEKLY,
            "history" => false,
            "time" => "Y-m-d",
            "timeout" => 86400,
        ),
        "MONTHLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_DAILY,
            "prev" => "LastAverageDAILY",
            "type" => \HUGnet\db\Average::AVERAGE_MONTHLY,
            "history" => false,
            "time" => "Y-m",
            "timeout" => 86400,
        ),
        "YEARLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_MONTHLY,
            "prev" => "LastAverageMONTHLY",
            "type" => \HUGnet\db\Average::AVERAGE_YEARLY,
            "history" => false,
            "time" => "Y-m",
            "timeout" => 86400,
        ),
    );
    /** This is the period */
    private $_fastAverages = array(
        "30SEC" => array(
            "base" => null,
            "prev" => "LastHistory",
            "type" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
            "history" => true,
            "time" => "Y-m-d H:i:s",
            "timeout" => 25,
        ),
        "1MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
            "prev" => "LastAverage30SEC",
            "type" => \HUGnet\db\FastAverage::AVERAGE_1MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
            "timeout" => 55,
        ),
        "5MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_1MIN,
            "prev" => "LastAverage1MIN",
            "type" => \HUGnet\db\FastAverage::AVERAGE_5MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
            "timeout" => 290,
        ),
        "15MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_5MIN,
            "prev" => "LastAverage5MIN",
            "type" => \HUGnet\db\FastAverage::AVERAGE_15MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
            "timeout" => 800,
        ),
    );
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
    * This runs the job.
    *
    * @param object &$device The device to use
    *
    * @return null
    */
    public function &execute(&$device)
    {
        $return = true;
        if (!$this->ready($device)) {
            return $return;
        }
        $avg = $device->historyFactory(array(), false);
        if (is_subclass_of($avg, "HUGnet\\db\\FastAverage")) {
            $averages = $this->_fastAverages;
        } else {
            $averages = $this->_averages;
        }
        foreach ($averages as $type => $param) {
            $this->_avg($device, $type, $param);
        }
        return $return;
    }

    /**
    * This runs the job.
    *
    * @param object &$device The device to use
    * @param string $type    The type of average to do
    * @param array  $param   The parameters to use
    *
    * @return null
    */
    public function _avg(&$device, $type, $param)
    {
        $return = true;
        $timeout = time() - $device->getParam("LastAverage".$type."Try");
        if ($timeout < $param["timeout"]) {
            return;
        }
        $this->system()->out("$type average starting ", 3);
        $hist = $device->historyFactory($data, $param["history"]);
        // We don't want more than 100 records at a time;
        if (empty($this->conf["maxRecords"])) {
            $hist->sqlLimit = 1000;
        } else {
            $hist->sqlLimit = $this->conf["maxRecords"];
        }
        $hist->sqlOrderBy = "Date asc";

        $avg = $device->historyFactory($data, false);


        $last     = $device->getParam("LastAverage".$type);
        $lastTry  = $device->getParam("LastAverage".$type."Try");
        $lastPrev = $device->getParam($param["prev"]);
        $ret = $hist->getPeriod(
            (int)$last,
            (int)$lastPrev,
            $device->get("id"),
            $param["base"]
        );
        $bad = 0;
        $local = 0;
        if ($ret) {
            // Go through the records
            while ($avg->calcAverage($hist, $param["type"])) {
                if ($avg->insertRow(true)) {
                    $now = $avg->get("Date");
                    $local++;
                    $lastTry = time();
                } else {
                    $bad++;
                }
            }
        }

        if ($bad > 0) {
            // State we did some uploading
            $this->system()->out(
                "Failed to insert $bad $type average records",
                1
            );
        }
        if ($local > 0) {
            // State we did some uploading
            $this->system()->out(
                "Inserted $local $type average records ".
                date($param["time"], $last)." - ".date($param["time"], $now),
                1
            );
        }
        if (!empty($now)) {
            $last = (int)$now;
        }
        $device->load($device->id());
        $device->setParam("LastAverage".$type, $last);
        $device->setParam("LastAverage".$type."Try", $lastTry);
        $device->store();

        $this->system()->out("$type average ending ", 3);
        return $return;
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
        // Run when enabled
        return $this->enable;
    }
}


?>
