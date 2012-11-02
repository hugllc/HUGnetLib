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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is our system class */
require_once dirname(__FILE__)."/HTML.php";

/**
 * This code routes packets to their correct destinations.
 *
 * This is the router class, essentially.  It will take packets and figure out
 * which network interface to send them out.  This implements the Network layer
 * of the OSI model.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WebAPI extends HTML
{
    /** The config we are using */
    private $_config = array();
    /** The arguments we got */
    private $_args = array();
    /** The system object */
    private $_system = null;

    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    * @param mixed &$system The system object to use
    *
    * @return null
    */
    static public function &factory(&$config = array(), &$system = null)
    {
        $obj = new WebAPI($config, $system);
        return $obj;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        $this->out(get_class($this)." destroying system", 3);
        // Shut down the network
        unset($this->_system);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    public function execute($extra = array())
    {
        $task = (string)$this->args()->get("task");
        $method = "_execute".ucfirst(strtolower($task));
        if (method_exists($this, $method)) {
            $ret = $this->{$method}($extra);
        }
        $this->_body($ret);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeDevice($extra = array())
    {
        $did = hexdec($this->args()->get("id"));
        $dev = &$this->system()->device($did);
        return $this->_executeSystem($dev, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeDatacollector($extra = array())
    {
        $uuid = strtolower($this->args()->get("id"));
        $datacol = &$this->system()->datacollector(array("uuid" => $uuid));
        return $this->_executeSystem($datacol, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeSystem($obj, $extra = array())
    {
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            $ret = $obj->toArray(true);
        } else if ($action === "put") {
            $data = (array)$this->args()->get("data");
            $obj->change($data);
            // Reload it, so that we get what is in the database
            $did = hexdec($this->args()->get("id"));
            $obj->load($did);
            $ret = $obj->toArray(true);
        } else if ($action === "list") {
            $data = $this->args()->get("data");
            $ret = $obj->getList($data, true);
        } else {
            if (is_callable(array($obj, "webAPI"))) {
                $ret = $obj->webAPI($this->args(), $extra);
            }
        }
        return $ret;
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerNoCache()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 4 Apr 1998 20:00:00 GMT');
        }
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerCSV()
    {
        if (!headers_sent()) {
            header('Content-type: text/csv');
            header(
                'Content-disposition: attachment;'
                .'filename=HUGnetLab.'.$this->args()->get("id").'.csv'
            );
        }
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerJSON()
    {
        if (!headers_sent()) {
            header('Content-type: application/json');
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    private function _body($data)
    {
        $format = trim($this->args()->get("format"));
        if (strtoupper($format) === "CSV") {
            $this->_headerNoCache();
            $this->_headerCSV();
            print $data;
        } else {
            $this->_headerNoCache();
            $this->_headerJSON();
            if (!is_null($data)) {
                print json_encode($data);
            }
        }
    }
}
?>
