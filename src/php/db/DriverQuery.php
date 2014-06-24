<?php
/**
 * Abstract class for building SQL queries
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/connections/PDO.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo \PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverQuery
{

    /** @var object This is where we store our system object */
    protected $system = null;
    /** @var object This is where we store our connection object */
    protected $connect = null;

    /** @var object This is where we store our \PDO */
    protected $pdo = null;
    /** @var object This is where we store our table object */
    protected $myTable = null;
    /** @var string This is where we store the query */
    protected $query = "";
    /** @var string The name of this driver */
    protected $driver = "";

    /**
    * Register this database object
    *
    * @param object &$system  The system object
    * @param object &$table   The table object
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, &$table, &$connect)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a table object",
            !is_object($table)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a connection object",
            !is_object($connect)
        );
        $this->system = &$system;
        $this->connect = $connect;
        $this->myTable = &$table;
        // Connect to the database
        $this->connect();
    }
    /**
    * This gets a new PDO object
    *
    * @return null
    */
    protected function disconnect()
    {
        $this->connect->disconnect($this->myTable->get("group"));
    }
    /**
    * This gets a new PDO object
    *
    * @return null
    */
    protected function &pdo()
    {
        $group = $this->myTable->get("group");
        return $this->connect->getDBO($group);
    }
    /**
    * This gets a new PDO object
    *
    * @return null
    */
    protected function connect()
    {
        $group = $this->myTable->get("group");
        $this->system->fatalError(
            "No available database connection available in group '".$group
            ."'.  Check your database configuration.  Available php drivers: "
            .implode(", ", \PDO::getAvailableDrivers())." "
            .print_r($this->connect->config($group), true),
            !is_a($this->pdo(), "\PDO")
        );
        $verbose = $this->system->get("verbose");
        if ($verbose > 5) {
            $this->pdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } else if ($verbose > 1) {
            $this->pdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        } else {
            $this->pdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        }
        $this->pdo()->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
    }

    /**
    * Gets an attribute from the \PDO object
    *
    * @param string $attrib The attribute to get.
    *
    * @return mixed
    */
    public function getAttribute($attrib)
    {
        if (is_object($this->pdo())) {
            $ret = $this->pdo()->getAttribute($attrib);
        }
        return $ret;
    }
    /**
    * This function deals with errors
    *
    * @param array  $errorInfo The output of any of the pdo errorInfo() functions
    * @param string $method    Not used anymore
    * @param string $severity  The severity of the error.  This should be fed with
    *                          ErrorTable::SEVERITY_WARNING, et al.
    *
    * @return mixed
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function errorHandler($errorInfo, $method, $severity)
    {
        if ($this->myTable->sqlTable != "errors") {
            $this->system->error(
                $this->myTable->get("group")." (".$this->myTable->sqlTable."): "
                .$errorInfo[2],
                $severity
            );
        }
        $this->system->out(
            "With Error: ".print_r($errorInfo, true),
            8
        );
    }
    /**
    * .Queries the database
    *
    * This function is meant for very small sql statements, like those from
    * nextID and prevID.  It is also meant to be used where it needs to not mess
    * up a query in progress.
    *
    * @param array $query The query string
    * @param array $data  Data to use for the query
    *
    * @return array
    */
    public function query($query = "", $data = array())
    {
        $pdo = $this->pdo()->prepare($query);

        $res = false;
        if (is_object($pdo)) {
            $this->system->out(
                "Executing (group: ".$this->myTable->get("group")."): "
                .print_r($query, true),
                8
            );
            $this->system->out(
                "With Data: ".print_r($data, true),
                8
            );
            $ret = $pdo->execute($data);
            $this->system->out(
                "With Result: ".print_r($ret, true)
                . "(".$pdo->rowCount()." rows)",
                8
            );
            if ($ret) {
                $res = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $error = $pdo->errorInfo();
            }
            $pdo->closeCursor();
        } else {
            $error = $this->pdo()->errorInfo();
        }
        // Set the errors if there are any and we are not on table 'errors'
        if (is_array($error)) {
            $this->errorHandler(
                $error,
                __METHOD__,
                1 //\ErrorTable::SEVERITY_WARNING
            );
        }
        return $res;
    }
}


?>
