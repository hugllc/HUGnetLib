<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
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
 *   @subpackage Lib
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
/**
	@class plugins
	@brief This class handles plugins

	<b>Building Applications</b>
	@par
	Adding plugin support to your application is easy.  Just figure out what different
	types of plugins that you want for your application and put hooks in for those.  The
	"types" are any alphanumeric text that you make up.  That way plugin types can have
	names that are meaningful in your application.  The following hooks are supported:
	-# @c run_function used to run one function
	-# @c run_functions used to run all functions of one type
	-# @c run_filters used to run all filters of one type
	-# @c get_generic used to get infomation on generic plugins of a certain type
	-# @c get_functions used to get infomation on all plugins of a certain type
	@par
	These functions can be used to call functions, run filters (functions that take input and give output)
	and get generic functions.  Here are the different categories of plugins and what they are good for:
	@par	
	-# Functions These are broken down into two different sub-categories
		- Functions take no arguments and give no return
		- Filters can take multiple arguments and return one item
	-# Menu items These appear as menu items that can be sent directly to the menu class
	-# Generics These items can be just about anything, but the specific application has to implement them
	@par
	<B>Building Plugins</b>
	@par
	Plugins must be registered.  There are several ways of doing this.
	- Call a Registration Function - Calling one of the registration functions with the correct arguments
		will register your function to run.  This call should be in the global scope of the file.  It should
		also come after the function it references.
	- Creating the array "plugin_info" in the global scope of the plugin file.  The following should be supplied
		in the array:
		- The following are REQUIRED
			- plugin_info["Functions"] Array Either an array of function names or an array of function information.  If the
				information exists, the function will not be called to get more information.  If this is an array of function
				names, the function will be called with the first argument being a boolean TRUE.  The function should return
				an array of info about itself when called this way.  The information needed about the function consists of the
				following (the array will be called "info" but it should be either returned by your function or be set at
				plugin_info["Functions"][]):
					- The following are REQUIRED
						- @c info["Name"] String This is the name of the function
					- The following are SUGGESTED
						- @c info["Types"] Array or Comma separated list This is an array of types that this function should be registered as
						- @c info["Description"] String Description of what this function does
						- @c info["Warning"] Any warnings associated with this function
						- @c info["Notes"] Notes about the use if this function
						- @c Any of the items from 'plugin_info' that are different from the file
		- The following are SUGGESTED
			- @c plugin_info["Author"] String the author of the file <em>Ex.	Scott L. Price (prices@dflytech.com)</em>
			- @c plugin_info["Copyright"] String the copyright notice of the file  <em>Ex.  (C) 2002 Dragonfly Technologies, Inc.</em>
			- @c plugin_info["License"] String the license applied to the code  <em>Ex.  GPL / LGPL</em>
			- @c plugin_info["License_Notes"] String any notes on the license  <em>Ex. "This software is freesoftware..."</em>
			- @c plugin_info["Name"] String the name of the plugin <em>Ex. "Bob's Media Plugin"</em>
			- @c plugin_info["Notes"] String this should be any notes on the file
			- @c plugin_info["Description"] String the description of what the plugin does
			
	Here is an example with multiple plugins each being added different ways.
	@code
	global $debug;
	if ($debug) {

   	function Hello() {
   	   print "Hello There!  This is a test plugin to make sure plugins are working.<BR>\n";
   	     }
	
   	     $this->register_function("Hello", "", "Test Plugin", "This is a test plugin");
	
	
		// This is the second test.
   	$plugin_info["Functions"][0]["Name"] = "Second_Test";
   	$plugin_info["Functions"][0]["Title"] = "AutoPlugin Test";
   	$plugin_info["Functions"][0]["Description"] = "This function tests the automatic loading of plugins via the$
   	$plugin_info["Copyright"] = "(C) 2002 MeterTools.com";
   	$plugin_info["Name"] = "AutoPlugin Test";
   	$plugin_info["Author"] = "Scott L. Price (prices@dflytech.com)";
   	$plugin_info["Date"] = "09/25/2002";
   	$plugin_info["License"] = "Proprietary";

   	function Second_Test() {

   	   print "Hello There!  This is a test plugin to make sure plugins are working.<BR>\n";

   	   print get_stuff($return, "Plugin Info");
   	}
	}	
	@endcode
*/
class plugins {
	/** @privatesection */
	var $plugins = array("Functions" => array(), "Menu" => array(), "Generic" => array()); //!< This is where plugin information is stored
	var $plugin_info = array();  //!< This is where information on the plugin files is stored.
	var $dir = "./plugins/"; //!< This is the directory where plugins will be looked for.
	var $extension = ".plugin.php"; //!< The file extension of plugins.
	var $plugin_count = 0; //!< This is the total number of plugins registered
	var $file_count = 0; //!< This is the total number of plugin files parsed
	var $webdir = "plugins/"; //!< This is the directory according to the URL
	var $_debug_stack = "";   //!< Debug output is contained here.

