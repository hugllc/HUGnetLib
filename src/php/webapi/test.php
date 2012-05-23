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

$tid    = hexdec($json->args()->id);
$action = strtolower($json->args()->action);
$test   = &$json->system()->test();
$ret    = "";
$since  = (int)$_REQUEST["since"];
$limit  = ((int)$_REQUEST["limit"]) ? (int)$_REQUEST["limit"] : 100;

if ($action === "post") {
    $test->load($tid);
    $post = $_POST["test"];
    $post["modified"] = time();
    $worked = $test->change($post);
    //$worked = $test->updateRow();
    if ($worked) {
        $ret = "success";
    } else {
        $ret = -1;
    }
} else if ($action === "new") {
    if ($test->create()) {
        $ret = $test->toArray();
    } else {
        $ret = -1;
    }
} else if ($action === "get") {
    $test->load($tid);
    $ret = $test->toArray(true);
} else if ($action === "getall") {
    $ids = $test->ids();
    $ret = array();
    foreach ((array)$ids as $value) {
        $test->load((int)$value);
        $ret[] = $test->toArray(false);
    }
}
print json_encode($ret);
?>