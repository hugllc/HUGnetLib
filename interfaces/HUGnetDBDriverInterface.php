<?php
/**
 * Classes for dealing with devices
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
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface HUGnetDBDriverInterface
{
    /**
    * Creates the field array
    *
    * @return null
    */
    protected function columns();
    /**
    * Creates the database table.
    *
    * Must be defined in child classes
    *
    * @return bool
    */
    public function createTable();
    /**
    * Gets an attribute from the PDO object
    *
    * @param string $attrib The attribute to get.
    *
    * @return mixed
    */
    public function getAttribute($attrib);

    /**
    * Returns an array made for the execute query
    *
    * @param array $data The data to prepare
    * @param array $keys The keys to use
    *
    * @return array
    */
    protected function prepareData($data, $keys);

    /**
    * Creates an add query
    *
    * @param bool $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return string
    */
    protected function insert($replace = false);

    /**
    * Adds an row to the database
    *
    * @param array $info The row in array form
    *
    * @return bool Always False
    */
    public function replace($info);

    /**
    * Updates a row in the database.
    *
    * @return mixed
    */
    public function update();

    /**
    * Gets all rows from the database
    *
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return null
    */
    public function selectWhere($where, $data);

    /**
    * Gets all rows from the database
    *
    * @param string $where Where clause
    *
    * @return array
    */
    protected function where($where);

    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function table();

    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function orderby();
    /**
    * Return the ORDER BY clause
    *
    * @param bool $start Whether to include the 'start' portion
    *
    * @return string
    */
    protected function limit($start = true);

    /**
    * Removes a row from the database.
    *
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function deleteWhere($where, $data);
    /**
    * Removes a row from the database.
    *
    * @return mixed
    */
    public function delete();

}


?>