	/**
		@publicsection
	*/
	/**
		@name Functions for plugin developers
	*/
	///@{
	/**
		@brief Adds a menu item
		@sa menu.inc.php
		@param Name Array or String If an Array, it should contain at least Name["Name"].  Other valid items are Name["Link"]
			Name["Show"], and Name["Help"].  None of the other paramters are used in this case.  If it is a string, it is 
			just the name of the item.  The other paramters must be used to set the other items.
		@param Link String The link used for the menu item.  Not used if Name is an array.
		@param Type String The type of the menu item.
		@param Show Boolean Whether or not to show the menu item.  Not used if Name is an array.
		@param Help String The help text to show in a popup.  Not used if Name is an array.
		
		This function adds a menu item.  It is fairly flexible.  If it is sent an array it will support anything that you give
		it as long as the menu system does.  It all depends on how the menus are added into the code.
	*/
	function add_menu_item($Name, $Link="", $Type="ALL_TYPES", $Show=1, $Help="") {
		if (!is_array($Name)) {
			$this->_debug("\tRegistering Menu Item:  ".$Name."\t\tLink:  ".$Link." ", 4);
			$Name = trim($Name);
			$Link = trim($Link);
			if ($Name != "") {
				$this->plugins["Menu"][$Type][] = array("Name" => $Name, "Link" => $Link, "Show" => $Show, "Help" => $Help);
			} else {
				$this->_debug(" Failed.  Name must not be blank", 4);
			}
		} else {
			$this->_debug("\tRegistering Menu Item:  ".$Name["title"]."\t\tType:  ".$Name["type"]." ", 4);
			if (isset($Name["title"])) {
				if (!isset($Name["type"])) $Name["type"] = "ALL_TYPES";
				$this->plugins["Menu"][$Name["type"]][] = $Name;
				$this->_debug(" Done!", 4);
			} else {
				$this->_debug(" Failed: 'Name' is not set in the array", 4);
			}
		}
		$this->_debug("\n", 4);
	}	

	/**
		@brief Registers a function as a plugin
		@param Name Array or String If an Array, it should contain at least Name["Name"] which is the name of the function.  The
			only other semi-required item is Name["Types"]
			which is an array of plugin types to register this as.  If it is not included, it will be registered as "ALL_TYPES" which is 
			probably not what you want.  None of the other paramters are used in this case.  If it is a string, it is 
			just the name of the item.  The other paramters must be used to set the other items.
		@param Type String The type of plugin to register this as.  If ommited plugin will be registered as "ALL_TYPES" which is probably not
			what you want.
		@param Title String The title of the plugin.  Anything goes, but it should be relatively short.  Optional
		@param Desc String The description of the plugin.  This is freeform text.  Optional
	*/
	function register_function($Name, $Type="", $Title="", $Desc="") {
		if (is_array($Name)) {
			$this->register_function_raw($Name);
		} else {
			$info = array("Name" => $Name, "Types" => $Type, "Title" => $Title, "Description" => $Desc);
			$this->register_function_raw($info);
		}
	}
	
	/**
		@brief Registers a generic plugin
		@param Name Array or String If an Array, it should contain at least Name["Name"] which is the name of the function.  The
			only other semi-required item is Name["Types"]
			which is an array of plugin types to register this as.  If it is not included, it will be registered as "ALL_TYPES" which is 
			probably not what you want.  None of the other paramters are used in this case.  If it is a string, it is 
			just the name of the item.  The other paramters must be used to set the other items.
		@param Type String The type of plugin to register this as.  If ommited plugin will be registered as "ALL_TYPES" which is probably not
			what you want.
		@param HTML String The HTML text associated with the plugin.  Optional

		This routine was originally made to allow plugins to insert HTML code in different place inside my application. It
		soon evolved into a generic plugin that could be used for a variety of things.
	*/
	function add_generic($Name, $HTML="", $Type="") {
		if (!is_array($Name)) {
			$info = array("Name" => $Name, "HTML" => $HTML, "Type" => $Type);
		} else {
			$info = $Name;
		}
		$this->add_generic_raw($info);
	}

