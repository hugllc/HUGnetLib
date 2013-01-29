<?php
/**
 * Main index
 *
 * PHP Version 5
 * <pre>
 * HUGnetAPI is a web interface for the HUGnet devices.
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once "HUGnetLib/ui/WebAPI.php";
require_once "HUGnetLib/ui/WebAPIArgs.php";

$args = \HUGnet\ui\WebAPIArgs::factory(
    $_REQUEST,
    count($_REQUEST)
);
$args->addLocation("/usr/share/HUGnet/config.ini");

$api = \HUGnet\ui\WebAPI::factory($args);
$api->execute((array)$_REQUEST);

