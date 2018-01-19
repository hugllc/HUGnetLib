<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This is our base class */
require_once dirname(__FILE__)."/DummyBase.php";
/** This is our base class */
require_once dirname(__FILE__)."/DummyNetwork.php";
/** This is our base class */
require_once dirname(__FILE__)."/DummyTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DummySystem extends DummyBase
{
    /** @var This is our returns */
    protected $class = "System";
    /**
    * This sets the configuration array _config
    *
    * @return null
    */
    public function &network()
    {
        $ret = new \HUGnet\network\DummyNetwork("Network");
        return $ret;
    }
    /**
    * This sets the configuration array _config
    *
    * @param string $table The table to use
    *
    * @return null
    */
    public function &table($table)
    {
        $ret = parent::table($table);
        if (!is_object($ret)) {
            $ret = new \HUGnet\DummyTable($table);
        }
        return $ret;
    }
    /**
    * Throws an exception
    *
    * @param string $msg       The message
    * @param bool   $condition If true the exception is thrown.  On false it
    *                 is ignored.
    *
    * @return null
    */
    public function fatalError($msg, $condition = true)
    {
        parent::fatalError($msg, $condition);
        if (!(boolean)$condition) {
            return false;
        }
        throw new \RuntimeException($msg);
    }


}
?>