	/**
		@brief Registers information about a plugin
		@param $Info Array Freeform array with information about the plugin.  "Name" is required by this software, everything else 
			is optional.  See description for suggested array.
		@param $Filename String the name of the file to attach to the about record.

		This routine is to easily add information about the plugin in question.  The following is the suggested array
		(Change values accordingly):
		@code
		$Info["Name"] = "Spam Assassin Plugin";
		$Info["Author"] = "Scott Price";
		$Info["AuthorEmail"] = "prices@dflytech.com";
		$Info["Copyright"] = "2004, 2005 Dragonfly Technologies, Inc";
		@endcode


	*/
	function add_about($Info, $Filename) {
		if (is_array($Info)) {
			$Info["Filename"] = stristr($Filename, $this->webdir);
			$Info["Type"] = "about";
			$this->add_generic_raw($Info);
		}
	}
	///@}
	/**
		@name Functions for application developers
	*/
	///@{
	/**
		@brief This function is used to run filter plugins.
		@param Argument Mixed This is the argument to be sent to the filter
		@param Type String This is the type of plugins to run.
		@note Any other parameters sent to this function will be sent to the filter
			in the order they are received.
			
		This function runs filter plugins.  These are just normal function plugins
			except they take and argument and return a value.  They are registered just like any other
			function.  They are called filters because they were created to take in a value, modify it based
			on the plugin, then return it.  It is mostly used for filtering strings based on plugins.  In
			DragonFlyMail filter plugins are used to add change how the subject in the mail_index is printed
			out.  It is used by the msize plugin to add the number of lines and the size of the message after
			the subject.
	*/
	function run_filter($Argument, $Type) {
	
		$return = $Argument;
		$this->_debug("Running Plugin Filters of Type: ".$Type."\n", 4);
		if (is_array($this->plugins["Functions"][$Type])) {
			foreach($this->plugins["Functions"][$Type] as $fct) {
				$function = $fct["Name"];
				$this->_debug("Running Plugin ".$function."\n", 4);
				if (function_exists($function)) {
					$command = "\$output = $function(";
					if ($fct["INFO"] === TRUE) $command .= "FALSE";
					$command .= "\$return";
					$args = func_get_args();
					for ($i = 2; $i < func_num_args(); $i++) {
						$command .= ", \$args[".$i."]";
					}
					$command .= ");";
					$this->_debug("Running command:\n".$command."\n", 4);
					$this->_debug("[PLUGIN OUTPUT]\n", 4);
					eval($command);
					//$output = $function($return);
			   if (trim($output) != "") $return = $output;
					$this->_debug($output, 4);
					$this->_debug("[END PLUGIN OUTPUT]\n", 4);
				}
			}
		}
		return($return);
	}
	
	/**
		@brief This runs one plugin function.
		@param Name String The name of the function to run
		
		This routine is useful if you have a page where you only want to run one plugin at at time.
		It will run the function specified and exit.  This will not run filters correctly.  Use run_filter
		if it is available.
	*/
	function run_function($Name) {

		$fct = $this->get_function($Name);
		if ($fct !== FALSE) {
			$this->_debug("Running Plugin '".$Name."' of Type: '".$fct["Type"]."'\n", 4);
			$function = $fct["Name"];	
			if (function_exists($function)) {
 				$command = "\$output = ".$function."(";
				if ($fct["INFO"] === TRUE) $command .= "FALSE";
				$args = func_get_args();
				for ($i = 1; $i < func_num_args(); $i++) {
					$command .= ", \$args[".$i."]";
				}
				$command .= ");";
				$this->_debug("Running command:\n".$command."\n", 4);
				$this->_debug("[PLUGIN OUTPUT]\n", 4);
				eval($command);
				//$output = $function($return);
				if (trim($output) != "") $return = $output;
				$this->_debug($output, 4);
				$this->_debug("[END PLUGIN OUTPUT]\n", 4);
			}
		} else {
			$this->_debug("Function ".$Name." Not Found!\n", 4);
		}
	}
	
