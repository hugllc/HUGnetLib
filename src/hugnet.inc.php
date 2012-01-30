<?php
/**
 * Main HUGnet include.  Include this file and you should get everything that
 * you need.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** The base path to all the files included for HUGnet */
if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__));
}
/** The directory Separator */
if (!defined("DS")) {
    /** The name of the default HUGnet Database */
    define("DS", "/");
}

/** The version define for all of HUGnetLib */
define(
    "HUGNET_LIB_VERSION",
    trim(file_get_contents(dirname(__FILE__)."/VERSION.TXT"))
);
/** This is for backward compatibility with some older stuff */
define("HUGNET_BACKEND_VERSION", HUGNET_LIB_VERSION);

if (!defined("HUGNET_DATABASE")) {
    /** The name of the default HUGnet Database */
    define("HUGNET_DATABASE", "HUGnet");
}

$temp_dir = sys_get_temp_dir();

?>
