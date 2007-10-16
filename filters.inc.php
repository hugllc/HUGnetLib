<?php
/**
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
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Filters
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id: unitConversion.inc.php 369 2007-10-12 15:05:32Z prices $    
 *
 */

class filter {

	function __construct(&$plugins = "") {
		if (!is_object($plugins)) {
			if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
			$plugins = new plugins(dirname(__FILE__)."/plugins/", "inc.php");
		}

		foreach($plugins->plugins["Generic"]["filter"] as $driver) {
			if (class_exists($driver["Class"])) {
				$class = $driver["Class"];
				$this->filters[$class] = new $class();
				if (is_array($this->filters[$class]->filters)) {
					foreach($this->filters[$class]->filters as $type => $sInfo) {
						foreach($sInfo as $filter => $val) {
							$this->filter[$type][$filter] = $class;
						}
						if (!isset($this->filter[$type]['default'])) $this->dev[$type]['default'] = $class;
					}
				}
			}
		}
	}

	/**
		@public
		@brief Return the voltage
		@param $R Float The current resistance of the thermistor
		@param $type Int The type of filter.
		@return filter value	
	
		@par Introduction
		This function 

	
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
        Returns the class
        $return is the default sent to it.
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
        Returns the class
    */
    function &getClass($type, &$filter) {
        $class = $this->dev[$type][$filter];
        if (is_null($class)) {
            if (is_array($this->dev[$type])) {
                reset($this->dev[$type]);
                $filter = key($this->dev[$type]);
                $class = current($this->dev[$type]);
            }            
//            $class = $this->dev[$type]['default'];
        }
        return $this->filters[$class];    
    }
}


/**
	@brief Base class for filters.
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
