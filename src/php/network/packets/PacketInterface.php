<?php
/**
 * Tests the filter class
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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\network\packets;
/**
 * Interface for device drivers
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface PacketInterface
{
    /**
    * Creates the object
    *
    * @param mixed $data The array of data to use
    *
    * @return null
    */
    public function &factory($data = array());
    /**
    * Checks for a given key
    *
    * @return null
    */
    public function __toString();
    /**
    * Checks to see if this packet is valid
    *
    * @return bool True if the packet is valid, false otherwise
    */
    public function isValid();
    /**
    * Sets and/or returns the from
    *
    * @param mixed $value The value to set this to.
    *
    * @return string Returns the value it is set to
    */
    public function extra($value = null);
    /**
    * Returns the packet reply data if there is any
    *
    * @param mixed $value The value to set this to.
    * @param bool  $raw   Return the raw data as an array if true
    *
    * @return null, true or false.  null if no return yet, true if positive ack,
    *                       false if negative ack
    */
    public function reply($value = null, $raw = false);
    /**
    * Return a modified configuration array
    *
    * @param array $config The configuration array to start with
    *
    * @return array The modified confiruation array
    */
    public function config($config = array());

}