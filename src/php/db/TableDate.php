<?php
/**
 * Abstract class for building SQL queries
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/Table.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class TableDate extends Table
{
    /** @var This is the date field for this record */
    public $dateField = null;


    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $rid        The ID to use.  None if null
    * @param string $idField    The ID Field to use.  Table Primary id if left blank
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    */
    protected function getTimePeriod(
        $start,
        $end = null,
        $rid = null,
        $idField = null,
        $extraWhere = null,
        $extraData = null
    ) {
        // If date field doesn't exist return
        if (empty($this->dateField)) {
            return false;
        }
        if (is_null($idField)) {
            $idField = $this->sqlId;
        }
        // Make sure the start and end dates are in the correct form
        if (empty($end)) {
            $end = $start;
        }
        $end = self::unixDate($end);
        // Set up the where and data fields
        $where = "`".$this->dateField."` >= ? AND `".$this->dateField."` <= ?";
        $data = array($start, $end);
        if (!is_null($rid)) {
            $where .= " AND `".$idField."` = ?";
            $data[] = $rid;
        }
        if (!empty($extraWhere)) {
            $where .= " AND ".$extraWhere;
        }
        if (is_array($extraData)) {
            $data = array_merge($data, $extraData);
        }
        return $this->selectInto(
            $where,
            $data
        );
    }


}


?>
