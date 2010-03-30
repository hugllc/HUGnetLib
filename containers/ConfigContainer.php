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
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *<?php
define("HUGNET_DATABASE", "HUGNet");
$hugnet_config["hugnet_database"] = "HUGNet";
$hugnet_config["script_gatewaykey"] = 2;
$hugnet_config["servers"][0]["driver"] = "mysql";
$hugnet_config["servers"][0]["host"] = "192.168.42.1";
$hugnet_config["servers"][0]["user"] = "PortalW";
$hugnet_config["servers"][0]["password"] = 'Por*tal';
$hugnet_config["poll_enable"] = true;
$hugnet_config["config_enable"] = true;
$hugnet_config["control_enable"] = false;
$hugnet_config["check_enable"] = true;
$hugnet_config["check_send_daily"] = true;
$hugnet_config["analysis_enable"] = true;
$hugnet_config["admin_email"] = "prices@hugllc.com";
$hugnet_config["gatewayIP"] = "127.0.0.1";
$hugnet_config["gatewayPort"] = 2000;
?>
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
        "servers"         => array(),      // The servers to use
        "hugnet_database" => "HUGnet",     // The database to use
        "script_gateway"  => 0,            // The gateway for the scripts
        "poll"            => array(        // Configuration for the poll script
            "enable"  => false,       // Enable this script
        ),
        "config"          => array(        // Configuration for the config script
            "enable"  => false,       // Enable this script
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
        "admin_email"     => "",           // Administrator Email
        "gatewayIP"       => "127.0.0.1",  // The gateway IP Address
        "gatewayPort"     => "2000",       // The port on the gateway to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();
    /** @var object This is where we store our database connection */
    protected $servers = null;
    /** @var object This is where we store our database connection */
    public $gateway = null;

    /**
    * Build everything
    *
    * @param array $config The configuration array.
    */
    public function __construct($config = array())
    {
        if (is_string($config)) {
            $config = $this->_readConfigFile($config);
        }
        parent::__construct($config);
        $this->_setServers();
        if ($this->findClass("GatewayContainer")) {
            $this->gateway = new GatewayContainer($config);
        }
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
    }
    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @return null
    */
    private function _setServers()
    {
        // Load the container
        if ($this->findClass("DBServersContainer")) {
            $this->data["servers"] = new DBServersContainer($this->servers);
        }
        // The import set $this->servers instead of $this->data["servers"].
        $this->servers = &$this->data["servers"];
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
    public function &singleton($config = array())
    {
        static $instance;

        if (!is_object($instance)) {
            $class = __CLASS__;
            $instance = new $class($config);
        }
        return $instance;
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

}
?>
