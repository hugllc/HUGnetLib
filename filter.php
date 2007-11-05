<?php
/**
 *   Main driver for the filters
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package HUGnetLib
 * @subpackage Filters
 * @copyright 2007 Hunt Utilities Group, LLC
 * @author Scott Price <prices@hugllc.com>
 * @version $Id: unitConversion.inc.php 369 2007-10-12 15:05:32Z prices $    
 *
 */
/**
 * A class for filtering endpoint data.  This class implements drivers that actually
 * do the filtering.
 */
class filter {

    /**
     * The constructor.  This sets everything up and finds the plugins.
     *
     * @param object $plugins This is an object of class plugins.
     * @see plugins
     */
    function __construct(&$plugins = "") {
        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
            $plugins = new plugins(dirname(__FILE__)."/drivers/", "php");
        }

        foreach($plugins->plugins["Generic"]["filter"] as $driver) {
            if (class_exists($driver["Class"])) {
                $class = $driver["Class"];
                $this->filters[$class] = new $class();
                if (is_array($this->filters[$class]->filters)) {
                    foreach($this->filters[$class]->filters as $type => $sInfo) {
                        foreach($sInfo as $filter => $val) {
                            $this->dev[$type][$filter] = $class;
                        }
                    }
                }
            }
        }
    }

    /**
     * This function does the actual filtering of the data based on the input given.
     *
     * Return the voltage
     * @param array $data
     * @param string $type
     * @param string $filter    
     * @return array The filtered data
    */
    function filterdata(&$data, $type, $filter=NULL) 
    {
        $class = $this->getClass($type, $filter);
        if (is_object($class)) {
            $args = func_get_args();
            $args[1]; // Remove the $type
            unset($args[2]); // Remove the $filter
            $stuff = $class->filters[$type][$filter];
            $args[1] = $stuff;
            $args = array_merge($args); // Compacts the array
            $val = $this->runFunction($class, $stuff['function'], $args, $args[0]);
        }
        return($val);
    }

    /**
     * Runs the filter function based on the information given.
     *
     * @param object $class This is the filter class to run the function on
     * @param string $function This is the method to call on the class
     * @param array $args The array of arguments for the function
     * @param mixed $return This is the default value to return if the function is not found
     * @return array The filtered data
     */
    function runFunction(&$class, $function, &$args, &$return = NULL) {
        if (isset($function)) {
            if (method_exists($class, $function)) {
                $fct = array(&$class, $function);
                call_user_func_array($fct, $args);
            }
        }
        return $return;
    }

    /**
     *   Returns the class.  If you want the default filter for the filter type
     * Just give $filter a blank variable.  This will be set to the name of the filter
     * tat it finds.
     *
     * @param string $type The type of filter
     * @param string $filter The filter to implement.  This can be changed by this routine.
     */
    function &getClass($type, &$filter) {
        $class = $this->dev[$type][$filter];
        if (is_null($class)) {
            if (is_array($this->dev[$type])) {
                reset($this->dev[$type]);
                $filter = key($this->dev[$type]);
                $class = current($this->dev[$type]);
            }            
        }
        return $this->filters[$class];    
    }
}


/**
 * Base class for filters.
*/
class filter_base
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