	/**
		@brief This finds the specified plugin and returns all of the info about it.
		@param Name String The name of the function to find
		
		Used for finding information on a specific plugin.  It returns the array fed
		to register_function_raw, exactly as it was sent.
	*/
	function get_function($Name) {

		$return = FALSE;
		if (is_array($this->plugins["Functions"])) {
			foreach($this->plugins["Functions"] as $Type) {
				if (is_array($Type)) {
					foreach($Type as $fct) {
		  				if ($fct["Name"] == $Name) {
							$return = $fct;
							$return["Type"] = $Type;
							break;
						}
					}
				}
				if ($return !== FALSE) break;
			}
		}
		return($return);
	}

	/**
		@brief Runs all functions of one type
		@param Type String The type of function to run
		@return Integer The number of functions run
		
		This function is the mainstay of running plugins.  It is used to run plugins in batches
		based on their type.
	*/
	function run_functions($Type) {
	
		$count = 0;
		$this->_debug("Running Plugins of Type: ".$Type."\n", 4);
		if (is_array($this->plugins["Functions"][$Type])) {
			foreach($this->get_functions($Type) as $fct) {
				$function = $fct["Name"];
				$this->_debug("Running Plugin ".$function."\n", 4);
				if (function_exists($function)) {
					$command = "\$output = $function(";
					if ($fct["INFO"] === TRUE) $command .= "FALSE";
					$args = func_get_args();
					$sep = "";
					for ($i = 1; $i < func_num_args(); $i++) {
						$command .= $sep." \$args[".$i."]";
						$sep = ",";
					}
					$command .= ");";
					$this->_debug("Running command:\n".$command."\n", 4);
					$this->_debug("[PLUGIN OUTPUT]\n", 4);
					eval($command);
					//$output = $function($return);
					if (trim($output) != "") $return = $output;
					$this->_debug($output, 4);
					$this->_debug("[END PLUGIN OUTPUT]\n", 4);
					$count++;
				}
			}
		}
		return($count);	
	}
	
	/**
		@brief Gets all of the generic of one type.
		@return Array An array of plugins of whatever type was sent to it, plus all plugins of type "ALL_TYPES".
		@param Type String The type of generic plugins to return
		
		This returns an array of information on all of the generic plugins of a certain type, plus all generic
		plugins of type "ALL_TYPES".
	*/
	function get_generic($Type) {
		if (is_array($this->plugins["Generic"]["ALL_TYPES"])) {
			$return = array_merge($this->plugins["Generic"][$Type], $this->plugins["Generic"]["ALL_TYPES"]);
		} else {
			$return = $this->plugins["Generic"][$Type];
		}
		if (!is_array($return)) $return = array();
		$return = $this->sort_plugins($return);
		return($return);
	}

	/**
		@brief Gets all of the Menu's of one type.
		@return Array An array of plugins of whatever type was sent to it, plus all plugins of type "ALL_TYPES".
		@param Type String The type of generic plugins to return
		
		This returns an array of information on all of the menu plugins of a certain type, plus all menu
			plugins of type "ALL_TYPES".  If no parameter is given, it returns everything it has for the menu.
	*/
	function get_menu($Type = FALSE) {
		$return = array();
		if ($Type !== FALSE) {
			if (is_array($this->plugins["Menu"][$Type]) && is_array($this->plugins["Menu"]["ALL_TYPES"])) {
				$return = array_merge($this->plugins["Menu"][$Type], $this->plugins["Menu"]["ALL_TYPES"]);
			} else {
				$return = $this->plugins["Menu"]["ALL_TYPES"];
			}
			$return = $this->sort_plugins($return);
		} else {
			$return = $this->plugins["Menu"];
			foreach($return as $key => $value) {
				$return[$key] = $this->sort_plugins($value);
			}
		}
		if (!is_array($return)) $return = array();
		return($return);
	}
	
	/**
		@brief Gets all of the functions of one type.
		@return Array An array of plugins of whatever type was sent to it, plus all plugins of type "ALL_TYPES".
		@param Type String The type of functions to return
		
		This returns all plugins that would be run if run_functions was called with the same type.  It is used
		to get a list of functions.
	*/
	function get_functions($Type) {
		if (is_array($this->plugins["Generic"]["ALL_TYPES"])) {
			$return = array_merge($this->plugins["Functions"][$Type], $this->plugins["Functions"]["ALL_TYPES"]);
		} else {
			$return = $this->plugins["Functions"][$Type];
		}
		if (!is_array($return)) $return = array();
		$return = $this->sort_plugins($return);
		return($return);
	}
	
