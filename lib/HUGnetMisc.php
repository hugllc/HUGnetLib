<?php
/**
 * This class is for misc functions that don't fit anywhere else
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
 * Copyright (C) 2002-2009 Scott Price
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Lib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2002-2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * This class is for misc functions that don't fit anywhere else
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Lib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2002-2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetMisc
{
    /**
    * Gets the ip address, netmask and broadcast address
    *
    * The array returned has the following:
    * - <b>inet addr</b> The internet address
    * - <b>mask</b> The netmask
    * - <b>bcast</b> The broadcast address
    *
    * @return array
    */
    public function getNetInfo()
    {
        //@codeCoverageIgnoreStart
        // This is not testable because it doesn't work on all systems.
        // I know this works on Linux
        $Info = trim(`/sbin/ifconfig|grep Bcast`);
        $Info = explode("  ", $Info);
        foreach ($Info as $key => $val) {
            if (!empty($val)) {
                $t = explode(":", $val);
                $netInfo[trim($t[0])] = trim($t[1]);
            }
        }
        $netInfo = array_change_key_case($netInfo, CASE_LOWER);
        return $netInfo;
        //@codeCoverageIgnoreEnd
    }
    /**
    * Changes seconds into YDHMS
    *
    * This is for uptime displays.
    *
    * @param float $seconds The number of seconds
    * @param int   $digits  The number of digits after the decimal point in the
    *                       returned seconds
    *
    * @return string The number of years, days, hours, minutes, and seconds in the
    *                original number of seconds.
    */
    function getYdhms ($seconds, $digits=0)
    {
        $seconds  = (float) $seconds;
        $years    = (int)($seconds/60/60/24/365.25);
        $seconds -= $years*60*60*24*365.25;
        $days     = (int)($seconds/60/60/24);
        $seconds -= $days*60*60*24;
        $hours    = (int)($seconds/60/60);
        $seconds -= $hours*60*60;
        $minutes  = (int)($seconds/60);
        $seconds -= $minutes*60;
        $seconds  = number_format($seconds, $digits);

        $return = "";
        if ($years > 0) {
            $return .= $years."Y ";
        }
        if ($days > 0) {
            $return .= $days."d ";
        }
        if ($hours > 0) {
            $return .= $hours."h ";
        }
        if ($minutes > 0) {
            $return .= $minutes."m ";
        }
        $return .= $seconds."s";
        return $return;
    }

    /**
    * Changes number of bytes into human readable numbers using K, M, G, T, Etc
    *
    * @param int $bytes  The original number of bytes
    * @param int $digits The number places to the right of the decimal point
    *                    to show
    *
    * @return String The number of bytes human readable.
    */
    function getBytes($bytes, $digits=2)
    {

        $bytes  = (int) $bytes;
        $labels = array("", " k", " M", " G", " T", " P");

        $index == 0;
        while ($bytes > 1024) {
                $bytes = $bytes/1024;
                $index ++;
        }
        $bytes  = number_format($bytes, $digits);
        $bytes .= $labels[$index]." bytes";
        return $bytes;
    }


}

?>