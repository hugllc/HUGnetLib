<?php
/**
 *   Main HUGnet include.  Include this file and you should get everything that
 *   you need.
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
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
/** The version define for all of HUGnetLib */
define("HUGNET_LIB_VERSION", "0.7.2");	
/** This is for backward compatibility with some older stuff */
define("HUGNET_BACKEND_VERSION", HUGNET_LIB_VERSION);	

/** The base path to all the files included for HUGnet */
define("HUGNET_INCLUDE_PATH", dirname(__FILE__));	
if (!defined("HUGNET_DATABASE")) {
    /** The name of the default HUGnet Database */
    define("HUGNET_DATABASE", "HUGNet");
}
if (!defined("HUGNET_LOCAL_DATABASE")) {
    /** The name of the default local (sqlite) HUGnet Database */
    define("HUGNET_LOCAL_DATABASE", "HUGnetLocal");
}

$inc = ini_get('include_path');
$inc .= ":".dirname(__FILE__)."/lib/pear";
ini_set('include_path', $inc);
 

//require_once(HUGNET_INCLUDE_PATH."/device.inc.php");
/** Include the gateway code */
require_once(HUGNET_INCLUDE_PATH."/gateway.inc.php");
/** Include the endpoint driver code */
require_once(HUGNET_INCLUDE_PATH."/driver.inc.php");

if (!function_exists("get_temp_dir")) {
   /**
    *  This finds the temp directory for the system.  It does this by creating
    *  a temporary file name then cutting the directory off of this.
    *
    *  Returns FALSE on failure.
    *
    *  @return string The path to the temp directory.
    */
   function get_temp_dir() {
      // Try to use system's temporary directory
      // as random name shouldn't exist
      $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
      if ( $temp_file )
      {
          $temp_dir = realpath( dirname( $temp_file ) );
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