	/**
		@brief finds the plugins in this->dir
		
		calls get_plugin_dir to actually find the plugins.  This function should be called if you
		need to find new plugins after the constructor is run.  This function is called by the constructor.
	*/
	function find_plugins() {
		$count = $this->get_plugin_dir($this->dir, $this->webdir, 0);  
		$this->_debug("Registered ".$this->plugin_count." plugin(s) in ".$this->file_count." File(s)\n\n", 4);
		//$this->_debug(get_stuff($this->plugins, "plugins"), 5);
	}
	/**
		@brief Sorts the plugin arrays.
		@param plugin_info Array Plugin information
		@param key depreciated.
		@return Array Plugin information sorted in a natural order
	
	*/
	function sort_plugins($plugin_info, $key="Name") {
//		usort($plugin_info, array("this", "compare_plugins"));
 		return($plugin_info);
	}

	///@}
		/**
		@privatesection
		Private Functions
	*/
	/**
		@brief Constructor
		@param basedir String the directory to look for plugins in.  Sets this->dir
		@param extension String the file extension to look for.  Sets this->extension
		@param webdir String the directory that it will be in on the web site.
		@param skipDir Array of Strings Directories to not look into for plugins.
		This routine sets this->dir and this->extension then checks for plugins
	*/
	function plugins ($basedir="", $extension="", $webdir = "", $skipDir=array()) {
        $this->plugins = &$GLOBALS['df_plugins'][$basedir][$extension];
		if (trim($basedir) != "" ) $this->dir = $basedir;
		if (trim($webdir) != "" ) $this->webdir = $webdir;
		if (trim($extension) != "" ) $this->extension = $extension;
		$this->_skipDir = $skipDir;
		if (!is_array($this->plugins)) $this->find_plugins();
	}

	/**
		@brief Reads the plugin directory and builds this->plugins and this->plugin_info
		@param basedir String The name of the directory to search.  This is here so that when this
			routine is called recursively it can go through different directories in the directory
			specified by this->dir.
		@param webdir String the directory that it will be in on the web site.
		@param Level Integer Depreciated

		Combs recursively through whatever directory it is given and looks for plugins.  It then
		registers them if it can.
	*/
	function get_plugin_dir($basedir= ".", $webdir="plugins/", $Level = 0, $recursive=TRUE) {
		$this->_debug("Checking for plugins in ".$basedir."\n", 4);
		$plugindir = @opendir($basedir);
		if ($plugindir) {
	
			$print_debug .= "\n";
			while ($file = readdir($plugindir)) {
				$files[] = $file;
			}
			natcasesort($files);
			$count = 0;
			foreach($files as $file) {
				$file = str_replace("/", "", trim($file));
				$webfile = str_replace("//", "/", $webdir."/".$file);
				$basefile = str_replace("//", "/", $basedir."/".$file);
				
				if (($file != "..") && ($file != ".")) { 
					if (is_file($basefile)) {
						if (preg_match('/[.]*'.str_replace(".", "\\.", $this->extension).'*/', $file) && !preg_match('/[.]*~/', $file)) {  // Eliminate files with ~ at the end of their name.
							if (substr($file, 0, 2) == ".#") {
								$this->_debug("Skipping CVS file $file\n");
							} else {
								$this->include_file($file, $basedir, $webdir);
							}
						}
					} else if (($file != "CVS") && ($file != ".svn") && (is_dir($basefile))) {
						$dName = str_replace($webdir, "", $webfile);
						if ((array_search($dName, $this->_skipDir) === FALSE) && ($recursive)) {
							$count += $this->get_plugin_dir($basefile."/", $webfile."/", $Level + 1);
						} else {
							$this->_debug("Skipping directory ".$dName."\n");
						}
					}
				}
			}
			$return = $count;
		} else {
			$this->_debug( "Error:  Directory ".$basedir." not found.\n\n", 4);
			$return = 0;
		}
		return($return);
	}	

