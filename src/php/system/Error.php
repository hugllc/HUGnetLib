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
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Error extends \HUGnet\base\SystemTableBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** @var int The database table class to use */
    protected $tableClass = "Error";
    /** notice level severity */
    const NOTICE = 1;
    /** warning level severity */
    const WARNING = 2;
    /** error level severity */
    const ERROR = 4;
    /** critical level severity */
    const CRITICAL = 8;
    /** @var Severity level for syslog */
    private $_syslog = array(
        self::NOTICE => LOG_NOTICE,
        self::WARNING => LOG_WARNING,
        self::ERROR => LOG_ERR,
        self::CRITICAL => LOG_CRIT,
    );
    /** This is our url */
    protected $url = "/error";
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The configuration array
    * @param string $table   The table class to use
    * @param array  $data    The initial data for this object
    *
    * @return null
    */
    public function __construct(&$system, $table, $data = array())
    {
        parent::__construct($system, $table, $data);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $table="Error")
    {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Logs an error in the database
    *
    * @param mixed  $errno    The error number.  Could be a string or number
    * @param string $errmsg   The error message
    * @param string $severity The severity of the message
    * @param string $method   Should be filled with __METHOD__
    * @param string $class    The classs calling the error
    *
    * @return null
    */
    public function log($errno, $errmsg, $severity, $method, $class)
    {
        $this->table()->fromAny(
            array(
                "class"    => $class,
                "method"   => $method,
                "errno"    => $errno,
                "error"    => $errmsg,
                "Date"     => $this->system()->now(),
                "Severity" => $severity,
            )
        );
        return $this->table()->insertRow(true);
    }
    /**
    * Logs an error in the database
    *
    * @param string $msg      The error message
    * @param string $severity The severity of the message
    *
    * @return null
    */
    public function syslog($msg, $severity = self::WARNING)
    {
        if (isset($this->_syslog[$severity])) {
            $sev = $this->_syslog[$severity];
        } else {
            $sev = LOG_NOTICE;
        }
        return syslog($sev, $msg);
    }
    /**
    * Throws an exception
    *
    * @param string $msg  The message
    * @param string $type The type of exception to throw
    *
    * @return null
    */
    public function exception($msg, $type = "Runtime")
    {
        $class = "\\".$type."Exception";
        if (class_exists($class)) {
            throw new $class($msg);
        } else {
            throw new \RuntimeException($msg);
        }
    }
}


?>
