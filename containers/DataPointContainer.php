<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataPointContainer extends HUGnetClass
{
    /** @var The sensor we are attached to */
    public $value = null;
    /** @var The sensor we are attached to */
    private $_sensor = null;
    /** @var The sensor we are attached to */
    public $row = null;

    /**
    * Disconnects from the database
    *
    * @param object &$row  A reference to the object that is creating us
    * @param mixed  $value The current value of the data
    *
    * @return null
    */
    public function __construct(&$row, $value = null)
    {
        $this->sensor  = &$row->device->sensor[$sensor];
        $this->row     = &$row;
        $this->value   = $value;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
    }

    /**
    * Creates a sensor from data given
    *
    * @param object &$row  A reference to the object that is creating us
    * @param mixed  $value The current value of the data
    *
    * @return Reference to the sensor on success, null on failure
    */
    public static function &factory(&$row, $value = null)
    {
        if (is_null($value)) {
            return null;
        }
        $class = __CLASS__;
        $data = new $class($row, $value);
        return $data;
    }

    /**
    * returns a string
    *
    * @return Reference to the sensor on success, null on failure
    */
    public function __toString()
    {
        return (string)$this->value;
    }

}
?>
