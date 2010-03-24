<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * Base class for all other classes
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetClass
{
    /** @var int The verbosity level */
    public $verbose = 0;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param mixed $config The configuration array
    *
    * @return null
    */
    public function __construct($config = array())
    {
        $this->verbose($config["verbose"]);
    }

    /**
    * Sets the verbosity
    *
    * @param int $level The verbosity level
    *
    * @return null
    */
    public function verbose($level=0)
    {
        $level = (int) $level;
        $this->verbose = $level;
    }
    /**
    * Prints out a string
    *
    * @param string $str     The string to print out
    * @param int    $val     The minimum value to print this for
    * @param int    $verbose The verbosity level
    *                        (This is for if we are not an object)
    *
    * @return null
    */
    public function vprint($str, $val = 6, $verbose = 0)
    {
        if (is_object($this) && empty($verbose)) {
            $verbose = $this->verbose;
        }
        if (($verbose < $val) || empty($str)) {
            return;
        }
        if (is_object($this)) {
            $class  = get_class($this);
            print "(".$class.") ";
        }
        print $str."\n";
    }
}


?>
