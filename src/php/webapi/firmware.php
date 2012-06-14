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
require_once "HUGnetLib/tables/FirmwareTable.php";

$did      = hexdec($json->args()->id);
$action = strtolower($json->args()->action);
$firmware = new FirmwareTable();

$file = sys_get_temp_dir()."/HUGnetFirmware".$did;

if ($action === "updatecheck") {
    $path  = "http://www.int.hugllc.com/HUGnet/firmware";

    $files = file($path."/manifest");
    foreach ((array)$files as $file) {
        if (!$firmware->checkFile($file)) {
            // Load the firmware
            $firmware->fromFile($file, $path);
//            var_dump($firmware->toArray());
            // Insert it.
            $firmware->insertRow(true);
        }
    }
    $array = $firmware->selectIDs("1", array());
    $ret = array();
    foreach ((array)$array as $key => $value) {
        $ret[] = $value;
    }
} else if ($action === "status") {
    if (file_exists($file)) {
        $stuff = file($file);
        $ret = trim(array_pop($stuff));
    } else {
        $ret = "idle";
    }
} else if ($action === "load") {
    $fd = fopen($file, "w");
    \HUGnet\VPrint::config(
        array(
            "session" => $fd,
            "verbose" => 1,
        )
    );
    $dev = $json->system()->device($did);
    $firmware->set("FWPartNum", $dev->get("FWPartNum"));
    $firmware->set("HWPartNum", $dev->get("HWPartNum"));
    $firmware->set("RelStatus", \FirmwareTable::DEV);
    $ret = -1;
    if ($firmware->getLatest()) {
        if ($dev->network()->loadFirmware($firmware)) {
            $part = $firmware->get("FWPartNum");
            if (substr($part, 0, 7) === "0039-20") {
                $ret = "success";
            } else if ($dev->network()->loadConfig()) {
                $ret = "success";
            }
        }
    }
    fclose($fd);
    unlink($file);
}


print json_encode($ret);

?>