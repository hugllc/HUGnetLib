<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is the base of our table class */
require_once dirname(__FILE__)."/SystemTableBase.php";
/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class SystemTableAction extends SystemTableBase
{
    /** This is where we store our objects */
    private $_callCache = array();
    /** This is where we store our objects */
    protected $functions = array();
    /** This is where we store our objects */
    protected $classes = array();

    /**
    * This is the destructor
    */
    public function __destruct()
    {
        parent::__destruct();
        foreach (array_keys($this->_callCache) as $key) {
            unset($this->_callCache[$key]);
        }
    }
    /**
    * This this pulls together all of the extra classes and objects
    *
    * The extra classes and objects that help out with this object are tied
    * together here.
    *
    * @param string $name The name of the function called
    * @param array  $args The arguments of the called function
    *
    * @return mixed The return of the function called
    */
    public function &__call($name, $args)
    {
        $ret = null;
        if (isset($this->functions[$name])) {
            $class = $this->functions[$name];
            if (method_exists($this, $class)) {
                $obj = $this->$class();
                return call_user_func_array(
                    array($obj, $name), $args
                );
            } else if (isset($this->classes[$class])) {
                $this->_setupClass($class);
                return call_user_func_array(
                    array($this->_callCache[$class], $name), $args
                );
            }
            // @codeCoverageIgnoreStart
        }
        // @codeCoverageIgnoreEnd
        \HUGnet\System::exception(
            "Call to undefined method $name on ".get_class($this)
        );
        // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd
    /**
    * This makes sure a class is in the cache
    *
    * @param string $class The name of the class to set up
    *
    * @return null
    */
    private function _setupClass($class)
    {
        if (!is_object($this->_callCache[$class])) {
            $name = $this->classes[$class];
            $this->_callCache[$class] = $name::factory(
                $this->system(),
                $this
            );
        }
    }

}


?>
