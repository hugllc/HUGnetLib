<?php
/**
 * Main index
 *
 * PHP Version 5
 * <pre>
 * HUGnetAPI is a web interface for the HUGnet devices.
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Webapi
 * @package    HUGnetLib
 * @subpackage Webapi
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once 'HUGnetLib/hugnet.inc.php';
require_once HUGNET_INCLUDE_PATH."/ui/JSON.php";

$args = \HUGnet\ui\HTMLArgs::factory(
    $_REQUEST,
    count($_REQUEST),
    array(
        "task" => array("name" => "task", "type" => "string", "default" => ""),
        "action" => array("name" => "action", "type" => "string"),
        "id" => array("name" => "DeviceID", "type" => "string"),
        "sid" => array("name" => "SensorID", "type" => "int"),
        "clientuuid" => array("name" => "clientuuid", "type" => "string"),
        "TestID" => array("name" => "TestID", "type" => "bool", "default" => false),
    )
);
$args->addLocation("/usr/share/HUGnet/config.ini");

$json = \HUGnet\ui\JSON::factory($args);
$json->header();
$task = $json->args()->task;
if (file_exists(HUGNET_INCLUDE_PATH."/webapi/".$task.".php")) {
    include_once HUGNET_INCLUDE_PATH."/webapi/".$task.".php";
}