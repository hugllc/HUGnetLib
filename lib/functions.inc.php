<?php
/**
 * $Id: functions.inc.php 52 2006-05-14 20:51:23Z prices $
 *	@file functions.inc.php
 *	@par Copyright:
 *		&copy 2002-2005 Dragonfly Technologies, Inc. http://www.dflytech.com
 *	@brief This is all the misc functions that had no other home.
 *
 *	All of the functions that have no other home go into this file.  There
 *	are many handy functions here.
 *
 *	@verbatim
 *	Released under the GPL/LGPL
 *
	@verbatim
	Copyright (c) 2005 Scott Price
	All rights reserved.
	Released under the BSD License:

	Redistribution and use in source and binary forms, with or without modification, 
	are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, 
    this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, 
    this list of conditions and the following disclaimer in the documentation and/or 
    other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
	"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
	LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
	FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
	COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
	INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
	BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
	CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
	LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
	ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	POSSIBILITY OF SUCH DAMAGE.
	@endverbatim
 *
*/
/**
	This is the Portalversion
 */
$dflibversion = "0.5.0";
/**
	This is the Portalversion
 */
define("DFLIBVERSION", $dflibversion);
/**
 *	@mainpage DragonFlyPortal Documentation
 *	DragonFlyPortal is a library of code to make web development easier
 *	
 *	@author Scott L. Price (prices@dflytech.com)
 *	@date 09/27/2002
 *	@version 0.0.1
 *	@note There are a couple of glitches in these pages because Doxygen is still
 *		working out its PHP parser. SLP 09/28/2002
 *	
 *	@verbatim
 *	DragonFlyPortal is Released under the GPL/LGPL
 *	
 *	Copyright (C) 2002 Dragonfly Technologies, Inc. http://www.dflytech.com
 *	
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	(or the GNU Lesser General Public License at your option)
 *	as published by the Free Software Foundation; either version 2
 *	of the License, or (at your option) any later version.
 *	
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *	
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA
 *	02111-1307, USA.
 *	@endverbatim	
 */
/**
	This is the minimum version of PHP that this will run under.
 */
$RequiredPHP = "4.1.2";
/**
 *	@brief This function adds to the debug output
 *	@param stuff String This is what is actually added to the debug information
 *	@param level Integer between 0 and 10 this is the debug level at which to show it.
 *	@ingroup Debug
 *
 *	This function adds information to the file log in the debug info.    
 *	It must be the first function here, even preceeding the includes, as   
 *	all of the includes use it.                                            
 *
 */
function add_debug_output($stuff, $level=10) {
   global $debug;
   global $debug_output;

   if (($debug === TRUE) || ($debug <= $level)) {
      $debug_output .= $stuff;
   }
}

/**
 *	Returns seconds as years days hours minutes seconds
 */

function get_ydhms ($seconds, $digits=0) {	
	$years = (int)($seconds/60/60/24/365.25);
	$seconds -= $years*60*60*24*365.25;
	$days = (int)($seconds/60/60/24);
	$seconds -= $days*60*60*24;
	$hours = (int)($seconds/60/60);
	$seconds -= $hours*60*60;
	$minutes = (int)($seconds/60);
	$seconds -= $minutes*60;
	$seconds = number_format($seconds, $digits);
	
	$return = "";
	if ($years > 0) $return .= $years."Y ";
	if ($days > 0) $return .= $days."d ";
	if ($hours > 0) $return .= $hours."h ";
	if ($minutes > 0) $return .= $minutes."m ";
	$return .= $seconds."s";
	return($return);
}

/**
	@brief Prints out the number of bytes using K, M, G, T, Etc

*/
function get_bytes($bytes, $digits=2) {

	$labels = array("", " k", " M", " G", " T", " P");

	$index == 0;
	while ($bytes > 1024) {
		$bytes = $bytes/1024;
		$index ++;
	}
	$bytes = number_format($bytes, $digits);
	$bytes .= $labels[$index]." bytes";
	return($bytes);
}
/**
	@brief Prints a string formated in the debug style
	@param stuff String The string to print out
	@ingroup Debug
	
	Prints out the string in $stuff within tags that
	look like "\<DIV CLASS="debug">...\</DIV>"
*/
function print_debug($stuff) {
   print get_debug($stuff);

}

