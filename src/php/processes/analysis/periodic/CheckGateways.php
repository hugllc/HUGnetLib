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
namespace HUGnet\processes\analysis\periodic;
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
class CheckGateways extends \HUGnet\processes\analysis\Periodic
{
    /** This is the period */
    protected $period = 600;
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
        $this->_datacollector = $this->system()->datacollector();
        $this->_gateway = $this->system()->gateway();
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
            $this->system()->out("Checking Gateways");
            $ids = $this->_datacollector->ids();
            foreach ($ids as $id) {
                $this->_datacollector->load($id);
                $last = $this->_datacollector->get("LastContact");
                // The gateway has to have checked in during the last period
                if ($last > ($this->system()->now() - $this->period)) {
                    $this->_gateway->load(
                        (int)$this->_datacollector->get("GatewayKey")
                    );
                    $name = $this->_gateway->get("name");
                    if (empty($name)) {
                        $this->_gateway->set(
                            "name",
                            $this->_datacollector->get("name")
                        );
                    }
                    if ($this->_gateway->table()->insert(true)) {
                        $this->system()->out(
                            "Updated Gateway ".$this->_gateway->id()
                            .": ".$this->_gateway->get("name")
                        );
                    }
                }
            }
            $this->success();
        }
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    private function _updateDC()
    {
        $this->system()->out("Updating the data collector record...");
        $device = $this->system()->device(
            $this->system()->network()->device()->getID()
        );
        $this->_datacollector->load($device);
        if (function_exists("posix_uname")) {
            $uname = posix_uname();
            $this->_datacollector->set("name", trim($uname['nodename']));
        }
        $this->_datacollector->set("LastContact", $this->system()->now());
        $this->_datacollector->store(true);
        $this->_datacollector->load(
            array("uuid" => $this->system()->get("uuid"))
        );
    }
}


?>
