<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/PeriodContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";

/**
 * This class keeps track of hooks that can be defined and used other places in the
 * code to cause custom functions to happen.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HistoryPeriodContainer extends PeriodContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",     // The database group we are in
        "start" => 0,             // The start date in unix timestamp format
        "end" => 0,               // The end date in unix timestamp format
        "class"     => "historyTable",        // The class each record is
        "dateField" => "Date",    // The name of the date field in the records
        "records" => array(),     // The records we have
    );
    /** @var array This is where the data is stored */
    protected $auto = false;
    /** @var array This is where the data is stored */
    public $device = false;
    /**
    * Builds the class
    *
    * @param array  $data    The data to build the class with
    * @param object &$device The device this history is from
    *
    * @return null
    */
    public function __construct($data = array(), DeviceContainer &$device)
    {
        $this->device = &$device;
        parent::__construct($data);
    }
    /**
    * Sets the extra attributes field
    *
    * @param int $start The start of the time
    * @param int $end   The end of the time
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod($start = null, $end = null)
    {
        $start   = (is_null($start)) ? $this->start : $start;
        $end     = (is_null($end)) ? $this->end : $end;
        $class   = $this->class;
        $myClass = new $class(array("group" => $this->group));
        $records = $myClass->select(
            "id = ? AND "
            ."`".$this->dateField."` >= ? AND `".$this->dateField."` <= ?",
            array($this->device->id, $start, $end)
        );
        foreach (array_keys((array)$records) as $k) {
            $this->insertRecord($records[$k]);
        }
    }

}
?>
