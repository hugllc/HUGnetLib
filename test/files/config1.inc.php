<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is our test configuration */
$hugnet_config["hugnet_database"] = "MyDatabase";
$hugnet_config["script_gatewaykey"] = 2;
$hugnet_config["servers"][0]["driver"] = "mysql";
$hugnet_config["servers"][0]["host"] = "10.2.5.23";
$hugnet_config["servers"][0]["user"] = "user";
$hugnet_config["servers"][0]["password"] = 'password';
$hugnet_config["sockets"][0]["GatewayIP"] = "10.2.3.5";
$hugnet_config["sockets"][0]["GatewayPort"] = 2001;
$hugnet_config["poll_enable"] = true;
$hugnet_config["config_enable"] = true;
$hugnet_config["control_enable"] = false;
$hugnet_config["check_enable"] = true;
$hugnet_config["check_send_daily"] = true;
$hugnet_config["analysis_enable"] = true;
$hugnet_config["admin_email"] = "you@yourdomain.com";
?>