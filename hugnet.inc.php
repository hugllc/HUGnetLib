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

define("HUGNET_BACKEND_VERSION", "0.6.7");	

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
