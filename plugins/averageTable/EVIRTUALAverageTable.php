<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../tables/AverageTableBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EVIRTUALAverageTable extends AverageTableBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "EVIRTUALAverageTable",
        "Type" => "averageTable",
        "Class" => "EVIRTUALAverageTable",
        "Flags" => array("eVIRTUAL"),
    );
    /** @var string This is the table we should use */
    public $sqlTable = "eVIRTUAL_average";
    /** @var This is the dataset */
    public $datacols = 20;
    /** @var This how many times we have run */
    public $runs = 0;
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    *
    * @return bool True on success, false on failure
    */
    protected function calc15MinAverage(HistoryTableBase &$data)
    {
        do {
            // This gets us to our next average
            if (empty($data->Date)) {
                $data->Date = $this->getAverageDate("First");
            } else {
                $data->Date += 900;
            }
            if ((($this->runs++ > $data->sqlLimit) && !empty($data->sqlLimit))
                || ($data->Date > $this->getAverageDate("Last"))
                || empty($data->Date)
            ) {
                return false;
            }
            $ret = array("id" => $this->device->id, "Date" => $data->Date);
            $this->Type = self::AVERAGE_15MIN;
            $this->Date = $data->Date;
            $empty = true;
            for ($i = 0; $i < $this->device->sensors->Sensors; $i++) {
                $sensor = &$this->device->sensors->sensor($i);
                $ret[$i] = $sensor->get15MINAverage($data->Date, $ret);
                if (!is_null($ret[$i]["value"])) {
                    $empty = false;
                }
            }
        } while ($empty);
        $this->fromDataArray($ret);
        return true;
    }
    /**
    * This returns the first average from this device
    * 
    * @param string $type Either "First" or "Last"
    *
    * @return null
    */
    protected function getAverageDate($type = "First")
    {
        $ret = null;
        for ($i = 0; $i < $this->device->sensors->Sensors; $i++) {
            $sensor = &$this->device->sensors->sensor($i);
            $fct = "get".$type."Average15Min";
            if (method_exists($sensor, $fct)) {
                $avg = $sensor->$fct();
                if (is_null($ret) || ($avg < $ret)) {
                    $ret = $avg;
                }
            }
        }
        return $ret;
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
