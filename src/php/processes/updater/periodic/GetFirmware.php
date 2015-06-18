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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\updater\periodic;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class GetFirmware extends \HUGnet\processes\updater\Periodic
{
    /** This is the period */
    protected $period = 3600;
    /** This is the object we use */
    private $_firmware;
    /** This is the object we use */
    private $_fwPath = array();
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected function __construct(&$gui)
    {
        parent::__construct($gui);
        $this->_firmware = $this->system()->table("Firmware");
        $this->_fwPath = (array)$this->system()->get("firmware");
    }
    /**
    * This function creates the system.
    *
    * @param object &$gui the user interface object
    *
    * @return null
    */
    public static function &factory(&$gui)
    {
        return parent::intFactory($gui);
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    public function &execute()
    {
        if ($this->ready()) {
            foreach (array("url", "local") as $key) {
                if (strlen($this->_fwPath[$key]) == 0) {
                    continue;
                }
                // State we are looking for firmware
                $this->system()->out(
                    "Checking for new firmware at ".trim($this->_fwPath[$key])
                );
                $files = @file($this->_fwPath[$key]."/manifest");
                foreach ((array)$files as $file) {
                    if (!$this->ui()->loop()) {
                        return;
                    }
         
                    if (!$this->_firmware->checkFile($file)) {
                        // State we found some new firmware
                        $this->system()->out("Found ".trim($file));
                        // Load the firmware
                        $this->_firmware->fromFile($file, $this->_fwPath[$key]);
                        // Insert it.
                        $this->_firmware->insertRow(true);
                    }
                }
                $this->success();
            }
        }
    }
}


?>
