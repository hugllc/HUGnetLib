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
/** This define allows everything else to be included */
define("_HUGNET", true);
/** This is our base class */
require_once dirname(__FILE__)."/containers/ConfigContainer.php";
/** This is the system error class.  Everybody needs it */
require_once dirname(__FILE__).'/system/Error.php';
/** This is the system utility class.  Everybody needs it also */
require_once dirname(__FILE__).'/util/Util.php';


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
class HUGnetSystem
{
    /** @var array The configuration that we are going to use */
    private $_config = array();
    /** @var array The default configuration */
    private $_configDefault = array(
        "verbose" => 0,
    );
    /** @var This is where we store our configuration */
    protected $myConfig = null;

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param array $config The configuration array
    *
    * @return null
    */
    private function __construct($config = array())
    {
        $this->setConfig($config);
    }
    /**
    * This function creates the system.
    *
    * @param mixed $config (array)The configuration, (string) File path to open
    *
    * @return null
    */
    public static function &create($config = array())
    {
        return new HUGnetSystem($config);
    }
    /**
    * This sets the configuration array _config
    *
    * @param array $config The configuration array
    *
    * @return null
    * @todo remove ConfigContainer reference when ConfigContainer goes away
    */
    public function setConfig($config = array())
    {
        $this->_config = array_merge($this->_configDefault, (array)$config);

        // This is so that the rest of the system works when we call it through
        // This class.  This should be removed when ConfigContainer is retired.
        ConfigContainer::singleton()->forceConfig($this->_config);
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
    }

}


?>
