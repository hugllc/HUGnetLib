<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Plugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Require the files we need */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/DeviceProcessPluginInterface.php";
/**
 * Base class for all other classes
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Plugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DeviceProcessPluginBase extends HUGnetClass
    implements DeviceProcessPluginInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Default",
        "Type" => "deviceProcess",
        "Class" => "dummy",
        "Priority" => 50,
    );
    /** @var This is when we were created */
    protected $created = 0;
    /** @var This is when we were last run */
    protected $last = 0;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param mixed          $config The configuration array
    * @param DeviceAnalysis &$obj   The controller object
    *
    * @return null
    */
    public function __construct($config, DeviceProcess &$obj)
    {
        parent::__construct($config);
        $this->created = time();
        $this->control = &$obj;
        $class = get_class($this);
        $this->enable = (boolean)$this->control->myConfig->pluginData[$class]["enable"];
    }
    /**
    * This function does the stuff in the class.
    *
    * @param DeviceContainer &$dev The device to check
    *
    * @return bool True if ready to return, false otherwise
    */
    public function ready(DeviceContainer &$dev)
    {
        return true;
    }
    /**
    * This runs before once as the last part of the constructor
    *
    * @param DeviceContainer &$dev The device to check
    *
    * @return bool True if ready to return, false otherwise
    */
    public function pre(DeviceContainer &$dev)
    {
        return true;
    }
}


?>
