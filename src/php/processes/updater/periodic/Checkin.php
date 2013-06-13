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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Checkin extends \HUGnet\processes\updater\Periodic
{
    /** This is the period */
    protected $period = 600;
    /** This is the object we use */
    private $_datacollector;
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
        $this->system()->out("Updating the data collector record...");
        $device = $this->system()->device(
            $this->system()->network()->device()->getID()
        );
        $this->_datacollector->load($device);
        if (function_exists("posix_uname")) {
            $uname = posix_uname();
            $this->_datacollector->set("name", trim($uname['nodename']));
        }
        $this->_datacollector->store(true);
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
            $this->_datacollector->load(
                array("uuid" => $this->system()->get("uuid"))
            );

            if ($this->hasMaster()) {
                $this->system()->out(
                    "Pushing datacollector to the master server..."
                );
                $ret = $this->_datacollector->action()->post();
                if (is_array($ret)
                    && ($ret["uuid"] === $this->system()->get("uuid"))
                ) {
                    $this->system()->out("Checking in with the master server...");
                    $ret = $this->_datacollector->action()->checkin();
                    if ((bool)$ret) {
                        $this->success();
                    } else {
                        $this->failure();
                    }
                }
            } else {
                $this->success();
            }
        }
    }
}


?>
