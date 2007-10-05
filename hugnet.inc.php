<php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/**
	$Id$

	@file hugnet.inc.php
	@brief Includes all the files needed for HUGnet on other web pages.

	
*/
/**
	@mainpage
	This is the HUGnet web interface documentation.


	@par
		This page generated from @ref hugnet.inc.php
*/

define("HUGNET_BACKEND_VERSION", "0.7.2");	

/** The base path to all the files included for HUGnet */
define("HUGNET_INCLUDE_PATH", dirname(__FILE__));	
/** The name of the default HUGnet Database */
if (!defined("HUGNET_DATABASE")) {
    define("HUGNET_DATABASE", "HUGNet");
}
if (!defined("HUGNET_LOCAL_DATABASE")) {
    define("HUGNET_LOCAL_DATABASE", "HUGnetLocal");
}

$inc = ini_get('include_path');
$inc .= ":".dirname(__FILE__)."/lib/pear";
ini_set('include_path', $inc);
 

//require_once(HUGNET_INCLUDE_PATH."/device.inc.php");
require_once(HUGNET_INCLUDE_PATH."/gateway.inc.php");
require_once(HUGNET_INCLUDE_PATH."/driver.inc.php");

if (!function_exists("get_temp_dir")) {
   function get_temp_dir() {
      // Try to use system's temporary directory
      // as random name shouldn't exist
      $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
      if ( $temp_file )
      {
          $temp_dir = realpath( dirname($temp_file) );
          unlink( $temp_file );
          return $temp_dir;
      }
      else
      {
          return FALSE;
      }
   }
}

?>
