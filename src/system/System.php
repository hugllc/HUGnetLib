<?php
/**
 * Classes for dealing with devices
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
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet;

/** This define allows everything else to be included */
define("_HUGNET", true);
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class System
{
    /** @var array The configuration that we are going to use */
    private $_config = array();
    /** @var array The default configuration */
    private $_configDefault = array(
        "verbose" => 0,
    );

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param array $config The configuration array
    *
    * @return null
    */
    private function __construct($config = array())
    {
        $this->config($config);
    }
    /**
    * This function creates the system.
    *
    * @param mixed $config (array)The configuration, (string) File path to open
    *
    * @return null
    */
    public static function &factory($config = array())
    {
        return new System($config);
    }
    /**
    * This sets the configuration array _config
    *
    * @param array $config The configuration array
    *
    * @return array The configuration
    * @todo remove ConfigContainer reference when ConfigContainer goes away
    */
    public function config($config = array())
    {
        $this->_config = array_merge($this->_configDefault, (array)$config);

        // This is so that the rest of the system works when we call it through
        // This class.  This should be removed when ConfigContainer is retired.
        include_once dirname(__FILE__).'/../containers/ConfigContainer.php';
        \ConfigContainer::singleton()->forceConfig($this->_config);

        // Return the configuration
        return $this->_config;
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
    * @return null
    */
    public function &network(&$application = null)
    {
        if (!is_object($this->_network)) {
            if (is_object($application)) {
                $this->_network = &$application;
            } else {
                $net = dirname(__FILE__)."/../network/";
                include_once $net."Application.php";
                include_once $net."Transport.php";
                include_once $net."Network.php";
                $network = &network\Network::factory($this->_config["network"]);
                $transport = &network\Transport::factory(
                    $network, $this->_config["network"]
                );
                $this->_network = &network\Application::factory(
                    $transport, $this->_config["network"]
                );
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
        pcntl_signal_dispatch();
        // Call it this way so we don't create the object just for this
        if (is_object($this->_network)) {
            $this->_network->main();
        }
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

}


?>
