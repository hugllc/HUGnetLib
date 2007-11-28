<?php
/**
 *   Tests the socket class
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id: epsocketTest.php 507 2007-11-27 20:59:12Z prices $
 *
 */

$msg = $argv[1];

error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address = '127.0.0.1';
$port = 35000;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    die();
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
    die();
}
if (socket_listen($sock) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
    die();
}

if (($msgsock = socket_accept($sock)) === false) {
    echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
    die();
}

$stdin = fopen("php://stdin", "r");
if ($stdin === FALSE) {
    echo "std in open failed";
    die();
}

stream_set_blocking($stdin, FALSE);
socket_set_nonblock($msgsock);
$lbuf = "";
$buf = "";
if (empty($msg)) {
    do {
        // remote stuff
        $char = socket_read($msgsock, 1);
        if (false !== $char) {
            if (strlen($buf) > 3) $buf = substr($buf, 1, 3);
            $buf .= trim($char);
            printf("%02X", ord($char))."\n";
        }
        // Local Stuff
        $lchar = fgetc($stdin);
        if ($lchar !== FALSE) {
            socket_write($msgsock, $lchar, 1);
            if (strlen($lbuf) > 3) $buf = substr($lbuf, 1, 3);
            $lbuf .= trim($lchar);            
        }
        usleep(50000);
        
    } while (($buf != "quit") && ($lbuf != "quit"));
} else {
    socket_write($msgsock, $msg, strlen($msg));
}

fclose($stdin);

socket_close($msgsock);

socket_close($sock);
?>
