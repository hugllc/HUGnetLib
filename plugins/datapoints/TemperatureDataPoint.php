<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/DataPointBase.php";
/**
* This class implements photo sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Units
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TemperatureDataPoint extends DataPointBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Temperature",
        "Type" => "units",
        "Class" => "TemperatureDataPoint",
        "Flags" => array('Temperature'),
    );

    /** @var This is the preferred unit to display */
    protected $preferred = '&#176;F';

    /**
    * Does the actual conversion
    *
    * @param string $units The units to convert to
    *
    * @return null
    */
    public function convertTo($units)
    {
        if (empty($units)) {
            $units = $this->preferred;
        }
        if (($this->units == '&#176;C') && ($units == '&#176;F')) {
            $this->cToF();
        } else if (($this->units == '&#176;F') && ($units == '&#176;C')) {
            $this->fToC();
        } else {
            return parent::convertTo($units);
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
    * @return null
    */
    protected function cToF()
    {
        $F = ((9*$this->value)/5);
        if ($this->type != DataPointBase::TYPE_DIFF) {
            $F += 32;
        }
        $this->value = (float)$F;
        $this->units = '&#176;F';
    }

    /**
    *  Converts from &#176; F to &#176; C.
    *
    * If the temperature is differential we can't subtract 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @return null
    */
    protected function fToC()
    {
        $f = $this->value;
        if ($this->type != DataPointBase::TYPE_DIFF) {
            $f -= 32;
        }
        $this->value = (float)((5/9)*$f);
        $this->units = '&#176;C';
    }

}

?>
