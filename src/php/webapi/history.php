<?php
/**
 * Setup Home
 *
 * PHP Version 5
 * <pre>
 * CoreUI is a user interface for the HUGnet cores.
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Library
 * @package    HUGnetLib
 * @subpackage Webapi
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */


/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

require_once "HUGnetLib/tables/TestTable.php";

$did    = hexdec($json->args()->id);
$action = strtolower($json->args()->action);
$TestID = $json->args()->TestID;

$since  = (int)$_REQUEST["since"];
$limit  = ((int)$_REQUEST["limit"]) ? (int)$_REQUEST["limit"] : 100;

if ($TestID) {
    $table = &$json->system()->test($did)->historyFactory(array());
} else {
    $table = &$json->system()->device($did)->historyFactory(array());
}

$table->sqlLimit = $limit;
$table->sqlOrderBy = "Date asc";
$run = $table->selectInto(
    "`id` = ? AND `Date` > ?",
    array($did, $since)
);
$ret = array();
while ($run) {
    $ret[] = $table->toArray();
    $run   = $table->nextInto();
}
print json_encode($ret);
?>