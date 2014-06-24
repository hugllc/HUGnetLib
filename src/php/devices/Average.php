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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
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
    /** @var The number of averages we have done */
    protected $avgCount = 0;
    /** @var The number of averages we have done */
    protected $sqlLimit = 1000;
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
    * @param object &$data   This is the data to use to calculate the average
    *                        This is not used here, but it is required to
    *                        match the main implementation.
    * @param string $avgType The type of average to do
    *
    * @return false on failure, the average table on success
    */
    public function &get(&$data, $avgType)
    {
        $return = false;
        if (!is_object($this->hist)) {
            $this->hist = &$data;
            if (!empty($this->hist->sqlLimit)) {
                $this->sqlLimit = $this->hist->sqlLimit;
            } else {
                $this->sqlLimit = 1000;
            }
            $this->avgCount = $this->sqlLimit;
        }
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
    protected function &getAverage(&$rec, $param)
    {
        $ret = $this->avg->calcAverage($this->hist, $param["type"]);
        $this->avgCount--;
        if (($ret == false) || ($this->avgCount < 0)) {
            $this->done = true;
            $this->hist = null;
            $this->device->store();
            return false;
        }
        $rec = $this->avg->toArray(false);
        return true;
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
    private function _calcAverage(&$param)
    {
        $fct = "get".$this->avgType."Average";
        if (!method_exists($this, $fct)) {
            $fct = "getAverage";
        }
        do {
            $ret = $this->{$fct}($rec, $param);
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
