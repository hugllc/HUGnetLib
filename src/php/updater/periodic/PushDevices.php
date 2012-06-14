<?php
/**
 * Classes for dealing with devices
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\updater\periodic;
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PushDevices extends \HUGnet\updater\Periodic
{
    /** This is the period */
    protected $period = 60;
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
        $this->_device = $this->system()->device();
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
        if ($this->ready() && $this->hasMaster()) {
            $now = time();
            $ids = $this->_device->ids();
            $mem = memory_get_usage();
            foreach ($ids as $key => $devID) {
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }

                $this->_device->load($key);
                /* Let's just push the regular devices */
                if ($key >= 0xFE0000) {
                    continue;
                }
                $lastContact = $this->_device->getParam("LastContact");
                /* Only push it if we have changed it since the last push */
                if ($lastContact < $this->_device->getParam("LastMasterPush")) {
                    continue;
                }
                $this->ui()->out(
                    "Pushing ".sprintf("%06X", $devID)." to master server..."
                );
                $this->_device->setParam("LastMasterPush", $now);
                $ret = $this->_device->action()->post($url);
                $sens = $this->_device->get("totalSensors");
                $sensors = array();
                for ($i = 0; $i < $sens; $i++) {
                    //$this->ui()->out("Pushing sensor ".$i);
                    $this->_device->sensor($i)->action()->post($url);
                    $this->system()->main();
                    if (!$this->ui()->loop()) {
                        break;
                    }
                }
                if ($ret === "success") {
                    $this->ui()->out(
                        "Successfully pushed ".sprintf("%06X", $devID)."."
                    );
                    $this->_device->store();
                } else {
                    $this->ui()->out("Failure.");
                    /* Don't store it if we fail */
                }
            }
            $this->last = $now;
            print "Memory: ".((memory_get_usage()) / 1024.0 / 1024.0)."M".PHP_EOL;
        }
    }
}


?>
