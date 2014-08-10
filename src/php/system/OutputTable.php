<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class OutputTable extends \HUGnet\base\XTableBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** @var int The database table class to use */
    protected $tableClass = "OutputTable";
    /** This is the table we are using */
    protected $xTable = "OutputTable";
    /** This is the type of tables we have available */
    protected $types = array(
        "ADuCDAC" => "0039-37 HUGnetLab Endpoint DAC",
        "ADuCPWM" => "0039-37 HUGnetLab Endpoint PWM",
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
            $dbtable = "OutputTable";
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
        $dir = dirname(__FILE__)."/../devices/outputTable/tables/";
        $namespace = "\\HUGnet\\devices\\outputTable\\tables\\";
        $arch = $this->table()->get("arch");
        switch ($arch) {
        case "ADuCPWM":
            include_once $dir."ADuCPWM.php";
            $class = $namespace."ADuCPWM";
            break;
        default:
            include_once $dir."ADuCDAC.php";
            $class = $namespace."ADuCDAC";
            break;
        }
        return $class;
    }

}


?>
