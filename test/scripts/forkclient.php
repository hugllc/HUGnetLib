<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 * Copyright 2009 Scott Price
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
 * @subpackage Setup
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

set_time_limit(15);
declare(ticks = 1);
$exit = false;
pcntl_signal(
    SIGINT,
    function ($signo)
    {
        global $exit;
        $exit = true;
    }
);

if (!empty($argv[2])) {
    $fd = fopen($argv[2], "w");
    fwrite($fd, (string)getmypid());
    fclose($fd);
}
// Fork this into two
pcntl_fork();

if (is_numeric($argv[1])) {
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($socket, "127.0.0.1", $argv[1]);
} else {
    $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
    socket_connect($socket, $argv[1]);
}
$time = time();
while (!$exit && is_resource($socket) && ((time() - $time) < 10)) {
    $r = array($socket);
    $w = array();
    $e = array();
    if (@socket_select($r, $w, $e, 0, 10000)) {
        $input = @socket_read($socket, 1024);
        if (strlen($input) == 0) {
            break;
        }
        socket_write($socket, $input);
    }
}
socket_close($socket);
?>
