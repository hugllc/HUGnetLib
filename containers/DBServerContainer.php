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
class DBServerContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "driver" => "sqlite",        // The driver to use
        "host"   => "localhost",     // The server to contact
        "port"   => 3306,            // The port to use
        "db"     => "HUGnet",        // The database to use
        "socket" => "",              // Unix socket to use
        "user"   => "",              // Username to log in as
        "password" => "",            // Password to use
        "options" => array(),        // Options to use
        "file"    => ":memory:",     // The file for sqlite
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @return null
    */
    function getDSN()
    {
        $this->driver = strtolower($this->driver);

        if ($this->driver == "mysql") {
            $dsn  = $this->driver.":";
            if (!empty($this->socket)) {
                $dsn .= "unix_socket=".$this->socket.";";
            } else {
                $dsn .= "host=".$this->host.";";
                $dsn .= "port=".$this->port.";";
            }
            $dsn .= "dbname=".$this->db;
        } else {
            if (empty($this->file)) {
                $this->setDefault("file");
            }
            $dsn = "sqlite:".$this->file;
        }
        return (string)$dsn;
    }

}
?>
