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
        // The following are deprectated.  These are moved into the arrays above
    );
    /** @var array This is where the data is stored */
    protected $data = array();
    /** @var object This is where we store our database connection */
    private $_servers = null;

    /**
    * Build everything
    *
    * @param array $config The configuration array.
    */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_setServers();
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
        if (count($this->data["servers"]) < 1) {
            $this->data["servers"][] = array();
        }
        foreach ((array)$this->data["servers"] as $key => $serv) {
            $this->_servers[$key] =& self::factory($serv, "DBServerContainer");
        }
        $this->data["servers.old"] = $this->data["servers"];
        $this->data["servers"] = &$this->_servers;
    }

}
?>