	/**
		@brief Deals with the plugin files.
		@param file String The full or relative path to the file to be included.
		@param filedir String The filesystem directory where the files are located.
		@param webdir String The web directory where they are located (the path relative to DOCUMENT_ROOT)
		
		Includes files and registers any plugins it finds in those files.
	*/
	function include_file($file, $filedir = "", $webdir="") {
		global $debug;
		$plugin_info = FALSE;
		$this->_debug("Checking File:  ".$file."\n", 4);
		$freturn = include_once($filedir.$file); 
		if (!$freturn) {
			$this->_debug($freturn, 4);
			$this->_debug( "\tErrors encountered parsing file. Skipping ".$file.".\n", 4);
		} else {
			$this->file_count++;
			$info = NULL;
			if (is_array($plugin_info)) {
				$info = $plugin_info;
				$this->plugin_info[$file] = $info;
				if (is_array($info["Functions"])) {
					foreach($info["Functions"] as $fct) {
						if (is_array($fct) && !empty($fct["Name"])) {
							if (function_exists($fct["Name"])) {
								$this->register_function_raw($fct);
							}
						} else {
							if (function_exists($fct)) {
								$fctinfo = $fct(TRUE);
								$fctinfo["INFO"] = TRUE;
								$this->register_function_raw($fctinfo);
							}
						}
					}
				}
				if (is_array($info["Generics"])) {
					foreach($info["Generics"] as $gen) {
						$this->register_function_raw($gen);
					}
				}
			}
						
		}
								
	}
	
	

	
	/**
		@brief Function to register plugin functions.
		@param info Array This must at least contain info["Name"] which must be the name of a valid function, or the plugin 
			won't be registered.  Anything else is stored with the name of the function in case it should ever be needed.
			
		This function sets up the array containing function names and descriptions.  It should not be called directly.
	*/
	function register_function_raw($info) {
 		$this->_debug("\tRegistering Function:  ".$info["Name"]."\t\tType:  ".$info["Types"]."\t\t", 4);
		if (is_array($info)) {
			if (trim($info["Name"]) != "") {
				if (function_exists($info["Name"])) {
					if (is_array($info["Types"])) {
						foreach($info["Types"] as $Type) {
							$this->plugins["Functions"][$Type][] = $info;
						}
					} else {
						if (trim($info["Types"]) != "") {
							foreach(explode(",", $info["Types"]) as $Type) {
								$this->plugins["Functions"][trim($Type)][] = $info;
							}
						} else {
							$this->plugins["Functions"]["ALL_TYPES"][] = $info;
						}
					}
					$this->_debug(" Done!", 4);
					$this->function_count++;
					$this->plugin_count++;
				} else {
					$this->_debug(" Failed (Function doesn't Exist)", 4);
				}
			} else {
				$this->_debug(" Failed (Name not set), 4");
			}
		} else {
			$this->_debug(" Failed (Bad arguments to register_function)", 4);
		}
  		$this->_debug("\n", 4);
	}
	
	
	
	/**
		@brief Adds a generic plugin to the list of valid plugins.
		@param info Array this is all of the information about the plugin.  Only info["Name"] is required.  Everything
			else is application dependent.  These plugins allow for doing almost anything.

		Copys the info parameter into its array of generic plugins.
	*/
	function add_generic_raw($info) {
	
		$info["Name"] = trim($info["Name"]);
		if (!isset($info["Type"])) $info["Type"] = "ALL_TYPES";
		$this->_debug("\tRegistering Generic:  ".$info["Name"]."\t\tType:  ".$info["Type"], 4);
		if ($info["Name"] != "") {
			$this->_debug("  Done", 4);
			$this->plugins["Generic"][$info["Type"]][] = $info;
			$this->generic_count++;
			$this->plugin_count++;
		} else {
			$this->_debug("  Failed", 4);
		}
		$this->_debug("\n", 4);
	
	}
	

	/**
		@brief Used to compare plugins for sorting.
		@param a Array The first argument for the compare
		@param b Array The second argument for the compare
		
		This function compares the names of the plugins and returns the output needed by usort.  It should only
		be used for this purpose.
	*/
	function compare_plugins($a, $b) {
		return(strnatcasecmp($a["Name"], $b["Name"]));
	}
	


	/**
		@private
		@brief Saves debug information.
		@param $text String Text to add to the stack
		@param $level Integer 0-5 How much to log to the stack.

	*/
	function _debug($text, $level = 1) {
		$this->_debug_stack .= $text;
	}

	/**
		@public
		@brief Returns the debug stack.	
		@return The debug stack.
	*/
	function getDebug() {
		return $this->_debug_stack;
	}
	
}	

?>
