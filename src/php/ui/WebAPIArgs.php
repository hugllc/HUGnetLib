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
require_once dirname(__FILE__)."/HTMLArgs.php";
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
class WebAPIArgs extends HTMLArgs
{
    /** These are pretty standard config changes */
    protected $defaultConfig = array(
        "q" => array("name" => "quiet", "type" => "bool", "default" => false),
        "v" => array("name" => "verbose", "type" => "int", "default" => 0),
        "d" => array("name" => "debug", "type" => "bool", "default" => false),
        "t" => array("name" => "test", "type" => "bool", "default" => false),
        "task" => array("name" => "task", "type" => "string", "default" => null),
        "subtask" => array("name" => "subtask", "type" => "string", "default" => null),
        "action" => array("name" => "action", "type" => "string", "default" => null),
        "format" => array("name" => "action", "type" => "string", 'default' => null),
        "id" => array("name" => "id", "type" => "string", 'default' => null),
        "sid" => array("name" => "sid", "type" => "string", 'default' => null),
        "object" => array("name" => "object", "type" => "string", 'default' => null),
        "subobject" => array("name" => "subobject", "type" => "string", 'default' => null),
        "method" => array("name" => "method", "type" => "string", 'default' => 'GET'),
        "cuuid" => array("name" => "cuuid", "type" => "string"),
        "data" => array("name" => "data", "type" => "array", "default" => array()),
        "restextra" => array("name" => "restextra", "type" => "array", "default" => array()),
        "since" => array("name" => "since", "type" => "string", 'default' => null),
        "until" => array("name" => "until", "type" => "string", 'default' => null),
        "limit" => array("name" => "limit", "type" => "string", 'default' => null),
        "start" => array("name" => "start", "type" => "string", 'default' => null),
        "order" => array("name" => "order", "type" => "string", 'default' => null),

    );
    /**
    * Creates the object
    *
    * @param array $args    The argument array
    * @param int   $count   The argument count
    * @param array $config  The configuration of command line args
    * @param array $sysargs The system arguments
    *
    * @return Args object
    */
    static public function &factory($args, $count, $config = array(), $sysargs = array())
    {
        $obj = new WebAPIArgs((array)$args, (int)$count, (array)$config, (array)$sysargs);
        return $obj;
    }
    /**
    * Pulls the arguments apart and stores them
    *
    * @return null
    */
    protected function interpret()
    {
        parent::interpret();
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
        $obj = array_shift($args);
        if (!empty($obj)) {
            $this->arguments["object"] = $obj;
            $this->arguments["method"] = $this->sysargs["REQUEST_METHOD"];
            $id = array_shift($args);
            if (strlen($id) > 0) {
                $this->arguments["id"] = $id;
                $subobj = array_shift($args);
                if (!empty($subobj)) {
                    $this->arguments["subobject"] = $subobj;
                    $sid = array_shift($args);
                    if (strlen($sid) > 0) {
                        $this->arguments["sid"] = $sid;
                    }
                }
            }
        }
        if (($this->arguments["method"] == "PUT") || ($this->arguments["method"] == "POST")) {
            $data = file_get_contents("php://input");
            if (strlen($data) > 0) {
                $this->arguments["data"] = json_decode($data, true);
            }
        }
        $this->arguments["restextra"] = $args;
    }

}
?>
