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
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/BaseChan.php";

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
class ControlChan extends \HUGnet\base\BaseChan
    implements \HUGnet\interfaces\WebAPI2
{
    /** @var array The configuration that we are going to use */
    protected $setable = array();
    /**
    * Returns the input object associated with this channel
    *
    * @return null
    */
    public function output()
    {
        return $this->device()->output($this->get("output"));
    }
    /**
    * returns a history object for this device
    *
    * @param object $api   The API object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI2($api, $extra)
    {
        $method = trim(strtoupper($api->args()->get("method")));
        $extra  = $api->args()->get("restextra");
        $ret = null;
        if (trim(strtolower($extra[0])) == "settable") {
            $ret = $this->_value($api);
        } else {
            $api->response(401);
            $c = get_class($api);
            $api->error($c::NOT_IMPLEMENTED);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $api The API object
    *
    * @return string
    */
    private function _value($api)
    {
        $data = null;
        if ($action == "get") {
            $pkt = $this->system()->device($did)->action()->send(
                array(
                    "Command" => '0x65',
                    "Data" => sprintf("%02X", $chan),
                )
            );
            if (is_object($pkt)) {
                $reply = $pkt->reply();
                $data = 0;
                for ($i = 0; $i < 4; $i++) {
                    $data += hexdec(substr($reply, ($i * 2), 2))<<($i * 8);
                }
                $api->response(202);
                return $data;
            } else {
                $api->response(401);
                $c = get_class($api);
                $api->pdoerror($c::NO_RESPONSE);
            }
        } else if ($action == "set") {
            $data = (int)$this->args()->get("data");
            $datastr = sprintf("%08X", $data);
            for ($i = 6; $i >= 0; $i-=2) {
                $value .= substr($datastr, $i, 2);
            }
            $pkt = $this->system()->device($did)->action()->send(
                array(
                    "Command" => '0x64',
                    "Data" => sprintf("%02X", $chan).$value,
                )
            );
            if ($pkt->reply() == $value) {
                return $data;
            }
        }
        if (!is_null($ret)) {
        } else {
        }

    }

}


?>
