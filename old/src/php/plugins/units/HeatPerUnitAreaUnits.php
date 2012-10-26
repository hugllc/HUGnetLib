<?php
/**
 * Sensor driver for light sensors.
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
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/UnitsBase.php";
/**
 * This class implements photo sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class HeatPerUnitAreaUnits extends UnitsBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Heat/Unit Area",
        "Type" => "Units",
        "Class" => "HeatPerUnitAreaUnits",
        "Flags" => array('Heat/Unit Area'),
    );
    /** @var The units of this point */
    public $to = "Btu/hr ft^2";
    /** @var The type of this point */
    public $from = "W/m^2";
    /** @var The units that are valid for conversion */
    protected $valid = array("W/m^2", "Btu/hr ft^2");

    /**
    * Does the actual conversion
    *
    * Information for conversion rates came from:
    *    http://metalpass.com/unit/unit8.aspx
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to=null, $from=null)
    {
        $ret = parent::convert($data, $to, $from);
        if (($this->from == 'W/m^2') && ($this->to == 'Btu/hr ft^2')) {
            $data = $data * 0.317;
        } else if (($this->from == 'Btu/hr ft^2') && ($this->to == 'W/m^2')) {
            $data = $data * 3.154;
        } else {
            return $ret;
        }
        return true;
    }
}

?>
