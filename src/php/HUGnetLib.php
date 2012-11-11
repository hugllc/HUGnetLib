<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/**
 * This just ties everything together and allows things to be easily gotten
 * without having to know what files things are in.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage HUGnetLib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetLib
{
    /**
    * Creates an argument object
    *
    * @param array  $config The configuration to use
    * @param string $type   The type of args to get.  Automatic if left blank
    * @param array  $args   The argument array to pass to the class
    *
    * @return null
    */
    static public function &args(
        $config, $type = null, $args = null
    ) {
        if (empty($type)) {
            // Default to HTML
            $type = 'html';
            if (PHP_SAPI === 'cli') {
                $type = "cli";
            }
        }
        $type = trim(strtolower($type));
        $fct  = "_args".ucfirst($type);
        if (method_exists("HUGnetLib", $fct)) {
            $obj = self::$fct($config, $args);
        } else {
            include_once dirname(__FILE__)."/ui/Args.php";
            $count = count($args);
            $obj = \HUGnet\ui\Args::factory($args, $count, $config);
        }
        return $obj;
    }
    /**
    * Creates an argument object
    *
    * @param array  $config The configuration to use
    * @param string $type   The type of args to get.  Automatic if left blank
    * @param array  $args   The argument array to pass to the class
    *
    * @return null
    */
    static private function &_argsHtml(
        $config, $args = null
    ) {
        if (is_null($args)) {
            $args = $_REQUEST;
        }
        include_once dirname(__FILE__)."/ui/HTMLArgs.php";
        $count = count($args);
        return \HUGnet\ui\HTMLArgs::factory($args, $count, $config);
    }
    /**
    * Creates the object
    *
    * @param array  $config The configuration to use
    * @param string $type   The type of args to get.  Automatic if left blank
    *
    * @return null
    */
    static public function &ui($config, $type = "CLI")
    {
        $type = trim(strtolower($type));
        $fct  = "_ui".ucfirst($type);
        if (method_exists(self, $fct)) {
            $obj = self::$fct($config);
        } else {
            include_once dirname(__FILE__)."/ui/CLI.php";
            $obj = \HUGnet\ui\CLI::factory($config);
        }
        return $obj;
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    static private function &_uiDaemon($config)
    {
        include_once dirname(__FILE__)."/ui/Daemon.php";
        return \HUGnet\ui\Daemon::factory($config);
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    static private function &_uiHtml($config)
    {
        include_once dirname(__FILE__)."/ui/HTML.php";
        return \HUGnet\ui\HTML::factory($config);
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    static private function &_uiWebapi($config)
    {
        include_once dirname(__FILE__)."/ui/WebAPI.php";
        return \HUGnet\ui\WebAPI::factory($config);
    }
}
?>
