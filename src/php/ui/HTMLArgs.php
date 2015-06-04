<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is our base class */
require_once dirname(__FILE__)."/Args.php";
/**
 * This code interprets command line arguments
 *
 * The configuration array should consist of an array with each key being a single
 * letter that is the command line argument.  The value attached to each key
 * should be an array with the following:
 *    "name" - optional - string The name in the config array output
 *    "type" - optional - The return type for the argument
 *    "args" - optional - Is there extra stuff that this argument is requiring?
 *
 * The arguments can be gotten with $obj->x where 'x' is the command line argument.
 * So say your argument was -v you would get it by $obj->v.  For maximum accuracy
 * the arguments should be set up in the config array.  If any of your arguments
 * take arguments then they MUST be defined in the config array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HTMLArgs extends Args
{
    /** These are pretty standard config changes */
    protected $defaultConfig = array(
        "q" => array("name" => "quiet", "type" => "bool", "default" => false),
        "v" => array("name" => "verbose", "type" => "int", "default" => 0),
        "d" => array("name" => "debug", "type" => "bool", "default" => false),
        "t" => array("name" => "test", "type" => "bool", "default" => false),
    );
    /**
    * Creates the object
    *
    * @param array $args   The argument array
    * @param int   $count  The argument count
    * @param array $config The configuration of command line args
    * @param array $sysargs The system arguments
    *
    * @return Args object
    */
    static public function &factory($args, $count, $config = array(), $sysargs = array())
    {
        $obj = new HTMLArgs((array)$args, (int)$count, (array)$config, (array)$sysargs);
        return $obj;
    }
    /**
    * Pulls the arguments apart and stores them
    *
    * @return null
    */
    protected function interpret()
    {
//        $this->_name = trim($this->argv[0]);
        foreach ((array)$this->argv as $name => $value) {
            if (isset($this->config[$name])) {
                $this->arguments[$name] = $value;
                continue;
            }
            foreach ($this->config as $arg => $stuff) {
                if ($stuff["name"] === $name) {
                    $this->arguments[$arg] = $value;
                    break;
                }
            }
        }
        // Do the RESTful interface last, so it takes precident
        if (!empty($this->sysargs)) {
            $this->RESTful();
        }
    }
    /**
    * Pulls the arguments apart and stores them
    *
    * @return null
    */
    protected function RESTful()
    {
        // This gets everything in the URL beyond the script
        $temp = explode($this->sysargs['SCRIPT_NAME'], $this->sysargs['REQUEST_URI']);
        // This takes the arguments off the URL
        $temp = explode("?", $temp[1]);
        // This is just the URL after the script.
        $url  = ltrim($temp[0], "/");
        // Remove the first
        if (empty($url)) {
            $args = array();
        } else {
            $args = explode("/", $url);
        }
        $task = array_shift($args);
        if (!empty($task)) {
            $this->arguments["task"] = $task;
            switch ($this->sysargs["REQUEST_METHOD"]) {
                default:
                case "POST":
                    $action = "put";
                    break;
                case "PUT":
                    // Nothing
                    break;
                case "PATCH":
                    // Update
                    break;
                case "DELETE":
                    $action = "remove";
                    break;
                case "GET":
                    $action = "get";
                    break;
            }
            $id = array_shift($args);
            if (empty($id)) {
                $this->arguments["action"] = "list";
            } else {
                $this->arguments["id"] = $id;
                $this->arguments["action"] = $action;
            }
        }
        $this->arguments["restextra"] = $args;
    }
}
?>
