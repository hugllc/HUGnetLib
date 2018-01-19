<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/Table.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class TableParams extends \HUGnet\db\Table
{
    /** @var array These are the params that will be set */
    protected $setParams = array(
    );
    /**
    * Overload the set attribute
    *
    * @param string $name  This is the attribute to set
    * @param mixed  $value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->default)) {
            $ret = parent::set($name, $value);
        } else {
            $this->sParam($name, $value);
        }
        return $ret;
    }
    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if (is_null($ret)) {
            $ret = $this->gParam($name);
        }
        return $ret;
    }
    /**
    * Overload the set attribute
    *
    * @param string $name  This is the attribute to set
    * @param mixed  $value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    public function sParam($name, $value)
    {
        $ret = null;
        if (in_array($name, $this->setParams) || empty($this->setParams)) {
            $this->_fixExtra($name, $value);
            $array = (array)json_decode(parent::get("params"), true);
            $ret = $array[$name] = $value;
            parent::set("params", $array);
        }
        return $ret;
    }
    /**
    * Overload the set attribute
    *
    * @param string $name   This is the attribute to set
    * @param mixed  &$value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    private function _fixExtra($name, &$value)
    {
        if ($name === "extra") {
            foreach ((array)$value as $k => $v) {
                if ($v === "") {
                    unset($value[$k]);
                }
            }
        }

    }

    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    protected function gParam($name)
    {
        $array = (array)json_decode(parent::get("params"), true);
        return $array[$name];
    }
    /**
    * Overload the get attribute
    *
    * @return null
    */
    public function cleanParams()
    {
        if (empty($this->setParams)) {
            return;
        }
        $old = json_decode($this->get("params"), true);
        $set = array();
        foreach ($this->setParams as $key) {
            if (isset($old[$key])) {
                $set[$key] = $old[$key];
            }
        }
        $this->set("params", $set);
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
        parent::fromArray($array);
        if (!isset($array["params"]) || !is_string($array["params"])) {
            foreach ($this->getProperties() as $key) {
                unset($array[$key]);
            }
            $this->cleanParams();
            foreach ((array)$array as $key => $value) {
                $this->sParam($key, $value);
            }
        }
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
        $data = parent::toArray($default);
        $params = json_decode($data["params"], true);
        unset($data["params"]);
        return array_merge((array)$params, (array)$data);
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setParams($value)
    {
        if (is_array($value)) {
            $this->data["params"] = json_encode($value);
        } else if (is_string($value)) {
            $this->data["params"] = $value;
        }
    }

}
?>
