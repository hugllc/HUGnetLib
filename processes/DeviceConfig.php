<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/PacketContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceConfig extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "group"      => "default",  // The groups to route between
        "GatewayKey" => 0,
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is our config */
    protected $myConfig = null;
    /** @var object This is the devices we are going through */
    protected $device = null;
    /** @var array We store our routes here */
    protected $Routes = array();

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        // Clear the data
        $this->clearData();
        // This is our config
        $this->myConfig = &ConfigContainer::singleton();
        // Run the parent stuff
        parent::__construct($data);
        // Set the gatewaykey if it hasn't been set
        if (empty($this->GatewayKey)) {
            $this->GatewayKey = $this->myConfig->script_gateway;
        }
        // Set the verbosity
        $this->verbose($this->myConfig->verbose);
        // This is our device container
        $this->device = new DeviceContainer();
        // We need a GatewayKey
        if ($this->GatewayKey <= 0) {
            $this->throwException(
                "A GatewayKey must be specified.", -3
            );
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  The above function never returns.
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * This function gets setup information from all of the devices
    *
    * This function should be called periodically as often as possible.  It will
    * go through the whole list of devices before returning.
    *
    * @param DeviceContainer &$device This is the class to send packets to me to.
    *
    * @return int The number of packets routed
    */
    public function config(DeviceContainer &$device)
    {
        $ret = $this->device->selectInto(1);
        while ($ret) {
            if ($this->GatewayKey == $this->device->GatewayKey) {
                $this->device->readConfig();
            }
            $ret = $this->device->nextInto();
        }
    }
    /**
    * This function sends a powerup packet
    *
    * @return null
    */
    public function powerup()
    {
        PacketContainer::powerup("", $this->group);
    }

}
?>
