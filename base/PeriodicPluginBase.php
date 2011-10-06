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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Require the files we need */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/PeriodicPluginInterface.php";
/**
 * Base class for all other classes
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class PeriodicPluginBase extends HUGnetClass
    implements PeriodicPluginInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Default",
        "Type" => "periodic",
        "Class" => "dummy",
    );
    /** @var This is when we were created */
    protected $created = 0;
    /** @var This is when we were last run */
    protected $last = 0;
    /** @var This says if we are enabled or not */
    protected $enable = true;
    /** @var This is our configuration */
    protected $conf = null;
    /** @var This is our configuration */
    protected $defConf = array();

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param mixed           $config The configuration array
    * @param PeriodicPlugins &$obj   The controller object
    *
    * @return null
    */
    public function __construct($config, PeriodicPlugins &$obj)
    {
        parent::__construct($config);
        $this->created = time();
        $this->control = &$obj;
        $class = get_class($this);
        $this->control->myConfig->pluginData[$class] = array_merge(
            (array)$this->defConf,
            (array)$this->control->myConfig->pluginData[$class]
        );
        $this->conf = &$this->control->myConfig->pluginData[$class];
        $this->enable = (boolean)$this->conf["enable"];
    }
    /**
    * This function checks to see if it is ready to run again
    *
    * The default is to run every 24 hours.
    *
    * @return bool True if ready to return, false otherwise
    */
    public function ready()
    {
        // Run every 24 hours
        return $this->enable && (time() >= ($this->last + 86400));
    }

}


?>
