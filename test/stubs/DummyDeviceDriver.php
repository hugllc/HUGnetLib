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
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This is our base class */
require_once dirname(__FILE__)."/DummyBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DummyDeviceDriver extends \HUGnet\DummyBase
{
    /** @var This is our returns */
    protected $class = "DummyDeviceDriver";
    /**
    * Creates the object
    *
    * @param string $name The object name
    *
    * @return null
    */
    public function &factory($name)
    {
        return new DummyDeviceDriver($name);
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function config()
    {
        $ret = parent::__call("config", func_get_args());
        return $ret;
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function poll()
    {
        $ret = parent::__call("poll", func_get_args());
        return $ret;
    }

    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function sensorConfig()
    {
        $ret = parent::__call("sensorConfig", func_get_args());
        return $ret;
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function setSensorConfig()
    {
        $ret = parent::__call("setSensorConfig", func_get_args());
        return $ret;
    }


}
?>
