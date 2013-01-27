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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Error extends \HUGnet\base\SystemTableBase
{
    /** @var notice level severity */
    const SEVERITY_NOTICE = 1;
    /** @var warning level severity */
    const SEVERITY_WARNING = 2;
    /** @var error level severity */
    const SEVERITY_ERROR = 4;
    /** @var critical level severity */
    const SEVERITY_CRITICAL = 8;
    /** @var notice level severity */
    const EXCEPTION_OBJ_NOT_CONFIG = -99;
    /**
    * Logs an error in the database
    *
    * @param mixed  $errno    The error number.  Could be a string or number
    * @param string $errmsg   The error message
    * @param string $severity The severity of the message
    * @param string $method   Should be filled with __METHOD__
    * @param string $class    The classs calling the error
    *
    * @return null
    */
    public function log($errno, $errmsg, $severity, $method, $class)
    {
        $this->table()->fromAny(
            array(
                "class"    => $class,
                "method"   => $method,
                "errno"    => $errno,
                "error"    => $errmsg,
                "Date"     => $this->system()->time(),
                "Severity" => $severity,
            )
        );
        return $this->table()->insertRow(true);
    }

}


?>
