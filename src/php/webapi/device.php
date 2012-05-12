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

$did    = hexdec($html->args()->id);
$action = strtolower($html->args()->action);
$dev    = &$html->system()->device();


if ($action === "post") {
    $dev->load($did);
    /* If this is a new device save get it */
    if ($dev->get("DeviceID") === "000000") {
        $dev->set("id", 0);
        $dev->store(true);
    }
    /* Save any device information given to us */
    $device = &$_POST["device"];
    if (is_array($device) && (count($device) > 0)) {
        $dev->setParam("LastModified", date("Y-m-d H:i:s"));
        $dev->change($device);
    }
    /* Save any sensor information given to us */
    $sensors = &$_POST["sensors"];
    if (is_array($sensors) && (count($sensors) > 0)) {
        $totalSensors = $dev->get("totalSensors");
        for ($i = 0; $i < $totalSensors; $i++) {
            if (is_array($sensors[$i])) {
                $dev->sensor($i)->change($sensors[$i]);
            }
        }
    }
    $ret = $dev->json();
} else if ($action === "config") {
    $dev->load($did);
    if ($dev->action()->config()) {
        $dev->store(true);
        $sensors = $dev->get("physicalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $pkt = $dev->network()->sensorConfig($i);
            if (strlen($pkt->reply()) > 0) {
                $dev->sensor($i)->decode($pkt->reply());
                $dev->sensor($i)->change(array());
            } else {
                break;
            }
        }
    }
    $ret = $dev->json();
} else if ($action === "get") {
    $dev->load($did);
    $ret = $dev->json();
} else {
    $ids = $dev->ids();
    $ret = array();
    foreach ((array)$ids as $value) {
        $ret[] = $value;
    }
    $ret = json_encode($ret);
}
if ($html->args()->d > 0) {
    var_dump(json_decode($ret, true));
}

print $ret;

?>