/**
	@brief Returns a string formated to print out as in the debug style
	@param stuff String The string to print out
	@returns String $stuff within HTML tags
	@ingroup Debug

	Returns a string with stuff in an HTML tag that 
	looks like "\<DIV CLASS="debug">...\</DIV>"
*/
function get_debug($stuff) {
   global $debug;
   if ($debug && (trim($stuff) != "")) {
      $text = "<DIV CLASS=\"debug\" STYLE=\"\">\n".wordwrap($stuff, 200, "<br />\n", TRUE)."\n</DIV>\n";
   } else {
      $text = "";
   }
   return $text;

}

/**
   @brief Prints out a lot of debug information.
	@ingroup Debug
	
	Prints out a lot of debug information including
	all of the GLOBALS in PHP ($_ENV, $_GET, etc) plus
	the PHP version, all the constants, and a listing
	of the debug output from the code that ran.
*/
function print_debug_info() {

   global $debug;
   global $debug_output;
   global $language;
   @include($language);

   if ($debug) {
   // Variables

      $text .= "<h1>Variables</H1>\n";
      $text .= get_stuff($_ENV, "_ENV");  
      $text .= get_stuff($_COOKIE, "_COOKIE");
      $text .= get_stuff($_GET, "_GET");
      $text .= get_stuff($_POST, "_POST");
      $text .= get_stuff($_FILES, "_FILES");
      $text .= get_stuff($_SESSION, "_SESSION");
      $text .= get_stuff($_SERVER, "_SERVER");
//      $text .= session_encode();

      print_debug($text);


      // Files
      $text = "<h1>Files</h1>\n\n";
      $const = get_included_files();
      if (is_array($const) && (count($const) > 0)) {   
         $text .= "<b>".$strIncludedFiles."</b><br />\n";
         foreach(array_keys($const) as $EKeys) {
            $text .= "&nbsp; &nbsp; &nbsp; <b>".$EKeys.":</b> ".$const[$EKeys]."<br />\n";
         }
      }
      print_debug($text);


      // File Log
      $debug_output = "<h1>".$strFileLog."</h1>\n".$debug_output;
      print_debug($debug_output);

      // PHP Information
      $text = "<h1>PHP ".$strInformation."</h1>\n";
      $text .= "<b>Version:</b> ".phpversion()."<br />\n";
      $text .= "<b>OS:</b> ".php_uname()."<br />\n";
      $text .= "<b>Interface:</b> ".php_sapi_name()."<br />\n";
      $const = get_loaded_extensions();
      if (is_array($const) && (count($const) > 0)) {
         $text .= "<b>Loaded Extensions:</b><br />\n";
         foreach(array_keys($const) as $EKeys) {
            $text .= "&nbsp; &nbsp; &nbsp; <b>".$EKeys.":</b> ".$const[$EKeys]."<br />\n";
         }
      }
      
		// Process ID stuff.
      $text .= "<b>User:</b> ".getmyuid()." (".get_current_user().")<br />\n";
      $text .= "<b>Process #:</b> ".getmypid()."<br />\n";
      $cfile = get_cfg_var("cfg_file_path");
      if (ini_get("register_globals") == "1") {
         $rg = "on";
      } else {
         $rg = "off";
      }
      if (trim($cfile) == "") $cfile = "None";
      $text .= "<b>Configuration File:</b> ".$cfile."<br />\n";
      $text .= "<b>Register Globals is:</b> ".$rg."<br />\n";
      $text .= "<b>Constants:</b><br />\n";
      $const = get_defined_constants();
      ksort($const);
      $text .= get_stuff($const, "CONSTANTS");
      print_debug($text);
   }

}

