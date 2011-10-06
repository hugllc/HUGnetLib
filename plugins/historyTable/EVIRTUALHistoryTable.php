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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsHistoryTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EVIRTUALHistoryTable extends HistoryTableBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "EVIRTUALHistoryTable",
        "Type" => "historyTable",
        "Class" => "EVIRTUALHistoryTable",
        "Flags" => array("eVIRTUAL"),
    );
    /** @var string This is the table we should use */
    public $sqlTable = "eVIRTUAL_average";
    /** @var This is the dataset */
    public $datacols = 20;

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $devId      The ID to use.  None if null
    * @param string $idField    The ID Field to use.  Table Primary id if left blank
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getPeriod(
        $start,
        $end = null,
        $devId = null,
        $idField = null,
        $extraWhere = null,
        $extraData = null
    ) {
        return true;
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
    {
        return false;
    }
}
?>
