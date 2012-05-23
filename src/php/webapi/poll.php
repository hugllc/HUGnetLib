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

$TestID = $json->args()->TestID;
$did = hexdec($json->args()->id);
$ret  = array();

if ($did !== 0) {
    if ($TestID) {
        $hist = $json->system()->test($did)->action()->poll();
    } else {
        $hist = $json->system()->device($did)->action()->poll();
    }
}
if (is_object($hist)) {
    if ($TestID) {
        $filename = "/tmp/LeNR.".$did.".".date("Ymd");
        $new = !file_exists($filename);
        $fd = fopen($filename, "a");
        $fields = $json->system()->test($did)->getField();
        if ($new) {
            $sep = "";
            foreach ((array)$fields as $key => $value) {
                fwrite($fd, $sep.$value["name"]);
                $sep = ",";
            }
            fwrite($fd, "\r\n");
        }
        $sep = "";
        foreach((array)$fields as $key => $value) {
            if (is_numeric($value["field"])) {
                $data = $hist->get("Data".$key);
            } else {
                if ($value["field"] == 'Date') {
                    $data = date("Y-m-d H:i:s", $hist->get($value["field"]));
                } else {
                    $data = $hist->get($value["field"]);
                }
            }
            fwrite($fd, $sep.$data);
            $sep = ",";
        }
        fwrite($fd, "\r\n");
        fclose($fd);
        chmod($filename, 0666);
    }
    $ret = $hist->toArray(false);
} else {
    $ret = -1;
}
print json_encode($ret);

?>