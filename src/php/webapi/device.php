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

$did    = hexdec($json->args()->id);
$action = strtolower($json->args()->action);
$dev    = &$json->system()->device();
$ret    = "";

$file = sys_get_temp_dir()."/HUGnetLibDevice".$did;


if ($action === "post") {
    $dev->load($did);
    $worked = true;
    $device = $_POST["device"];
    $channels = $device["channels"];
    unset($device["sensors"]);
    unset($device["channels"]);
    if ($dev->get("DeviceID") === "000000") {
        $dev->load($device);
        $dev->channels($channels)->store();
        $worked = $dev->store(true);
    } else {
        //unset($device["params"]);
        $dev->channels($channels)->store();
        $worked = $dev->change($device);
    }
    if ($worked) {
        //$dev->setParam("LastModified", time());
        $dev->store();
        $ret = "success";
    } else {
        $ret = -1;
    }
} else if ($action === "config") {
    $fd = fopen($file, "w");
    \HUGnet\VPrint::config(
        array(
            "session" => $fd,
            "verbose" => 1,
        )
    );
    $dev->load($did);
    $worked  = true;
    $sensors = $dev->get("physicalSensors");
    $steps   = $sensors + 2;
    $step    = 1;
    \HUGnet\VPrint::out("config $step/$steps success", 1);
    if ($dev->action()->config()) {
        $step++;
        \HUGnet\VPrint::out("sensor $step/$steps success", 1);
        $dev->store();
        for ($i = 0; $i < $sensors; $i++) {
            $step++;
            $pkt = $dev->network()->sensorConfig($i);
            if (strlen($pkt->reply()) > 0) {
                \HUGnet\VPrint::out("sensor $step/$steps success", 1);
                $dev->sensor($i)->decode($pkt->reply());
                $dev->sensor($i)->change(array());
            } else {
                \HUGnet\VPrint::out("sensor $step/$steps failure", 1);
                $worked = false;
                break;
            }
        }
    } else {
        \HUGnet\VPrint::out("sensor $step/$steps failure", 1);
        $worked = false;
    }
    if ($worked) {
        $dev->setParam("LastModified", time());
        $dev->store();
        $ret = $dev->fullArray();
    } else {
        $ret = -1;
    }
    fclose($fd);
    unlink($file);
} else if ($action === "get") {
    $dev->load($did);
    $ret = $dev->fullArray();
} else if ($action === "getall") {
    $ids = $dev->ids();
    $ret = array();
    foreach ((array)$ids as $value) {
        $dev->load((int)$value);
        $ret[] = $dev->fullArray();
    }
} else if ($action === "ids") {
    $ids = $dev->ids();
    $ret = array();
    foreach ((array)$ids as $value) {
        $ret[] = $value;
    }
} else if ($action === "status") {
    if (file_exists($file)) {
        $stuff = file($file);
        $ret = trim(array_pop($stuff));
    } else {
        $ret = "idle";
    }
} else if ($action === "loadfirmware") {
    $firmware = new FirmwareTable();
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
            $ret = $dev->fullArray();
        }
    }
    fclose($fd);
    unlink($file);
} else if ($action === "loadconfig") {
    $fd = fopen($file, "w");
    \HUGnet\VPrint::config(
        array(
            "session" => $fd,
            "verbose" => 1,
        )
    );
    $dev = $json->system()->device($did);
    $ret = -1;
    if ($dev->network()->loadConfig()) {
        $ret = $dev->fullArray();
    }
    fclose($fd);
    unlink($file);
}
//var_dump($ret);
print json_encode($ret);
?>