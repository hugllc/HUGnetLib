<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/XTableBase.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class InputTable extends \HUGnet\base\XTableBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** This is the table we are using */
    protected $xTable = "InputTable";
    /** This is the type of tables we have available */
    protected $types = array(
        "0039-37" => "0039-37 ADuC HUGnetLab Endpoint Analog",
        "0039-12" => "0039-12 AVR Endpoint Analog",
        "0039-21-01" => "0039-21-01 Old Controller Board Analog",
        "0039-21-02" => "0039-21-02 Controller Board Analog",
        "0039-28" => "0039-28 Enhanced AVR Endpoint Analog",
    );
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $dbtable The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $dbtable=null)
    {
        if (empty($dbtable)) {
            $dbtable = "InputTable";
        }
        $object = parent::factory($system, $data, $dbtable);
        return $object;
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    protected function entryDriver()
    {
        $dir = dirname(__FILE__)."/../devices/inputTable/tables/";
        $namespace = "\\HUGnet\\devices\\inputTable\\tables\\";
        $arch = $this->table()->get("arch");
        switch ($arch) {
        case "0039-12":
            include_once $dir."E003912AnalogTable.php";
            $class = $namespace."E003912AnalogTable";
            break;
        case "0039-21-01":
            include_once $dir."E00392101AnalogTable.php";
            $class = $namespace."E00392101AnalogTable";
            break;
        case "0039-21-02":
            include_once $dir."E00392102AnalogTable.php";
            $class = $namespace."E00392102AnalogTable";
            break;
        case "0039-28":
            include_once $dir."E003928AnalogTable.php";
            $class = $namespace."E003928AnalogTable";
            break;
        case "0039-37":
            include_once $dir."ADuCInputTable.php";
            $class = $namespace."ADuCInputTable";
            break;
        default:
            include_once $dir."AVRAnalogTable.php";
            $class = $namespace."AVRAnalogTable";
            break;
        }
        return $class;
    }

}


?>
