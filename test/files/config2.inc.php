<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our test configuration */
$config["hugnet_database"] = "MyDatabase";
$config["script_gatewaykey"] = 2;
$config["servers"][0]["driver"] = "mysql";
$config["servers"][0]["host"] = "10.2.5.23";
$config["servers"][0]["user"] = "user";
$config["servers"][0]["password"] = 'password';
$config["sockets"][0]["GatewayIP"] = "10.2.3.5";
$config["sockets"][0]["GatewayPort"] = 2001;
$config["poll_enable"] = true;
$config["config_enable"] = true;
$config["control_enable"] = false;
$config["check_enable"] = true;
$config["check_send_daily"] = true;
$config["analysis_enable"] = true;
$config["admin_email"] = "you@yourdomain.com";
?>