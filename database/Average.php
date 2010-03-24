<?php
/**
 * Class for saving analysis data
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/database/History.php";

/**
 * A class for controlling processes
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Average extends History implements HUGnetDBInterface
{
    /** The database table to use */
    protected $table = "average";
    /** This is the Field name for the key of the record */
    protected $id = "AverageKey";
    /** The number of data elements */
    private $_elements = 16;
    /** The number of columns */
    private $_columns = 3;
    /**
     * This function sets up the driver object, and the database object.  The
     * database object is taken from the driver object.
     *
     * @param mixed $config The configuration array
     *
     * @return null
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        if (empty($this->config["Type"])) {
            $this->config["Type"] = "15MIN";
        }
    }
    /**
     * Creates the database table
     *
     * @param string $table    The table to use
     * @param mixed  $elements The number of data fields
     *
     * @return null
     */
    public function createTable($table=null, $elements=null)
    {
        $elements = (int) $elements;
        if (!empty($elements)) {
            $this->_elements = $elements;
        }
        $this->_columns = 3 + $this->_elements;

        if (is_string($table) && !empty($table)) {
            $this->table = $table;
        }
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
                  `Type` varchar(16) NOT NULL default '15MIN',
                 ";
        for ($i = 0; $i < $this->_elements; $i++) {
            $query .= "`Data".$i."` float default NULL,\n";
        }
        $query .= "PRIMARY KEY  (`DeviceKey`, `Date`)\n);";
        $ret    = $this->query($query, false);
        $this->getColumns();
        return $ret;
    }
    /**
     * Gets all rows from the database
     *
     * @param string $where   Where clause
     * @param array  $data    Data for query
     * @param int    $limit   The maximum number of rows to return (0 to return all)
     * @param int    $start   The row offset to start returning records at
     * @param string $orderby The 'ORDER BY' SQL clause
     *
     * @return array
     */
    public function getWhere(
        $where,
        $data = array(),
        $limit = 0,
        $start = 0,
        $orderby = ""
    ) {
        if (strtolower($this->config["Type"]) != "all") {
            if (!empty($where)) {
                $where .= " AND";
            }
            $where .= " Type = ?";
            $data[] = $this->config["Type"];
        }
        return parent::getWhere($where, $data, $limit, $start, $orderby);
    }
}

?>
