<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/ProcessBase.php";
require_once dirname(__FILE__)."/../base/PeriodicPluginBase.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/PacketContainer.php";
require_once dirname(__FILE__)."/../interfaces/PacketConsumerInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PeriodicPlugins extends ProcessBase
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "group"      => "default",          // The groups to route between
        "GatewayKey" => 0,                  // The gateway key we are using
        "PluginDir"       => "./plugins",  // This is the plugin path
        "PluginExtension" => "php",
        "PluginType"      => "periodic",
    );
    /** @var array Array of objects that are our plugins */
    protected $active = array();
    /** @var object This is where our plugin object resides */
    protected $myPlugins = array();

    /**
    * Builds the class
    *
    * @param array $data   The data to build the class with
    * @param array $device This is the setup for my device class
    *
    * @return null
    */
    public function __construct($data, $device)
    {
        parent::__construct($data, $device);
        $this->registerHooks();
        $this->requireGateway();
        $this->_registerPlugins();
    }
    /**
    * This function gets setup information from all of the devices
    *
    * This function should be called periodically as often as possible.  It will
    * check all plugins before returning
    *
    * @return null
    */
    private function _registerPlugins()
    {
        $this->active = array();
        $this->myPlugins = new PluginsContainer(
            array(
                "dir" => $this->PluginDir,
                "extension" => $this->PluginExtension,
            )
        );
        $classes = $this->myPlugins->getPlugin($this->PluginType);
        $data = array(
            "verbose" => $this->verbose,
        );
        foreach ((array)$classes as $class) {
            $c = $class["Class"];
            if (is_subclass_of($c, "PeriodicPluginInterface")) {
                $this->active[$class["Name"]] = new $c($data, $this);
            }
        }
    }
    /**
    * This function gets setup information from all of the devices
    *
    * This function should be called periodically as often as possible.  It will
    * check all plugins before returning
    *
    * @return null
    */
    public function main()
    {
        foreach (array_keys((array)$this->active) as $key) {
            if ($this->active[$key]->ready()) {
                $this->active[$key]->main();
            }
        }
    }
    /**
    * Wrapper to send out an email
    *
    * This wrapper is just for testing purposes, so I can isolate the call to 'mail'
    *
    * @param string $subject The subject of the message
    * @param string $message The actual message
    *
    * @return mixed Array in test mode, bool in normal mode
    */
    public function mail($subject, $message)
    {
        if (empty($this->myConfig->admin_email)) {
            return false;
        }
        $additional_headers    = "";
        $additional_params = "";
        // Do a test if we are in test mode.
        if ($this->myConfig->test) {
            return array(
                $this->myConfig->admin_email,
                $subject,
                $message,
                $additional_headers,
                $additional_params
            );
        }
        // @codeCoverageIgnoreStart
        // Can't test this call
        return mail(
            $this->myConfig->admin_email,
            $subject,
            $message,
            $additional_headers,
            $additional_params
        );
        // @codeCoverageIgnoreEnd
    }
}
?>
