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
require_once dirname(__FILE__)."/../../base/UnitBase.php";
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
class CapacitanceUnits extends unitBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Capacitance",
        "Type" => "units",
    );
    /**
    *  This is the array that defines all of our units and how to
    * display and use them.
    *  @var array
    *
    */
    var $units = array(
        'F' => array(
            'longName' => 'Farad',
            'varType' => 'float',
        ),
        'uF' => array(
            'longName' => 'Microfarad',
            'varType' => 'float',
        ),
        'nF' => array(
            'longName' => 'Nanofarad',
            'varType' => 'float',
        ),
    );


}


?>
