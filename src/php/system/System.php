<?php
/**
 * Classes for dealing with devices
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
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
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
/** This is the system utility class.  Everybody needs it also */
require_once dirname(__FILE__).'/../util/Util.php';
/** THis is our db connection class */
require_once dirname(__FILE__).'/../db/Connection.php';
/** This is the error class */
require_once dirname(__FILE__).'/Error.php';


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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
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
    /** @var bool This is our quit flag */
    private $_quit = false;
    /** @var object This is our user interface */
    private $_error = null;
    /** @var bool This says if a fatal error has happened */
    private $_fatalError = false;
    /** @var array This is our static things that get might want to retrieve */
    private $_fixed = array(
        "nodename" => "unknown",
        "domainname" => "example.com",
        "fqdn" => "unknown.example.com",
        "phpversion" => PHP_VERSION,
    );
    /** @var array The default configuration */
    private $_configDefault = array(
        "verbose" => 0,
        "min_log" => Error::ERROR,
    );

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param array  $config    The configuration array
    * @param object $interface The user interface to use
    * @param object $error     The error object
    *
    * @return null
    */
    public function __construct(
        $config = array(), $interface = null, $error = null
    ) {
        if (function_exists("posix_uname")) {
            $uname = posix_uname();
            $uname["osversion"] = $uname["version"];
            $uname["fqdn"] = $uname["nodename"].".".$uname["domainname"];
            $this->_fixed = array_merge($this->_fixed, $uname);
        }
        $this->config((array)$config);
        $this->_ui = $interface;
        if (is_object($error)) {
            $this->_error = &$error;
        }
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $config    (array)The configuration, (string) File path to open
    * @param object $interface The user interface to use
    * @param object $error     The error object
    *
    * @return null
    */
    public static function &factory(
        $config = array(), $interface = null, $error = null
    ) {
        $obj = new System($config, $interface, $error);
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
            $confdir = dirname((string)$config['file']);
            $this->_fixed["confdir"] = (empty($confdir)) ? '/etc/hugnet' : $confdir;
            // Re-create the database connection
            unset($this->_dbconnect);
            $this->_dbconnect = \HUGnet\db\Connection::factory($this);
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
        $dev = $this->dataCollector(array("uuid" => $this->get("uuid")));
        if (is_array($config)) {
            $dev->set("GatewayKey", $this->get("GatewayKey"));
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
        unset($this->_ui);
        unset($this->_network);
        unset($this->_dbconnect);
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
                    $network   = network\Network::factory($this, $config);
                    $transport = network\Transport::factory(
                        $network, $this, $config
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
    * Returns the database connection object
    *
    * @return object The database connection object
    */
    public function &dbconnect()
    {
        return $this->_dbconnect;
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
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &annotation($data = array())
    {
        include_once dirname(__FILE__)."/Annotation.php";
        $obj = Annotation::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &test($data = array())
    {
        include_once dirname(__FILE__)."/Test.php";
        $obj = Test::factory($this, $data);
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
    * This returns a gateway object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &gateway($data = array())
    {
        include_once dirname(__FILE__)."/Gateway.php";
        $obj = Gateway::factory($this, $data);
        return $obj;
    }
    /**
    * This returns an image object.
    *
    * @param array $data The data to send to the object
    *
    * @return Reference to an image object
    */
    public function &image($data = array())
    {
        include_once dirname(__FILE__)."/Image.php";
        $obj = Image::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &inputTable($data = array())
    {
        include_once dirname(__FILE__)."/InputTable.php";
        $obj = InputTable::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &outputTable($data = array())
    {
        include_once dirname(__FILE__)."/OutputTable.php";
        $obj = OutputTable::factory($this, $data);
        return $obj;
    }
    /**
    * This returns a device object.
    *
    * @param array $data The data to send to the device object
    *
    * @return Reference to a device object
    */
    public function &processTable($data = array())
    {
        include_once dirname(__FILE__)."/ProcessTable.php";
        $obj = ProcessTable::factory($this, $data);
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
    * This returns the time
    *
    * @return int The time in unix format
    */
    public function now()
    {
        return time();
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    public static function systemMissing($msg, $condition)
    {
        if ((boolean)$condition) {
            syslog(LOG_CRIT, $msg);
            throw new \InvalidArgumentException($msg);
        }
    }
    /**
    * Gives us our error class
    *
    * @return null
    */
    public function error($id=null)
    {
        if (!is_object($this->_error)) {
            $this->_error = Error::factory($this, $id);
        }
        return $this->_error;
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param int    $severity  The severity of the error
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    public function logError($msg, $severity, $condition = true)
    {
        return $this->_setError($msg, $severity, $condition);
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param int    $severity  The severity of the error
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    private function _setError($msg, $severity, $condition = true)
    {
        if (!(boolean)$condition) {
            return false;
        } else if ($severity < $this->get("min_log")) {
            return true;
        }
        $this->error()->syslog($msg, $severity);
        if ($this->dbconnect()->available()) {
            $debug = debug_backtrace();
            $method = "unknown";
            $class = "unknown";
            if (isset($debug[2])) {
                $method = $debug[2]["function"];
                $class = $debug[2]["class"];
            }
            $ret = $this->error()->log(-1, $msg, $severity, $method, $class);
        }
        return $ret;
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    public function fatalError($msg, $condition = true)
    {
        if (!(boolean)$condition) {
            return false;
        }
        if (!$this->_fatalError) {
            $this->_fatalError = true;
            $this->_setError($msg, Error::CRITICAL);
        }
        $this->error()->exception($msg, "Runtime");
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
    * Causes everything to exit
    *
    * @param mixed $set (null) Don't set anything, (bool) set quit to this value
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
    /**
    * Set the verbosity level
    *
    * @param mixed $set (null) set the default value, otherwise set the value given
    *
    * @return null
    */
    public function verbose($set = null)
    {
        return $this->_ui->verbose($set);
    }
}


?>
