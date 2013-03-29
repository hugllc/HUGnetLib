<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\interfaces;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.11.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 */
interface DBTableHistory
{
    /**
    * Checks to see if our record exists in the database
    *
    * @param int $period The length of time to search in
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists($period = 0);
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $devId      The ID to use.  None if null
    * @param string $type       Not used here.  This is to be compatible with
    *                              AverageTableBase::getPeriod()
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
        $type = null,
        $extraWhere = null,
        $extraData = null
    );
}
?>
