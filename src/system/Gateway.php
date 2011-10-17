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
/** This is the HUGnet namespace */
namespace HUGnet;

/** This is our base class */
require_once dirname(__FILE__)."/../tables/GatewaysTable.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
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
 */
class Gateway
{
    /** @var int Our configuration */
    private $_config = array();
    /** @var int The database table to use */
    private $_table = null;
    /** @var int The database table class to use */
    protected $tableClass = "\GatewaysTable";
    /** @var int The verbosity level */
    private $_configDefault = array(
        "verbose" => 0,
    );
    /** @var This is where we store our configuration */
    protected $myConfig = null;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param array $config The configuration array
    *
    * @return null
    */
    private function __construct($config = array())
    {
        $this->_config = array_merge($this->_configDefault, (array)$config);
    }
    /**
    * This function creates the system.
    *
    * @param mixed $config (array)The configuration, (string) File path to open
    * @param mixed $gateway (int)The id of the gateway, (array) Gateway info array
    *
    * @return null
    */
    public static function &create($config = array(), $gateway=null)
    {
        $class = get_called_class();
        $gate = new $class($config);
        $gate->setRecord($gateway);
        return $gate;
    }
    /**
    * This function creates the system.
    *
    * @param mixed $gateway (int)The id of the gateway,
    *                       (array) or (string) Gateway info array
    *
    * @return null
    */
    public function setRecord($gateway)
    {
        $class = $this->tableClass;
        $ret = false;
        if (is_int($gateway)) {
            $this->_table = new $class();
            $ret = $this->_table->getRow($gateway);
        } else if (is_array($gateway) || is_string($gateway)) {
            $this->_table = new $class($gateway);
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * This is the destructor
    */
    function __destruct()
    {
    }

}


?>
