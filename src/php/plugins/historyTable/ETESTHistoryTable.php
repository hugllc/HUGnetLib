<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsHistoryTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../tables/HistoryTableBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsHistoryTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ETESTHistoryTable extends HistoryTableBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "ETESTHistoryTable",
        "Type" => "historyTable",
        "Class" => "ETESTHistoryTable",
        "Flags" => array("eTEST"),
    );
    /** @var string This is the table we should use */
    public $sqlTable = "eTEST_history";
    /** @var This is the dataset */
    public $datacols = 20;

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array &$array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromDataArray(&$array)
    {
        $this->set("id", $array["id"]);
        $this->set("TestID", $array["id"]);
        $this->set("Date", $array["Date"]);
        foreach ((array)$array[0] as $key => $field) {
            if ($key > $this->datacols) {
                break;
            }
            $dev = sprintf("%06X", hexdec($field["device"]));
            if (is_object($array[$dev])) {
                $val = $array[$dev]->get("Data".(int)$field["field"]);
                $this->set("Data$key", $val);
            }
        }
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