/**
	@brief Makes stuff printable
	@return String A printable copy of what it was given.
	@param array Mixed The input.  Can be just about anything including objects and arrays.
	@param name String The name you want it printed out under.
	@param level integer DO NOT USE.  FOR INTERNAL USE ONLY.  This allows it to recurse.
	
	This function takes just about anything and makes it printable.  It splits out arrays
	and objects so they look like the code it would have taken to create them.  Ex if $a = array("Hello"),
	$a would print out: @c a[0]="Hello" .
*/
function get_stuff($array, $name="Stuff", $levelLimit = 50, $level=1) {

	if ($level > $levelLimit) return $levelLimit;
   if (is_array($array)) {
      foreach(array_keys($array) as $key) {
         if ((is_array($array[$key])) || (is_object($array[$key]))){
            if (is_array($array)) {
               $nextname = $name." [".$key."]";
            } else {
               $nextname = $name."->".$key;
            }
            $text .= get_stuff($array[$key], $nextname, $levelLimit, $level + 1);
         } else {
            $text .= "&nbsp; &nbsp; &nbsp; ";
            $text .= "<b>".$name." [".$key."]:</b> ";
            $text .= " ".get_value($key, $array[$key])."<br />\n ";
         }
      }
   } else if (is_object($array)) {
      foreach(array_keys(get_object_vars($array)) as $key) {
         if ((is_array($array->$key)) || (is_object($array->$key))){
            if (is_array($array)) {
               $nextname = $name." [".$key."]";
            } else {
               $nextname = $name."->".$key;
            }
            $text .= get_stuff($array->$key, $nextname, $levelLimit, $level + 1);
         } else {
            $text .= "&nbsp; &nbsp; &nbsp; ";
            $text .= "<b>".$name."->".$key.":</b> ";
            $text .= " ".get_value($key, $array->$key)."<br />\n ";
         }
      }
   } else {
      $text = $name ." = ".get_value($name, $array)."<br />\n ";
   }
   if (($level == 1) && (trim($text) != "")) $text .= "<br />\n"; 
   return ($text);

}

/**
	@brief This function is a formatter for get_stuff.
	@return String Formated text based on $key and $value
	@param key String The name of the variable to print out
	@param val Mixed The value of the variable to print out
	@private
	
	This routine is used by get_stuff and should not be called directly.
*/
   function get_value($key, $val) {
      if (is_bool($val)) {
         if ($val) { 
            $return = "TRUE";
         } else {
            $return= "FALSE";
         }
      } else {
         if ((trim(strtolower($key)) != "password") && (trim(strtolower($key)) != "passwd") && (stristr($key, "AUTH_PW") === FALSE)) {
            if (is_string($val)) {
               $return = "'".htmlspecialchars($val)."'";
            } else {
               $return = $val;
            }
         } else {
            $return .= " <em>Hidden</em>";
         }
      }
      return($return);
   }

/**
	@brief Creates select boxes to select the time of day.
	@return String HTML code to create a select box for the time of day
	@param Name String the name of the SELECT input.  
	@param Selected Mixed This is the time to show by default in the boxes
		If it is not set it defaults to Now.  It will show the closest time
		that it can to the time it was given.
	@param MinuteIncrement Integer The increment for the minutes field.  Make
		this 1 to show all 60 minutes in the hour.  A higher increment means less
		options for the user.  Default = 5

	This will come back (when the form is submitted) as an array of the following:
	   	-name[hour] Integer the hour returned
	   	-name[minute] Integer the minute returned
	   	-name[am] String Either 'am' or 'pm'
	   	
*/
function SelectTime($Name="time", $Selected="", $MinuteIncrement=5) {
   if (trim($Selected) == "") {
      $Selected = time();
   } else {
      if (!is_numeric($Selected)) {
         $Selected = strtotime(date("Y-m-d ").$Selected);
      }
   }
   
   if (trim($Name) == "") $Name = "time";

   $selhour = date("h", $Selected);
   // This rounds the minutes to the nearest increment
   $selmin = round(date("i", $Selected)/$MinuteIncrement) * $MinuteIncrement;
   if ($selmin >= 60) {
      $selmin = 0;
      $hour++;
   }
   if ($hour > 12) $hour = 1;
   $selam = date("a", $Selected);

	// Do the hour
   $return = "<SELECT NAME=\"".$Name."[hour]\">\n";
   for ($i = 1; $i < 13; $i++) {
      $return .= "<option value=".$i;
      if ($i == $selhour) $return .= " SELECTED";
      $return .= ">".$i."</OPTION>\n";
   }
   $return .= "</SELECT>\n";
   $return .= " : ";

	// Do the minutes
   $return .= "<SELECT NAME=\"".$Name."[minute]\">\n";
   for ($i = 0; $i < 60; $i+=$MinuteIncrement) {
      $return .= "<option value=".$i;
      if ($i == $selmin) $return .= " SELECTED";
      $return .= ">".str_pad($i, 2, "0", STR_PAD_LEFT)."</OPTION>\n";
   }
   $return .= "</SELECT>\n";
   $return .= " ";

	// Do am/pm
   $return .= "<SELECT NAME=\"".$Name."[am]\">\n";
   $return .= "<option value=am";
   if ("am" == trim(strtolower($selam))) $return .= " SELECTED";
   $return .= ">am</OPTION>\n";
   $return .= "<option value=pm";
   if ("pm" == trim(strtolower($selam))) $return .= " SELECTED";
   $return .= ">pm</OPTION>\n";
   $return .= "</SELECT>\n";

   return($return);
}

