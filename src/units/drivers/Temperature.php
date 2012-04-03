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
 * @subpackage PluginsUnits
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\units\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsUnits
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Temperature extends \HUGnet\units\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array("&#176;F", "&#176;C", "K");
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to, $from, $type)
    {
        if (($from == '&#176;C') && ($to == '&#176;F')) {
            $this->cToF($data, $type);
        } else if (($from == '&#176;C') && ($to == 'K')) {
            $this->cToK($data, $type);
        } else if (($from == '&#176;F') && ($to == '&#176;C')) {
            $this->fToC($data, $type);
        } else {
            return parent::convert($data, $to, $from, $type);
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
    * @param float  &$data The temperature to convert
    * @param string $type  The type of data (raw or differential)
    *
    * @return null
    */
    protected function cToF(&$data, $type)
    {
        $data = ((9*$data)/5);
        if ($type != \HUGnet\units\Driver::TYPE_DIFF) {
            $data += 32.0;
        }
        $data = (float)$data;
    }
    /**
    * Converts from &#176; C to &#176; F.
    *
    * If the temperature is differential we can't add 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @param float  &$data The temperature to convert
    * @param string $type  The type of data (raw or differential)
    *
    * @return null
    */
    protected function cToK(&$data, $type)
    {
        if ($type != \HUGnet\units\Driver::TYPE_DIFF) {
            $data += 273.15;
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
    * @param float  &$data The temperature to convert
    * @param string $type  The type of data (raw or differential)
    *
    * @return null
    */
    protected function fToC(&$data, $type)
    {
        if ($type != \HUGnet\units\Driver::TYPE_DIFF) {
            $data -= 32.0;
        }
        $data = (float)((5/9)*$data);
    }
}
?>
