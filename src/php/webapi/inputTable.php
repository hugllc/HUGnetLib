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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */


/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
require_once "HUGnetLib/tables/InputTableTable.php";
require_once "HUGnetLib/sensors/ADuCInputTable.php";

$did      = (int)$json->args()->id;
$action = strtolower($json->args()->action);
$arch = substr((string)$_REQUEST["arch"], 0, 6);
$input = new InputTableTable();
$table = \HUGnet\sensors\ADuCInputTable::factory(array());

//\HUGnet\VPrint::config(array("verbose" => 10));

if ($action === "post") {
    $worked = true;
    $data = &$_REQUEST["inputTable"];
    if (is_array($data) && isset($data['id'])) {
        $input->fromArray($data);
        $worked &= $input->updateRow();
    }
    if ($worked) {
        $ret = "success";
    } else {
        $ret = -1;
    }
} else if ($action === "new") {
    $input->set("id", null);
    $input->set("name", "New Table");
    $worked = $input->insertRow();
    if ($worked) {
        $input->sqlOrderBy = "id desc";
        $input->selectOneInto("name = ?", array("New Table"));
        $ret = $input->toArray();
        $table->fromArray($ret);
        $ret["params"] = $table->fullArray();
    } else {
        $ret = -1;
    }
} else if ($action === "get") {
    $input->getRow($did);
    $ret = $input->toArray();
    $table->fromArray($ret);
    $ret["params"] = $table->fullArray();
} else if ($action == "ids") {
    $where = "";
    $whereData = array();
    if (!empty($arch)) {
        $where      .= "arch = ?";
        $whereData[] = $arch;
    }
    $ret = $input->selectIDs($where, $whereData);
} else if ($action === "getall") {
    $where = "";
    $whereData = array();
    if (!empty($arch)) {
        $where      .= "arch = ?";
        $whereData[] = $arch;
    }
    $ids = $input->selectIDs($where, $whereData);
    $ret = array();
    foreach ((array)$ids as $value) {
        $input->getRow((int)$value);
        $vals = $input->toArray();
        $table->fromArray($vals);
        $vals["params"] = $table->fullArray();
        $ret[] = $vals;
    }
}


print json_encode($ret);

?>