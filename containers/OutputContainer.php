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
        $this->container = $container;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        if (!is_a($this->container, "OutputInterface")) {
            return array();
        }
        return $this->container->toOutput();
    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    private function _getData()
    {
        if (empty($this->out)) {
            do {
                $this->out[] = $this->toArray();
            } while ($this->iterate && $this->container->nextInto());
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
    * Returns the object as a string
    *
    * @param string $type   The type of output to get
    * @param array  $params The parameters to use
    *
    * @return string
    */
    public function getOutput($type, $params = array())
    {
        if (!is_a($this->container, "OutputInterface")) {
            return "";
        }
        $class = $this->getPlugin($type);
        $this->throwException("No default 'output' plugin found", -6, empty($class));
        $p = array_merge(
            $this->container->outputParams($type),
            $params
        );
        $out  = new $class($p);
        $out->header($this->container->toOutputHeader());
        $this->_getData();
        foreach ((array)$this->out as $o) {
            $out->row($o);
        }
        return $out->toString();
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
}
?>
