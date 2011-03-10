<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";

/**
 * This is a generic, extensible container class
 *
 * Classes can be added in so that their methods and properties can be used
 * by this class and the reverse of that.  There can be a whole linked list
 * of containers that extend eachother.
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class OutputContainer extends HUGnetContainer
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "type" => "DEFAULT",
        "iterate" => true,
        "params" => array(),
    );
    /** @var object The data container class */
    public $container = null;
    /** @var array of output rows */
    public $out = array();
    /** @var object The data container class */
    protected $callbacks = array();
    /** @var array Our data to display */
    protected $dataOut = array();
    /** @var array Our header information */
    protected $headerOut = array();
    /** @var array parameters */
    protected $paramsOut = array();
    
    
    /**
    * This is the constructor
    *
    * @param mixed  $data       This is an array or string to create the object from
    * @param object &$container the container to use for data
    */
    function __construct($data="", &$container=null)
    {
        $this->setContainer($container);
        // Setup our configuration
        $this->myConfig = &ConfigContainer::singleton();
        parent::__construct($data);
    }
    /**
    * This is the constructor
    *
    * @param object &$container the container to use for data
    * 
    * @return none
    */
    public function setContainer(&$container)
    {
        $this->container = &$container;
    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    public function preloadData()
    {
        if (empty($this->dataOut)) {
            $iterate = is_a($this->container, "IteratorInterface");
            do {
                $ret = $this->container->toOutput();
                $this->dataOut[] = $ret;
            } while ($iterate && $this->iterate && $this->container->nextInto());
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $cols  The columns to use in $field => $name format
    * @param bool  $force Force the header to rewrite
    *
    * @return null
    */
    public function header($cols = array(), $force = false)
    {
        if (!is_a($this->container, "OutputInterface")) {
            return;
        }
        if (empty($this->headerOut) || $force) {
            $cCols = $this->container->toOutputHeader();
            if (empty($cols)) {
                $this->headerOut = $cCols;
            } else {
                $this->headerOut = array();
                foreach ($cols as $col => $name) {
                    if (isset($cCols[$col]) && empty($name)) {
                        $name = $cCols[$col];
                    }
                    $this->headerOut[$col] = $name;
                }
            }
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param string $type   The type of output to get
    * @param array  $params The parameters to use
    *
    * @return null
    */
    public function params($type, $params = array())
    {
        if (!is_a($this->container, "OutputInterface")) {
            return;
        }
        if (empty($this->paramsOut[$type])) {
            $this->paramsOut[$type] = array_merge(
                $this->container->outputParams($type),
                (array)$params
            );
        } else if (is_array($this->paramsOut[$type])) {
            $this->paramsOut[$type] = array_merge(
                $this->paramsOut[$type], (array)$params
            );
        }
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = true)
    {
        return $this->getOutput($this->type, $this->params);
    }
    /**
    * Clears all of the data out of the object
    *
    * @return string
    */
    public function clearData()
    {
        $this->dataOut = array();
        $this->headerOut = array();
        $this->paramsOut = array();
        return parent::clearData();
    }

    /**
    * Returns the object as a string
    *
    * @param string $type   The type of output to get
    * @param array  $params The parameters to use
    * @param array  $cols   The columns to us in $field => $name format
    *
    * @return string
    */
    public function getOutput($type, $params = array(), $cols = array())
    {
        if (!is_a($this->container, "OutputInterface")) {
            return "Container doesn't implement OutputInterface";
        }
        $class = $this->getPlugin($type);
        $this->throwException("No default 'output' plugin found", -6, empty($class));
        $this->params($type, $params);
        $out = new $class($this->paramsOut[$type]);
        $this->header($cols, (is_array($cols) && !empty($cols)));
        $this->preloadData();
        $out->header($this->headerOut);
        foreach ((array)$this->dataOut as $o) {
            $this->doCallbacks($o, $type);
            // Set the row for output
            $out->row($o);
        }
        return $out->toString();
    }
    /**
    * Creates a sensor object
    *
    * @param array  &$data The data to use
    * @param string $type  The type of output to get
    *
    * @return string The class for this sensor
    */
    protected function doCallbacks(&$data, $type)
    {
        // Apply the call backs
        foreach (array_keys($this->callbacks) as $field) {
            if (array_key_exists($field, $this->headerOut)) {
                $data[$field] = call_user_func(
                    $this->callbacks[$field],
                    $field,
                    $data[$field],
                    &$this->container,
                    $type
                );
            }
        }

    }
    /**
    * Creates a sensor object
    *
    * @param string $type The type of output to get
    *
    * @return string The class for this sensor
    */
    protected function getPlugin($type)
    {
        $driver = $this->myConfig->plugins->getPlugin(
            "output", $type
        );
        return $driver["Class"];
    }
    /**
    * Creates a sensor object
    *
    * Function should have the following form:
    *    string myFunction ($field, &$container);
    * 
    *  The function should return whatever string that you want to be displayed
    *  in the field.
    * 
    * @param string   $field    The field to use the callback on
    * @param callback $function The callback for the function
    *
    * @return bool True if successful, false on failure
    */
    public function addFunction($field, $function)
    {
        if (is_callable($function)) {
            $this->callbacks[$field] = &$function;
            return true;
        }
        return false;
    }
}
?>
