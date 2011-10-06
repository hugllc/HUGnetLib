<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TemperatureUnits extends UnitsBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Temperature",
        "Type" => "Units",
        "Class" => "TemperatureUnits",
        "Flags" => array('Temperature'),
    );
    /** @var The units of this point */
    public $to = "&#176;F";
    /** @var The type of this point */
    public $from = "&#176;C";
    /** @var The units that are valid for conversion */
    protected $valid = array("&#176;F", "&#176;C");

    /**
    * Does the actual conversion
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
        if (($this->from == '&#176;C') && ($this->to == '&#176;F')) {
            $this->cToF($data);
        } else if (($this->from == '&#176;F') && ($this->to == '&#176;C')) {
            $this->fToC($data);
        } else {
            return $ret;
        }
        return true;
    }

    /**
    * Converts from &#176; C to &#176; F.
    *
    * If the temperature is differential we can't add 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @param float &$data The temperature to convert
    *
    * @return null
    */
    protected function cToF(&$data)
    {
        $data = ((9*$data)/5);
        if ($this->type != UnitsBase::TYPE_DIFF) {
            $data += 32.0;
        }
        $data = (float)$data;
    }

    /**
    *  Converts from &#176; F to &#176; C.
    *
    * If the temperature is differential we can't subtract 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @param float &$data The temperature to convert
    *
    * @return null
    */
    protected function fToC(&$data)
    {
        if ($this->type != UnitsBase::TYPE_DIFF) {
            $data -= 32;
        }
        $data = (float)((5/9)*$data);
    }

}

?>