/**
	@brief Creates a select box for the month
	@return String HTML code to create a select box for the month
	@param Name String the name of the SELECT input  Default = 'month'
	@param Selected Integer This is the month to show by default
	@sa SelectYear
	@sa SelectDay
	@sa SelectTime
	
	Just print out the output of this routine and you will get a select box
	for the month.
*/
function SelectMonth($Name="month", $Selected="") {

   if (trim($Selected) == "") $Selected = date("m");

   $return = "<SELECT NAME=\"".$Name."\">\n";
   for ($i = 1; $i < 13; $i++) {
      $return .= "<option value=".$i;
      if ($i == $Selected) $return .= " SELECTED";
      $temp = getdate(mktime(0,0,0,$i,1,2000));
      $return .= ">".$temp["month"]."</OPTION>\n";
   }
   $return .= "</SELECT>\n";
   return($return);
}

/**
	@brief Creates a select box for the year
	@return String HTML code to create a select box for the year
	@param Name String the name of the SELECT input  Default = 'Year'
	@param Selected Integer This is the year to show by default
	@param EndYear Integer the last year to print.  Default = Current Year
	@param StartYear Integer the first year to print.  Default = EndYear - 6
	@sa SelectDay
	@sa SelectMonth
	@sa SelectTime
	
	Just print out the output of this routine and you will get a select box
	for the year.
*/
function SelectYear($Name="Year", $Selected="", $EndYear = "", $StartYear="") {
   if (trim($EndYear) == "") $EndYear = date("Y")+1;
   if (trim($StartYear) == "") $StartYear = $EndYear - 6;

   if (trim($Selected) == "") $Selected = date("Y");   
   if ($Selected > $EndYear) $Selected = $EndYear;
   $return = "<SELECT NAME=\"".$Name."\">\n";
   for ($i = $StartYear; $i <= $EndYear; $i++) {
      $return .= "<option value=".$i;
      if ($i == $Selected) {
         $return .= " SELECTED ";
      }
      $return .= ">".$i."</OPTION>\n";
   }
   $return .= "</SELECT>\n";
   return($return);
}

/**
	@brief Creates a select box for the day of the month
	@return String HTML code to create a select box for the day of the month
	@param Name String the name of the SELECT input  Default = 'Day'
	@param Selected Integer This is the Day to show by default
	@sa SelectYear
	@sa SelectMonth
	@sa SelectTime
	
	Just print out the output of this routine and you will get a select box
	for the day of the month.

*/
function SelectDay($Name="Day", $Selected="") {
   
   if (trim($Selected) == "") $Selected = date("d");
   $return = "<SELECT NAME=\"".$Name."\">\n";
   for ($i = 1; $i < 32; $i++) {
      $return .= "<option value=".$i;
      if ($i == $Selected) $return .= " SELECTED";
      $return .= ">".$i."</OPTION>\n";
   }
   $return .= "</SELECT>\n";
   return($return);
}


