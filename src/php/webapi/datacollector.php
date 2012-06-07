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

$uuid    = $json->args()->uuid;
$action  = strtolower($json->args()->action);
$dataCol = &$json->system()->dataCollector();
$ret     = array();



if ($action === "checkin") {
    $data = (array)$_POST["datacollector"];
    if (strlen($data["uuid"]) === 36) {
        $dataCol->load(array("uuid", $data["uuid"]));
        if ($dataCol->get("GatewayKey") == 0) {
            $dataCol->load($data);
            $dataCol->store(true);
        }
        $data["LastContact"] = time();
        if ($dataCol->change($data)) {
            $ret = "success";
        } else {
            $ret = 1;
        }
    } else {
        $ret = -1;
    }
} else if ($action === "get") {
    $dataCol->load(array("uuid" => $uuid));
    $ret = $dataCol->json();
}

print json_encode($ret);

?>