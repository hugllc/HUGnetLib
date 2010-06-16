<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../lib/plugins.inc.php";

define("HUGNET_PLUGIN_BASE_PATH", realpath(dirname(__FILE__)."/../plugins/"));


/**
 * This class contains the configuration control.
 *
 * This class is meant to be the center of the HUGnetLib code.  It ties most
 * of the stuff together.  It has the database and socket connections.  Things
 * should be gotten from here for the most part for the basic stuff.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ConfigContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "servers"         => array(),      // The database servers to use
        "sockets"         => array(),      // The gateways sockets to use
        "hooks"           => array(),      // The hooks that are available.
        "hugnet_database" => "HUGnet",     // The database to use
        "script_gateway"  => 0,            // The gateway for the scripts
        "poll"            => array(        // Configuration for the poll script
            "enable"  => false,       // Enable this script
        ),
        "config"          => array(        // Configuration for the config script
            "enable"  => false,       // Enable this script
            "deactivate" => 50,      //  How many failed attempts before disabling
                                       // The device.
        ),
        "control"         => array(        // Configuration for the control script
            "enable"  => false,       // Enable this script
        ),
        "check"           => array(        // Configuration for the check script
            "enable"      => false,   // Enable this script
            "send_daily"  => false,
        ),
        "analysis"        => array(        // Configuration for the analysis script
            "enable"  => false,       // Enable this script
        ),
        "sync"        => array(        // Configuration for the analysis script
            "downloads"  => "http://downloads.hugllc.com",   // Where to get stuff
            "firmware"   => "firmware",       // The firmware directory
        ),
        "admin_email"     => "",           // Administrator Email
        "PluginDir"       => HUGNET_PLUGIN_BASE_PATH, // This is the plugin path
        "PluginExtension" => "php",
        "PluginWebDir"    => "",
        "PluginSkipDir"   => array(),
        "useSocket"       => "default",    // What kind of socket to use
        "verbose"         => 0,            // The verbosity level
        "test"            => false,        // Test mode enabled.
    );
    /** @var array This is where the data is stored */
    protected $data = array();
    /** @var object This is where we store our database connection */
    protected $servers = null;
    /** @var object This is where we store our sockets */
    protected $sockets = null;
    /** @var object This is where we store our hooks */
    protected $hooks = null;
    /** @var object This is where we store our plugins */
    public $plugins = null;

    /** @var string The version of HUGnetLib */
    private $_version = null;

    /**
    * Build everything
    *
    * @param array $config The configuration array.
    */
    public function __construct($config = array())
    {
        $this->forceConfig($config);
    }

    /**
    * Build everything
    *
    * @param array $config The configuration array.
    *
    * @return null
    */
    public function forceConfig($config = array())
    {
        $this->clearData();
        if (is_string($config)) {
            $config = $this->_readConfigFile($config);
        }
        $this->fromArray($config);
        $this->_setServers();
        $this->_setSocket();
        $this->_setHooks();
        $this->plugins = new plugins(
            $this->PluginDir."/",
            $this->PluginExtension,
            $this->PluginWebDir,
            $this->PluginSkipDir,
            $this->verbose
        );
    }


    /**
    * Build everything
    *
    * @return null
    */
    private function _setSocket()
    {
        // Load the container
        $this->data["sockets"] = &self::factory($this->sockets, "SocketsContainer");
        // The import set $this->servers instead of $this->data["servers"].
        $this->sockets = &$this->data["sockets"];
        $this->sockets->verbose($this->verbose);
    }

    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @return null
    */
    private function _setServers()
    {
        // Load the container
        $this->data["servers"] = &self::factory(
            $this->servers, "DBServersContainer"
        );
        // The import set $this->servers instead of $this->data["servers"].
        $this->servers = &$this->data["servers"];
        $this->servers->verbose($this->verbose);
    }
    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @return null
    */
    private function _setHooks()
    {
        // Load the container
        $this->data["hooks"] = self::factory($this->hooks, "HooksContainer");
        // The import set $this->servers instead of $this->data["servers"].
        $this->hooks = &$this->data["hooks"];
    }
    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @param string $file The file to load
    *
    * @return null
    */
    private function _readConfigFile($file)
    {
        @include $file;
        if (isset($config)) {
            return (array)$config;
        } else {
            return (array)$hugnet_config;
        }
    }

    /**
    * There should only be a single instance of this class
    *
    * @param array $config The configuration array.
    *
    * @return object of type ConfigContainer
    */
    static public function &singleton($config = array())
    {
        static $instance;

        if (!is_object($instance)) {
            $class = __CLASS__;
            $instance = new $class($config);
        }
        return $instance;
    }

    /**
    * This is an alias for singleton()
    *
    * @param array $config The configuration array.
    *
    * @return object of type ConfigContainer
    */
    static public function &config($config = array())
    {
        return self::singleton($config);
    }

    /**
    * This returns the servers
    *
    * @return Array of DBServerContainers
    */
    public function &dbServers()
    {
        return $this->data["servers"];
    }

    /**
    * This returns the license text
    *
    * @return string
    */
    public function license()
    {
        // Cache this so that if it is called more than once it is fast.
        static $license;
        if (empty($license)) {
            $path = realpath(dirname(__FILE__)."/../");
            $license = file_get_contents($path."/LICENSE.TXT");
        }
        return $license;
    }

    /**
    * This returns the version
    *
    * @return string
    */
    public function version()
    {
        // Cache this so that if it is called more than once it is fast.
        static $version;
        if (empty($version)) {
            $path = realpath(dirname(__FILE__)."/../");
            $version = file_get_contents($path."/VERSION.TXT");
        }
        return $version;
    }
    /**
    * Sets the verbosity
    *
    * @param int $level The verbosity level
    *
    * @return null
    */
    public function verbose($level=0)
    {
        parent::verbose($level);
        if (is_object($this->servers)) {
            $this->servers->verbose($level);
        }
        if (is_object($this->sockets)) {
            $this->sockets->verbose($level);
        }
    }
}
?>
