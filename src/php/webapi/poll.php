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
/** This keeps this file from being included unless HUGnetSystem.php is included */
require_once HUGNET_INCLUDE_PATH."/containers/DeviceContainer.php";

$devs = explode(",", $json->args()->id);
$ret  = array();

$savedate = date("Y-m-d H:i:s");
$filename = "/tmp/LeNR".date("Ymd");
$new = false;
if (!file_exists($filename)) $new = true;
$fileheader = "Date";
$filedata = $savedate;
foreach ($devs as $dev) {
    $did = hexdec($dev);

    $device = &$json->system()->device($did);
    $pkt = $device->network()->poll();
    if (strlen($pkt->reply()) > 0) {
        $device->setParam("LastPoll", date("Y-m-d H:i:s"));
        $device->setParam("LastContact", date("Y-m-d H:i:s"));
        $device->store();

        //$dev = new DeviceContainer($device->get("RawSetup"));
        $data = $device->decodeData(
            $pkt->Reply(),
            $pkt->Command(),
            0,
            (array)$prev[$dev]
        );
        $device->setUnits($data);
        $data["id"] = $did;
        $data["Date"] = $savedate;
        $d = $device->historyFactory($data);
        $d->insertRow(true);
        $out = $d->toArray();
        $ret["Date"]      = $savedate;
        $ret["DataIndex"] = $data["DataIndex"];

        for ($i = 0; $i < 9; $i++) {
            $ret["Data"][$did.".".$i] = $out["Data".$i];
            $loc = $device->sensor($i)->get("location");
            if (strlen($loc) == 0) {
                $fileheader .= ",Device $dev Sensor $i";
            } else {
                $fileheader .= ",".$loc;
            }
            $filedata   .= ",".$out["Data".$i];
        }
    }
}
$fd = fopen($filename, "a");
if ($new) {
        fwrite($fd, $fileheader."\r\n");
}
fwrite($fd, $filedata."\r\n");
fclose($fd);
chmod($filename, 0666);

print json_encode($ret);

?>