/**
	@brief Adds links in text
	@param text String The string to mark up
	@returns String The marked up string
	@todo Make these regular expressioons better
	@todo Make it recognize already marked up links

	This function takes a string in, and replaces any occurance of links (http://www.dflytech.com or mailto:prices@dflytech.com)
	and replaces them with an <A HREF=> tag.  This makes links in text output clickable.
*/
function markup($text) {
//   global $extra;
//   $text = strip_tags($text, "<a><br><p><em><b></b></em>);
//   $text = htmlspecialchars($text);

//   $text = preg_replace('/\*(\S+)\*/','<em>\\1</em>',$text);
//   $text = preg_replace('/((http|https):\/\/[A-Za-z.-\/\\-?+@&;#~%\=0-9_\-]*)/','<A HREF="\1" target="_blank" CLASS=\"light\">\1</a>',$text);
 //  $text = preg_replace('/(>)((http|https):\/\/[A-Za-z.-\/\\-?+@&;#~%\=0-9_\-]*\.(gif|png|jpg|pjpeg))(<)/','><IMG SRC="\2" BORDER=0  ALT="\2" CLASS="markup"><',$text);
 //  $text = preg_replace('/(mailto:)*([A-z0-9_\-.]*)@([A-Za-z\=\-0-9_.+]*)\.([A-Za-z\=\-0-9_.+]*)([\?]?)([A-Za-z\=0-9_.+]*)/','\1<A HREF="mailto:\2@\3.\4">\2@\3.\4\5\6</a>',$text);

   $base = trim(BASE_WEB_DIR);
   if (substr($base, strlen($base)-1, 1) == "/") {
	$base = substr($base, 0,  strlen($base)-1);
   }

	$text = preg_replace('/(\[img\])+([A-Za-z.-\/\\-?+@&;#%\=0-9_\-]*)(\[\/img\])+/','<a href="'.$base.'\2" class="markup"><img src="'.$base.'\2" alt="[ Image ]" class="markup"></a>',$text);
	$text = preg_replace('/(<+[^<>]*)(<+[^<>]*>+)+([^<>]*>+)/','\1\3',$text);

//     $text = str_replace("//", "/", $text);
//   $text = str_replace("[img]", "<img src=\"".$extra, $text);
//   $text = str_replace("[/img]", "\" alt=Image class=markup>", $text);
//   $text = str_replace("&amp;", "&", $text);
//	$text = stripslashes($text);
 //  $text = nl2br($text);
   return($text);
}

/**
	@brief Formats the date consistantly, so that it is always the same. 
	@return String The formated date.
	@todo Make the format configurable.
	@param date Mixed This is the date in either Unix time format or a string (July 4, 1776)
	@param wrap Boolean Whether to force a line wrap between the date and time Default = FALSE
	@param time Boolean Whether or not to add the time.  Default = TRUE
*/
function formatdate($date, $wrap=FALSE, $time=TRUE) {

   if ($date == "") {
      $return = "Never";
   } else { 
      if (!is_numeric($date)) {
         $date = strtotime($date);
      }
      $return = date("m/d/Y", $date);
      if ($wrap) {
         $return .= "<br />";
      } else {
         $return .= " ";
      }
     if ($time) $return .= date("h:i a", $date);
   }
   return($return);
   
}

/**
	@brief Gets all of the arguments of a web page and returns them.
	@return The arguments of the web page formated to be added to the the URL of a link
	@param skip String A comma separated list of parameters to ignore.

	This function returns all of the arguments to a web page in a manner in which
	they can be easily added to a link.  They come back in the format:  "?Arg1=Value1&Arg2=Value2"
*/
function get_args($skip="") {
   $args = "?";

	// Remove the session information.  It will be added automatically anyway.
   if (trim($skip) != "") $skip .= ",";
   $skip .= session_name().", name, password, passwd";

   foreach(array_keys($_GET) as $key) {
      if (stristr($skip, $key) === FALSE) {
         $args .= $sep.$key."=".$_GET[$key];
         $sep = "&";
      }
   }
   foreach(array_keys($_POST) as $key) {
      if (stristr($skip, $key) === FALSE) {
         $args .= $sep.$key."=".$_POST[$key];
         $sep = "&";
      }
   }
   return($args);
}

/**
	@brief Returns hidden input tags for whatever variable is fed into it.
	@param var The variable to save.
	@param name The name of the variable to save.
*/
   function get_hidden_var_post($var, $name="") { 
      if (is_array($var)) {
         foreach($var as $key => $value) {
            if ((trim(strtolower($key)) != "password") && (trim(strtolower($key)) != "username")) {
               if (trim($name) == "") {
                  $usename = $key;
               } else {
                  $usename = $name."[".$key."]";
               }
               $return .= get_hidden_var_post($value, $usename);
            }
         }
      } else {
	    	if (trim($name) != "") {
				$return .= "<INPUT TYPE=HIDDEN NAME=\"".$name."\" VALUE=\"".$var."\">\n";
			}
      }
      return($return);
   }

add_debug_output("******** End of file ".__FILE__." *********<br />\n<br />\n");
?>
