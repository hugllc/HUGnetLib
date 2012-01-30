<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/ConfigContainer.php";
require_once dirname(__FILE__)."/ImagePointContainer.php";

/**
 * This is a generic, extensible container class
 *
 * Classes can be added in so that their methods and properties can be used
 * by this class and the reverse of that.  There can be a whole linked list
 * of containers that extend eachother.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ImageContainer extends HUGnetContainer
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => 0,
        "name" => "image",
        "type" => "DEFAULT",
        "iterate" => true,
        "imageLoc" => "",
        "height" => 0,
        "width" => 0,
        "pointCount" => 0,
        "description" => "",
        "dataType" => "15MIN",
    );
    /** @var object The data container class */
    protected $points = array();

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        $this->myConfig = &ConfigContainer::singleton();
        parent::__construct($data);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        foreach (array_keys((array)$array["points"]) as $key) {
            $this->addPoint($array["points"][$key]);
            unset($array["points"][$key]);
        }
        return parent::fromArray($array);
    }

    /**
    * This is the constructor
    *
    * @param object &$container the container to use for data
    *
    * @return none
    */
    public function addPoint(&$container)
    {
        $this->data["points"] =& $this->points;
        $this->pointCount = (int)count((array)$this->points);
        if (is_a($container, "ImagePointContainer")) {
            $this->points[$this->pointCount] = &$container;
        } else if (is_array($container)) {
            $this->points[$this->pointCount] = new ImagePointContainer($container);
        } else {
            return false;
        }
        $this->points[$this->pointCount]->id = $this->pointCount;
        return $this->pointCount++;
    }
    /**
    * This is the constructor
    *
    * @param int $index The index of the container to use
    *
    * @return none
    */
    public function point($index)
    {
        return $this->points[$index];
    }

    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toString($default = true)
    {
        $class = $this->getPlugin();
        $this->throwException("No default 'image' plugin found", -6, empty($class));
        $out  = new $class($this);
        return $out->output();
    }
    /**
    * Creates a sensor object
    *
    * @return string The class for this sensor
    */
    protected function getPlugin()
    {
        $driver = $this->myConfig->plugins->getPlugin(
            "image", $this->type
        );
        return $driver["Class"];
    }
}
?>
