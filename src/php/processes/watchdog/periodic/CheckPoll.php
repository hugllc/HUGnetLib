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
namespace HUGnet\processes\watchdog\periodic;
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
class CheckPoll extends \HUGnet\processes\watchdog\Periodic
{
    /** This is the period */
    protected $period = 600;
    /** This is the period */
    protected $oldest = 600;
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
        $this->oldest = (int)$this->ui()->get("max_history_age");
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
        if ($this->ready() && $this->system()->dbconnect()->available()) {
            $device = $this->system()->device();
            $ids = $device->ids(array("Active" => 1));
            $oldest = $this->system()->now() - $this->oldest;
            foreach (array_keys((array)$ids) as $key) {
                if (!$this->ui()->loop()) {
                    break;
                }
                $device->load($key);
                if ($device->get("PollInterval") > 0) {
                    $lastHistory = $device->getParam("LastHistoryPush");
                    if ($lastHistory < $oldest) {
                        $this->ui()->criticalError(
                            "CheckHistory".$key,
                            "Device ".sprintf("%06X", $key)." hasn't shown history"
                            ." since ".date("Y-m-d H:i:s", $lastHistory)
                        );
                    } else {
                        $this->ui()->clearError("CheckHistory".$key);
                    }
                }
            }

            $this->last = $this->ui()->system()->now();
        }
    }
}


?>
