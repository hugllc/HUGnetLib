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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Average 
{
    /** @var This is the system object */
    protected $system = null;
    /** @var This is the device object  */
    protected $device = null;
    /** @var The type of average we are doing */
    protected $avgType = \HUGnet\db\Average::AVERAGE_15MIN;
    /** @var The average object */
    protected $avg = null;
    /** @var The data object */
    protected $hist = null;
    /** @var string The start time for the average */
    protected $startTime = null;
    /** @var string The end time for the average */
    protected $endTime = null;
    /** This is the period */
    private $_averages = array(
        "15MIN" => array(
            "base" => null,
            "prev" => "LastHistory",
            "type" => \HUGnet\db\Average::AVERAGE_15MIN,
            "history" => true,
            "time" => "Y-m-d H:i",
        ),
        "HOURLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_15MIN,
            "prev" => "LastAverage15MIN",
            "type" => \HUGnet\db\Average::AVERAGE_HOURLY,
            "history" => false,
            "time" => "Y-m-d H",
        ),
        "DAILY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_HOURLY,
            "prev" => "LastAverageHOURLY",
            "type" => \HUGnet\db\Average::AVERAGE_DAILY,
            "history" => false,
            "time" => "Y-m-d",
        ),
        "WEEKLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_DAILY,
            "prev" => "LastAverageDAILY",
            "type" => \HUGnet\db\Average::AVERAGE_WEEKLY,
            "history" => false,
            "time" => "Y-m-d",
        ),
        "MONTHLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_DAILY,
            "prev" => "LastAverageDAILY",
            "type" => \HUGnet\db\Average::AVERAGE_MONTHLY,
            "history" => false,
            "time" => "Y-m",
        ),
        "YEARLY" => array(
            "base" => \HUGnet\db\Average::AVERAGE_MONTHLY,
            "prev" => "LastAverageMONTHLY",
            "type" => \HUGnet\db\Average::AVERAGE_YEARLY,
            "history" => false,
            "time" => "Y-m",
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
        ),
        "1MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
            "prev" => "LastAverage30SEC",
            "type" => \HUGnet\db\FastAverage::AVERAGE_1MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
        ),
        "5MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_1MIN,
            "prev" => "LastAverage1MIN",
            "type" => \HUGnet\db\FastAverage::AVERAGE_5MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
        ),
        "15MIN" => array(
            "base" => \HUGnet\db\FastAverage::AVERAGE_5MIN,
            "prev" => "LastAverage5MIN",
            "type" => \HUGnet\db\FastAverage::AVERAGE_15MIN,
            "history" => false,
            "time" => "Y-m-d H:i",
        ),
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The network application object
    * @param object &$device The device device object
    *
    * @return null
    */
    protected function __construct(&$system, &$device)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $this->system = &$system;
        $this->device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->system);
        unset($this->driver);
        unset($this->device);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$device  (object)The device to use
    *
    * @return null
    */
    public static function &factory(&$network, &$device)
    {
        $object = new Average($network, $device);
        return $object;
    }
    /**
    * This runs the job.
    *
    *
    * @param HistoryTableBase &$data   This is the data to use to calculate the average
    *                                  This is not used here, but it is required to
    *                                  match the main implementation.
    * @param string           $avgType The type of average to do
    *
    * @return null
    */
    public function &get($avgType, $start = null, $end = null)
    {
        $return = false;
        $this->start = $start;
        $this->end = (is_null($end)) ? time() : $end;
        $this->avg = $this->device->historyFactory(array(), false);
        if (is_subclass_of($this->avg, "HUGnet\\db\\FastAverage")) {
            $averages = &$this->_fastAverages;
        } else {
            $averages = &$this->_averages;
        }
        if (is_string($avgType) && (is_array($averages[$this->avgType]))) {
            $this->avgType = $avgType;
        } else {
            $this->avgType = $this->avg->baseType();
        }
        $ret = $this->_calcAverage($averages[$this->avgType]);
        if ($ret) {
            return $this->avg;
        }
        return false;
    }

    /**
    * This runs the job.
    *
    * @param array &$rec  The record to modify
    * @param array $param The parameters to use
    *
    * @return null
    */
    protected function getAverage(&$rec, $param)
    {
        if (empty($date) || ($date > $this->end)) {
            $this->done = true;
            $this->hist = null;
            $this->device->store();
            return false;
        }
        $ret = $this->_getTimePeriod($this->hist->get("Date"), $type);
        if (!$ret) {
            return false;
        }
        $rec = array(
            "id" => $this->device->get("id"), 
            "Date" => $this->endTime,
            "Type" => $this->avgType
        );
        $this->divisors = array();
        $ret = true;
        while (($rec["Date"] < $this->endTime) && $ret) {
            if ($rec["Type"] == $param["base"]) {
                for ($i = 0; $i < $this->avg->datacols; $i++) {
                    $col = "Data".$i;
                    $value = $data->get($col);
                    if (!is_null($value)) {
                        $mine = $this->get($col);
                        $mine += $value;
                        $this->set($col, $mine);
                        $this->divisors[$col]++;
                    }
                }
            }
            $ret = $data->nextInto();
        }
        $this->settleDivisors();
        if ($data->get("Date") >= $this->endTime) {
            // We passed our time, so this is a complete record
            return true;
        }
        // Not enough records to make this complete
        $this->clearData();
        return false;
/*
        $return = false;
        $rec = array(
            "id" => $this->device->get("id"), 
            "Date" => $date,
            "Type" => $this->avgType
        );
        $notEmpty = false;
        for ($i = 0; $i < $this->device->get("InputTables"); $i++) {
            $input = $this->device->input($i);
            $table = $this->_getPoint($input);
            if (is_object($table)) {
                $val = $input->channels();
                if ($table->get("Date") == $date) {
                    $extra = $input->get("extra");
                    $field = "Data".(int)$extra[1];
                    $val[0]["value"] = $table->get($field);
                }
            } else {
                $A = null;
                $val = $input->decodeData($A, 900, $prev, $rec);
            }
            if (is_array($val) && is_array($val[0])) {
                $rec[$i] = $val[0];
                if (!is_null($val[0]["value"])) {
                    $notEmpty = true;
                }
            }
        }
        $this->_next($date);
        if ($notEmpty) {
            $this->device->setParam("LastHistory", $rec["Date"]);
            $this->device->setLocalParam("LastHistory", $rec["Date"]);
        }
        return $notEmpty;
*/
/*
        $return = true;
        $timeout = time() - $this->device->getLocalParam("LastAverage".$type."Try");
        $old     = time() - $this->device->getLocalParam("LastAverage".$type);
        if ($timeout < $param["timeout"]) {
            return;
        }
        $this->system()->out("$type average starting ", 3);
        $hist = $this->device->historyFactory($data, $param["history"]);
        
        $hist->sqlLimit   = 1000;
        $hist->sqlOrderBy = "Date asc";

        $avg = $this->device->historyFactory($data, false);


        $last     = (int)$this->device->getLocalParam("LastAverage".$type);
        $lastTry  = (int)$this->device->getLocalParam("LastAverage".$type."Try");
        $lastPrev = $this->device->getLocalParam($param["prev"]);
        if ($last == $lastPrev) {
            // No date range.  We don't need to be here
            return;
        }
        $ret = $hist->getPeriod(
            (int)$last,
            (int)$lastPrev,
            $this->device->get("id"),
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
        $this->device->load($this->device->id());
        $this->device->setLocalParam("LastAverage".$type, $last);
        $this->device->setLocalParam("LastAverage".$type."Try", $lastTry);
        $this->device->store();

        $this->system()->out("$type average ending ", 3);
        return $return;
        */
    }
    /**
    * This settles the averages
    *
    * @return none
    */
    protected function settleDivisors()
    {
        // Settle  out the multipliers
        if (!is_array($this->_channels)) {
            $this->_channels = $this->device->dataChannels()->toArray();
        }
        for ($i = 0; $i < $this->datacols; $i++) {
            $col = "Data".$i;
            if ($this->divisors[$col] == 0) {
                $this->divisors[$col] = 1;
            }
            $value = $this->get($col);
            if (!is_null($value)) {
                if (!$this->_channels[$i]["total"]) {
                    $value = $value / $this->divisors[$col];
                }

                $this->set(
                    $col,
                    round(
                        $value,
                        $this->_channels[$i]["maxDecimals"]
                    )
                );
            }
        }
    }

    /**
    * This sets the time correctly
    *
    * @param int    $time The time we are currently at
    * @param string $type The type of average to calculate
    *
    * @return bool True on success, false on failure
    */
    private function _getTimePeriod($time, $type)
    {
        $Hour = gmdate("H", $time);
        $min = gmdate("i", $time);
        $mon = gmdate("m", $time);
        $day = gmdate("d", $time);
        $Year = gmdate("Y", $time);
        if ($type == self::AVERAGE_30SEC) {
            $sec = gmdate("s", $time);
            if ($sec >= 30) {
                $sec = 30;
            } else {
                $sec = 0;
            }
            $this->startTime = gmmktime($Hour, $min, $sec, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min, $sec + 30, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_1MIN) {
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 1, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_5MIN) {
            for ($base = 55; $base >= 0; $base -= 5) {
                if ($min >= $base) {
                    $min = $base;
                    break;
                }
            }
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 5, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_15MIN) {
            for ($base = 45; $base >= 0; $base -= 15) {
                if ($min >= $base) {
                    $min = $base;
                    break;
                }
            }
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 15, 0, $mon, $day, $Year);
            return true;
        }
        return false;
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
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param array $param The parameters to use
    *
    * @return bool array on success, false on failure
    */
    private function _calcAverage($params)
    {
        $this->sqlLimit = $data->sqlLimit;
        $fct = "get".$this->avgType."Average";
        if (!method_exists($this, $fct)) {
            $fct = "_getAverage";
        }
        do {
            $ret = $this->{$fct}($rec, $params);
        } while (($ret === false) && !$this->done);
        if ($ret) {
            $this->avg->clearData();
            $this->avg->fromAny($rec);
            return true;
        }
        return false;
    }
}


?>
