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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class SyncTables extends \HUGnet\processes\updater\Periodic
{
    /** This is the period (1 day) */
    protected $period = 86400;
    /** This is the object we use */
    private $_tables;
    /** This is the flag to run or not */
    private $_enable;
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
        $this->_enable = (bool)$this->ui()->get("sync_tables");

        $this->_tables["InputTable"] = $this->system()->InputTable();
        $this->_tables["OutputTable"] = $this->system()->OutputTable();
        $this->_tables["ProcessTable"] = $this->system()->ProcessTable();
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
        if ($this->ready() && $this->_enable) {
            $master = $this->system()->get("master");
            $murl = $master["url"];
            if (empty($murl)) {
                return;
            } else if (stripos("?", $murl) === false) {
                $murl .= "?";
            }
            foreach ($this->_tables as $table => $obj) {
                $url  = $murl."&task=".urlencode(strtolower(trim($table)));
                $url .= "&action=list";
                $this->system()->out(
                    "Syncing $table "
                );
                $ret = json_decode(@file_get_contents($url), true);
                if (is_array($ret)) {
                    $max = 0;
                    foreach ($ret as $key => $array) {
                        if (is_array($array)) {
                            $obj->table()->fromArray($array);
                            $obj->table()->insertRow(true);
                            if ($ret) {
                                $this->system()->out("Updated $key");
                            }
                            $id = $obj->table()->get("id");
                            if ($max < $id) {
                                $max = $id;
                            }
                        }
                    }
                    $obj->table()->delete(array("id" => array('$gt' => $max)));
                    $this->success();
                } else {
                    $this->failure();
                }
            }
        }
    }
}


?>
