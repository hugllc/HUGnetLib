<?php
/**
 * Main driver for the filters
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Filters
 * @package    HUGnetLib
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
 * A class for filtering endpoint data.  This class implements drivers that actually
 * do the filtering.
 *
 * @category   Filters
 * @package    HUGnetLib
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Filter
{

    /**
     * The constructor.  This sets everything up and finds the plugins.
     *
     * @param object &$plugins This is an object of class plugins.
     *
     * @see plugins
      */
    function __construct(&$plugins = "") 
    {
        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
            $plugins = new Plugins(dirname(__FILE__)."/drivers/", "php");
        }

        foreach ($plugins->plugins["Generic"]["filter"] as $driver) {
            $this->registerFilter($driver["Class"]);
        }
    }

    /**
     * Register a filter class.
     *
     * @param mixed  $class The name of the sensor class to register, or the actual object
     * @param string $name  The name of the class if the above is an object.
     *
     * @return bool true on success, false on failure
      */
    public function registerFilter($class, $name=false) 
    {
        if (is_string($class) && class_exists($class)) {
            $this->filters[$class] = new $class();
        } else if (is_object($class)) {
            if (empty($name)) $name = get_class($class);
            $this->filters[$name] = $class;
            $class                = $name;
        } else {
            return false;
        }

        if (is_array($this->filters[$class]->filters)) {
            foreach ($this->filters[$class]->filters as $type => $sInfo) {
                foreach ($sInfo as $filter => $val) {
                    $this->dev[$type][$filter] = $class;
                }
            }
            return true;
        } else {
            return false;
        }
     
    }

    /**
     * Filters the history given to it based on the filters specified
     *
     * @param array &$history The history to filter
     * @param array $filters  The array of filters to use
     *
     * @return none
     */
    public function filter(&$history, $filters) 
    {
        if (!is_array($filters)) return;
        foreach ($filters as $key => $filter) {
            $this->_filterData($history, $key, $filter);
        }
    }
         
    
    /**
     * This function does the actual filtering of the data based on the input given.
     *
     * @param array  &$data  The data to filter
     * @param int    $index  The index to use in the data array
     * @param string $filter Which filter to use
     *
     * @return none
     */
    private function _filterData(&$data, $index, $filter=null) 
    {
        $class = $this->getClass($type, $filter);
        if (!is_object($class)) return;
        $args = func_get_args();
        unset($args[2]); // Remove the $filter
        $stuff   = $class->filters[$type][$filter];
        $args[2] = $stuff;
        $this->runFunction($class, $stuff['function'], $args);
    }

    /**
     * Runs the filter function based on the information given.
     *
     * @param object &$class   This is the filter class to run the function on
     * @param string $function This is the method to call on the class
     * @param array  &$args    The array of arguments for the function
     *
     * @return none
      */
    function runFunction(&$class, $function, &$args) 
    {
        if (!is_string($function)) return;
        if (!method_exists($class, $function)) return;
        $ret = call_user_func_array(array(&$class, $function), $args);
        if (!empty($ret)) $args[0] = $ret;
    }

    /**
     * Returns the class.  If you want the default filter for the filter type
     * Just give $filter a blank variable.  This will be set to the name of the filter
     * tat it finds.
     *
     * @param string $type    The type of filter
     * @param string &$filter The filter to implement.  This can be changed by this routine.
     *
     * @return object
      */
    function &getClass($type, &$filter) 
    {
        $class = $this->dev[$type][$filter];
        if (is_null($class)) {
            if (is_array($this->dev[$type])) {
                reset($this->dev[$type]);
                $filter = key($this->dev[$type]);
                $class  = current($this->dev[$type]);
            }            
        }
        return $this->filters[$class];    
    }
}


/**
 * Base class for filters.
 *
 * @category   Filters
 * @package    HUGnetLib
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class Filter_Base
{
    /**
        This defines all of the filters that this driver deals with...
     */
    var $filters = array();
    
    /**
        Constructor.
     */
    function __construct()
    {

    }


}

?>
