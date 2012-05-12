<?php
/**
 * Main index
 *
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
 * @category   API
 * @package    HUGnetAPI
 * @subpackage HTML
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

define("_HUGNETAPI", true);
require_once 'HUGnetLib/hugnet.inc.php';
require_once HUGNET_INCLUDE_PATH."/ui/JSON.php";

$args = \HUGnet\ui\HTMLArgs::factory(
    $_REQUEST,
    count($_REQUEST),
    array(
        "task" => array("name" => "task", "type" => "string", "default" => ""),
        "action" => array("name" => "action", "type" => "string"),
        "id" => array("name" => "DeviceID", "type" => "string"),
        "uuid" => array("name" => "uuid", "type" => "string"),
    )
);
$args->addLocation("/usr/share/HUGnet/config.ini");

$json = \HUGnet\ui\JSON::factory($args);

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 4 Apr 1998 05:00:00 GMT');
header('Content-type: application/json');

$task = $json->args()->task;
include_once "HUGnetAPI/".$task.".php";
