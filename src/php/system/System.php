<?php
/**
 * Classes for dealing with devices
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet;

/** This define allows everything else to be included */
if (!defined("_HUGNET")) {
    define("_HUGNET", true);
}
/** This is the system error class.  Everybody needs it */
require_once dirname(__FILE__).'/Error.php';
/** This is the system utility class.  Everybody needs it also */
require_once dirname(__FILE__).'/../util/Util.php';


/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be included
 * to get HUGnetLib functionality.  This class will load everything else it needs,
 * so the user doesn't have to worry about it.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class System
{
    /** @var array The configuration that we are going to use */
    private $_config = array();
    /** @var object The configuration that we are going to use */
    private $_dbconnect = array();
    /** @var object This is our network configuration */
    private $_network = null;
    /** @var object This is our user interface */
    private $_ui = null;
    /** @var object This is our user interface */
    private $_quit = false;
    /** @var array This is our static things that get might want to retrieve */
    private $_fixed = array();
    /** @var array The default configuration */
    private $_configDefault = array(
        "verbose" => 0,
    );

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param array  $config     The configuration array
    * @param object &$interface The user interface to use
    *
    * @return null
    */
    private function __construct($config = array(), &$interface = null)
    {
        $this->config((array)$config);
        $this->_dbconnect = \HUGnet\db\Connection::factory($this);
        $this->_ui = $interface;
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $config     (array)The configuration, (string) File path to open
    * @param object &$interface The user interface to use
    *
    * @return null
    */
    public static function &factory($config = array(), &$interface = null)
    {
        $obj = new System($config, $interface);
        return $obj;
    }
    /**
    * This sets the configuration array _config
    *
    * @param array $config The configuration array
    *
    * @return array The configuration
    */
    public function config($config = null)
    {
        if (is_array($config)) {
            $this->_config = array_merge($this->_configDefault, (array)$config);
            $this->_fixed["version"] = trim(
                file_get_contents(dirname(__FILE__)."/../VERSION.TXT")
            );
        }
        // Return the configuration
        return $this->_config;
    }
    /**
    * This gets the runtime configuration
    *
    * @param array $config The configuration array
    *
    * @return array The configuration
    */
    public function runtime($config = null)
    {
        $dev =  $this->dataCollector(array("uuid" => $this->get("uuid")));
        if (is_array($config)) {
            $dev->set("Runtime", json_encode($config));
            $dev->store();
        }
        return (array)json_decode($dev->get("Runtime"), true);
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_error);
    }
    /**
    * This sets the configuration array _config
    *
    * @param object &$application The application to use
    *
    * @return null
    */
    public function &network(&$application = null)
    {
        if (!is_object($this->_network)) {
            if (is_object($application)) {
                $this->_network = &$application;
            } else {
                $config = $this->get("network");
                $net = dirname(__FILE__)."/../network/";
                if (is_array($config) && count($config) > 0) {
                    include_once $net."Application.php";
                    include_once $net."Transport.php";
                    include_once $net."Network.php";
                    $network   = network\Network::factory($config);
                    $transport = network\Transport::factory(
                        $network, $config
                    );
                    $this->_network = network\Application::factory(
                        $transport, $this, $config
                    );
                } else {
                    include_once $net."Dummy.php";
                    /* No network config, so give them a dummy */
                    $this->_network = network\Dummy::factory($this);
                }
            }
        }
        return $this->_network;
    }
    /**
    * The main routine should be called periodically (once per loop at least)
    *
    * @return null
    */
    public function main()
    {
        $this->quit();
        // Call it this way so we don't create the object just for this
        if (is_object($this->_network)) {
            $this->_network->main();
        }
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        if (isset($this->_fixed[$field])) {
            return $this->_fixed[$field];
        } else if (isset($this->_config[$field])) {
            return $this->_config[$field];
        }
        return null;
    }
    /**
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &device($data = array())
    {
        include_once dirname(__FILE__)."/Device.php";
        $obj = Device::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a dataCollector object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &dataCollector($data = array())
    {
        include_once dirname(__FILE__)."/DataCollector.php";
        $obj = DataCollector::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a dataCollector object.
    *
    * @param array $string The data to send to the device object
    * @param int   $level  The debug level to print it at
    *
    * @return Reference to a device object
    */
    public function out($string, $level = 0)
    {
        if (is_object($this->_ui)) {
            return $this->_ui->out($string, $level);
        }
    }
    /**
    * This returns a dataCollector object.
    *
    * @param string $table The name of the table
    * @param array  $data  The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &table($table, $data = array())
    {
        return \HUGnet\db\Table::factory(
            $this,
            $data,
            $table,
            $this->_dbconnect
        );
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param string $type      The type of exception to throw
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    public static function exception($msg, $type = "Runtime", $condition = true)
    {
        if ((boolean)$condition) {
            $class = "\\".$type."Exception";
            if (class_exists($class)) {
                throw new $class($msg);
            } else {
                throw new \RuntimeException($msg);
            }
        }
    }
    /**
    * Throws an exception
    *
    * @return null
    */
    public static function loopcheck()
    {
        // This dispatches any signals
        if (function_exists("pcntl_signal_dispatch")) {
            pcntl_signal_dispatch();
        }
    }
    /**
    * Throws an exception
    *
    * @return null
    */
    public function quit($set = null)
    {
        if ($this->_quit) {
            return true;
        } else if (is_bool($set)) {
            $this->_quit = $set;
        } else {
            // This dispatches any signals
            if (function_exists("pcntl_signal_dispatch")) {
                pcntl_signal_dispatch();
            }
        }
        return $this->_quit;
    }

}


?>
