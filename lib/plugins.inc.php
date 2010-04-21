<?php
/**
 * Class for doing autoregistering plugins.
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
 * @category   Plugins
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
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
/**
 * This class handles plugins
 *
 * <b>Building Applications</b>
 *
 * Adding plugin support to your application is easy.  Just figure out what different
 * types of Plugins that you want for your application and put hooks in for those.
 * The "types" are any alphanumeric text that you make up.  That way plugin types
 * can have names that are meaningful in your application.  The following hooks
 * are supported:
 * - <b>runFunction</b> used to run one function
 * - <b>runFunctions</b> used to run all functions of one type
 * - <b>runFilters</b> used to run all filters of one type
 * - <b>getGeneric</b> used to get infomation on generic Plugins of a certain type
 * - <b>getFunctions</b> used to get infomation on all Plugins of a certain type
 *
 * These functions can be used to call functions, run filters (functions that take
 * input and give output) and get generic functions.  Here are the different
 * categories of Plugins and what they are good for:
 *
 * - Functions These are broken down into two different sub-categories
 * 1. Functions take no arguments and give no return
 * 2. Filters can take multiple arguments and return one item
 * - Menu items These appear as menu items that can be sent directly to the menu
 *    class
 * - Generics These items can be just about anything, but the specific application
 *    has to implement them
 *
 * <b>Building Plugins</b>
 *
 * Plugins must be registered.  There are several ways of doing this.
 * 1. Call a Registration Function - Calling one of the registration functions
 *     with the correct arguments will register your function to run.  This call
 *     should be in the global scope of the file.  It should also come after the
 *     function it references.
 * 2. Creating the array "plugin_info" in the global scope of the plugin file.  The
 *     following should be supplied in the array:
 * The following are REQUIRED
 *   plugin_info["Functions"] Array Either an array of function names or an array
 *          of function information.  If the information exists, the function
 *          will not be called to get more information.  If this is an array of
 *          function names, the function will be called with the first argument
 *          being a boolean true.  The function should return an array of info
 *          about itself when called this way.  The information needed about the
 *          function consists of the following (the array will be called "info"
 *          but it should be either returned by your function or be set at
 *          plugin_info["Functions"][]):
 *
 *    The following are REQUIRED
 *     - <b> info["Name"]</b> String This is the name of the function
 *
 *    The following are SUGGESTED
 *     - <b> info["Types"]</b> Array or Comma separated list This is an array of
 *           types that this function should be registered as
 *     - <b> info["Description"]</b> String Description of what this function does
 *     - <b> info["Warning"]</b> Any warnings associated with this function
 *     - <b> info["Notes"]</b> Notes about the use if this function
 *     - Any of the items from 'plugin_info' that are different from the file
 *
 *  The following are SUGGESTED
 *   - <b> plugin_info["Author"]</b> String the author of the file
 *                         <em>Ex.    Scott L. Price (prices@dflytech.com)</em>
 *   - <b> plugin_info["Copyright"]</b> String the copyright notice of the file
 *                         <em>Ex.  (C) 2002 Dragonfly Technologies, Inc.</em>
 *   - <b> plugin_info["License"]</b> String the license applied to the code
 *                         <em>Ex.  GPL / LGPL</em>
 *   - <b> plugin_info["License_Notes"]</b> String any notes on the license
 *                         <em>Ex. "This software is freesoftware..."</em>
 *   - <b> plugin_info["Name"]</b> String the name of the plugin
 *                         <em>Ex. "Bob's Media Plugin"</em>
 *   - <b> plugin_info["Notes"]</b> String this should be any notes on the file
 *   - <b> plugin_info["Description"]</b> String the description of what the
 *                          plugin does
 *
 * Here is an example with multiple Plugins each being added different ways.
 *
 * <code>
 * global $debug;
 * if ($debug) {
 *
 *    function Hello() {
 *

print "Hello There!  This is a test plugin to make sure Plugins are working.<BR>\n";
 *    }
 *
 *    $this->registerFunction("Hello", "", "Test Plugin", "This is a test plugin");
 *
 *
 *    $plugin_info["Functions"][0]["Name"] = "Second_Test";
 *    $plugin_info["Functions"][0]["Title"] = "AutoPlugin Test";
 *    $plugin_info["Functions"][0]["Description"] = "This function tests the thing";
 *    $plugin_info["Copyright"] = "(C) 2002 MeterTools.com";
 *    $plugin_info["Name"] = "AutoPlugin Test";
 *    $plugin_info["Author"] = "Scott L. Price (prices@dflytech.com)";
 *    $plugin_info["Date"] = "09/25/2002";
 *    $plugin_info["License"] = "Proprietary";
 *
 *    function Second_Test() {
 *
 *       print "Hello There!  This is a test plugin<BR>\n";
 *
 *       print get_stuff($return, "Plugin Info");
 *    }
 * }
 *
 * </code>
 *
 * @category   Plugins
 * @package    HUGnetLib
 * @subpackage Lib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2002-2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Plugins extends HUGnetClass
{
    /** @var array Plugin Functions */
    var $plugins = array();
    /** @var array This is where information on the plugin files is stored. */
    var $plugin_info = array();
    /** @var string This is the directory where Plugins will be looked for. */
    var $dir = "./plugins/";
    /** @var string The file extension of plugins. */
    var $extension = ".plugin.php";
    /** @var int This is the total number of Plugins registered */
    var $plugin_count = 0;
    /** @var int This is the total number of plugin files parsed */
    var $file_count = 0;
    /** @var string This is the directory according to the URL */
    var $webdir = "plugins/";
    /** @var array The directory to skip. */
    var $_skipDir = array();

    /**
    * Registers a function as a plugin
    *
    * @param mixed  $Name  If an Array, it should contain at least Name["Name"]
    *     which is the name of the function.  The only other semi-required item
    *     is Name["Types"] which is an array of plugin types to register this
    *     as.  If it is not included, it will be registered as "ALL_TYPES" which is
    *     probably not what you want.  None of the other paramters are used in
    *     this case.  If it is a string, it is just the name of the item.  The
    *     other paramters must be used to set the other items.
    * @param string $Type  The type of plugin to register this as.  If ommited
    *     plugin will be registered as "ALL_TYPES" which is probably not
    *     what you want.
    * @param string $Title The title of the plugin.  Anything goes, but it
    *     should be relatively short.  Optional
    * @param string $Desc  The description of the plugin.  This is freeform text.
    *     Optional
    *
    * @return null
    */
    function registerFunction($Name, $Type="", $Title="", $Desc="")
    {
        if (is_array($Name)) {
            $this->registerFunctionRaw($Name);
        } else {
            $info = array(
                "Name" => $Name,
                "Types" => $Type,
                "Title" => $Title,
                "Description" => $Desc
            );
            $this->registerFunctionRaw($info);
        }
    }

    /**
    *  Registers a generic plugin
    *
    * This routine was originally made to allow Plugins to insert HTML code in
    * different place inside my application. It soon evolved into a generic
    * plugin that could be used for a variety of things.
    *
    * @param mixed  $Name If an Array, it should contain at least Name["Name"]
    *     which is the name of the function.  The only other semi-required item
    *      is Name["Types"] which is an array of plugin types to register this as.
    *        If it is not included, it will be registered as "ALL_TYPES" which is
    *     probably not what you want.  None of the other paramters are used in
    *     this case.  If it is a string, it is just the name of the item.  The
    *     other paramters must be used to set the other items.
    * @param string $HTML The HTML text associated with the plugin.  Optional
    * @param string $Type The type of plugin to register this as.  If ommited
    *     plugin will be registered as "ALL_TYPES" which is probably not
    *     what you want.
    *
    * @return null
    */
    function addGeneric($Name, $HTML="", $Type="")
    {
        if (!is_array($Name)) {
            $info = array("Name" => $Name, "HTML" => $HTML, "Type" => $Type);
        } else {
            $info = $Name;
        }
        $this->addGenericRaw($info);
    }

    /**
    *  This function is used to run filter plugins.
    *
    * This function runs filter plugins.  These are just normal function plugins
    *     except they take and argument and return a value.  They are registered
    *     just like any other function.  They are called filters because they
    *     were created to take in a value, modify it based on the plugin, then
    *     return it.  It is mostly used for filtering strings based on plugins.  In
    *     DragonFlyMail filter Plugins are used to add change how the subject in
    *     the mail_index is printed out.  It is used by the msize plugin to add
    *     the number of lines and the size of the message after
    *     the subject.
    *
    * Any other parameters sent to this function will be sent to the filter
    *     in the order they are received.
    *
    * @param mixed  &$Argument This is the argument to be sent to the filter
    * @param string $Type      This is the type of Plugins to run.
    *
    * @return mixed Modified version of Argument
    */
    function runFilter(&$Argument, $Type) {
        //$return = &$Argument;
        $this->vprint("Running Plugin Filters of Type: ".$Type."\n", 4);
        if (!is_array($this->plugins["Functions"][$Type])) {
            return $Argument;
        }
        $args = func_get_args();
        //array_shift($fArgs); // Shift off the argument
        //array_shift($fArgs); // Shift off the name of the function
        unset($args[1]); // Remove the Type.
        foreach ($this->plugins["Functions"][$Type] as $fct) {
            $this->vprint("Running Plugin ".$fct["Name"]."\n", 4);
            //$args = $fArgs;
            //array_unshift($args, $return);
            //$args = array_merge(array(&$return), $fArgs);
            $this->vprint("Running Plugin '".$fct["Name"]."'", 3);
            $this->vprint(" of Type: '".$fct["Types"]."'\n", 3);
            $this->vprint("[PLUGIN OUTPUT]\n", 4);
            $ret = $this->_runFunction($fct["Name"], $args);
            if (!is_null($ret)) {
                //$return = &$ret;
            }
            $this->vprint($output, 4);
            $this->vprint("[END PLUGIN OUTPUT]\n", 4);
        }
        return $return;
    }

    /**
    *  This runs one plugin function.
    *
    * This routine is useful if you have a page where you only want to run one
    * plugin at at time.  It will run the function specified and exit.  This will
    *  not run filters correctly.  Use runFilter if it is available.
    *
    * @param string $Name The name of the function to run
    *
    * @return null
    */
    function runFunction($Name)
    {
        $fct = $this->getFunction($Name);
        if (($fct === false) || !function_exists($fct["Name"])) {
            $this->vprint("Function ".$Name." Not Found!\n", 4);
            return false;
        }
        $this->vprint("Running Plugin '".$Name."' of Type: '".$fct["Type"]."'\n", 3);
        $args = func_get_args();
        array_shift($args); // Shift off the name of the function
        $this->vprint("Running command:\n".$command."\n", 4);
        $this->vprint("[PLUGIN OUTPUT]\n", 4);
        $output = $this->_runFunction($fct["Name"], $args);
        if (trim((string)$output) != "") {
            $return = $output;
        }
        $this->vprint($output, 4);
        $this->vprint("[END PLUGIN OUTPUT]\n", 4);
        return true;
    }

    /**
    * This finds the specified plugin and returns all of the info about it.
    *
    * Used for finding information on a specific plugin.  It returns the array fed
    * to registerFunctionRaw, exactly as it was sent.
    *
    * @param string $Name The name of the function to find
    *
    * @return null
    */
    function getFunction($Name)
    {
        if (!is_array($this->plugins["Functions"])) {
            return false;
        }
        foreach ($this->plugins["Functions"] as $Type) {
            if (!is_array($Type)) {
                continue;
            }
            foreach ($Type as $fct) {
                if ($fct["Name"] == $Name) {
                    return $fct;
                }
            }
        }
        return false;
    }

    /**
    * Runs all functions of one type
    *
    * This function is the mainstay of running plugins.  It is used to run Plugins
    * in batches based on their type.
    *
    * @param string $Type The type of function to run
    *
    * @return int The number of functions run
    */
    function runFunctions($Type)
    {

        $count = 0;
        $this->vprint("Running Plugins of Type: ".$Type."\n", 4);
        if (!is_array($this->plugins["Functions"][$Type])) {
            return 0;
        }
        $fArgs = func_get_args();
        array_shift($fArgs); // Shift off the name of the function
        foreach ($this->getFunctions($Type) as $fct) {
            $args = $fArgs;
            $this->vprint("Running Plugin '".$fct["Name"]."'", 3);
            $this->vprint(" of Type: '".$fct["Type"]."'\n", 3);
            $this->vprint("[PLUGIN OUTPUT]\n", 4);
            $output = $this->_runFunction($fct["Name"], $args);
            $this->vprint($output, 4);
            $this->vprint("[END PLUGIN OUTPUT]\n", 4);
            $count++;
        }
        return $count;
    }

    /**
    * Runs all functions of one type
    *
    * This function is the mainstay of running plugins.  It is used to run Plugins
    * in batches based on their type.
    *
    * @param string $name  The name of the function to call
    * @param array  &$args The arguments to call the function with
    *
    * @return int The number of functions run
    */
    private function &_runFunction($name, &$args)
    {
        if (!function_exists($name)) {
            $this->vprint("Function $name does not exist\n", 3);
            return false;
        }
        try {
            $call  = "return ".$name."(";
            if (!empty($args)) {
                $call .= '$args['.implode('], $args[', array_keys($args)).']';
            }
            $call .= ");";
            $output = eval($call);
        } catch (ErrorException $e) {
            $this->vprint("Caught Error: ".$e->getMessage()."\n", 1);
            return null;
        } catch (Exception $e) {
            $this->vprint("Caught Exception: ".$e->getMessage()."\n", 1);
            return null;
        }
        return $output;
    }
    /**
    * Gets all of the generic of one type.
    *
    * This returns an array of information on all of the generic Plugins of a
    * certain type, plus all generic Plugins of type "ALL_TYPES".
    *
    * @param string $Type The type of generic Plugins to return
    *
    * @return array An array of Plugins of whatever type was sent to it, plus
    *         all Plugins of type "ALL_TYPES".
    */
    function getGeneric($Type)
    {
        return $this->_getRaw($Type, "Generic");
    }

    /**
    * Gets all of the generic of one type.
    *
    * This returns an array of information on all of the generic Plugins of a
    * certain type, plus all generic Plugins of type "ALL_TYPES".
    *
    * @param string $Type The type of generic Plugins to return
    *
    * @return array An array of Plugins of whatever type was sent to it, plus
    *         all Plugins of type "ALL_TYPES".
    */
    function getClass($Type)
    {
        $return = $this->plugins["Class"][$Type];
        $return = $this->sortPlugins((array)$return);
        return $return;
    }

    /**
    * Gets all of the functions of one type.
    *
    * This returns all Plugins that would be run if runFunctions was called with
    * the same type.  It is used to get a list of functions.
    *
    * @param string $Type The type of functions to return
    *
    * @return array An array of Plugins of whatever type was sent to it, plus all
    *     Plugins of type "ALL_TYPES".
    */
    function getFunctions($Type)
    {
        return $this->_getRaw($Type, "Functions");
    }

    /**
    * Gets all of the plugins of one type.
    *
    * This returns all Plugins of plugin type $pType of type $Type
    *
    * @param string $Type  The type of functions to return
    * @param string $pType The type of plugin to get
    *
    * @return array An array of Plugins of whatever type was sent to it, plus all
    *     Plugins of type "ALL_TYPES".
    */
    private function _getRaw($Type, $pType)
    {
        if (is_array($this->plugins[$pType]["ALL_TYPES"])) {
            $return = array_merge(
                $this->plugins[$pType][$Type],
                $this->plugins[$pType]["ALL_TYPES"]
            );
        } else {
            $return = $this->plugins[$pType][$Type];
        }
        if (!is_array($return)) {
            $return = array();
        }
        $return = $this->sortPlugins($return);
        return($return);
    }

    /**
    * finds the Plugins in this->dir
    *
    * calls getPluginDir to actually find the plugins.  This function should be
    * called if you need to find new Plugins after the constructor is run.  This
    * function is called by the constructor.
    *
    * @return null
    */
    function findPlugins()
    {
        $count = $this->getPluginDir($this->dir, $this->webdir, 0);
        $this->vprint("Registered ".$this->plugin_count." plugin(s)", 4);
        $this->vprint("in ".$this->file_count." File(s)\n\n", 4);
        //$this->vprint(get_stuff($this->plugins, "plugins"), 5);
    }
    /**
    *  Sorts the plugin arrays.
    *
    * @param array  $plugin_info Plugin information
    * @param string $key         depreciated.
    *
    * @return array Plugin information sorted in a natural order
    */
    function sortPlugins($plugin_info, $key="Name")
    {
         return($plugin_info);
    }

    /**
    * Constructor
    *
    * This routine sets this->dir and this->extension then checks for plugins
    *
    * @param string $basedir   the directory to look for Plugins in.  Sets this->dir
    * @param string $extension the file extension to look for.  Sets this->extension
    * @param string $webdir    the directory that it will be in on the web site.
    * @param array  $skipDir   Array of Strings Directories to not look into for
    *                          plugins.
    * @param int    $verbose   The verbosity level
    *
    * @return null
    */
    function __construct(
        $basedir   = "",
        $extension = "",
        $webdir    = "",
        $skipDir   = array(),
        $verbose   = 0
    ) {
        $config = array(
            "verbose" => $verbose,
        );
        parent::__construct($config);

        if (!empty($basedir)) {
            $this->dir = $basedir;
        }
        if (!empty($webdir)) {
            $this->webdir = $webdir;
        }
        if (!empty($extension)) {
            $this->extension = $extension;
        }
        if (is_array($skipDir)) {
            $this->_skipDir = $skipDir;
        }
        $this->findPlugins();
    }

    /**
    *  Reads the plugin directory and builds this->plugins and this->plugin_info
    *
    * Combs recursively through whatever directory it is given and looks for
    * plugins.  It then registers them if it can.
    *
    * @param string $basedir   The name of the directory to search.  This is
    *     here so that when this routine is called recursively it can go
    *     through different directories in the directory specified by this->dir.
    * @param string $webdir    The directory that it will be in on the web site.
    * @param int    $Level     Depreciated
    * @param bool   $recursive Whether to be recursive or not
    *
    * @return null
    */
    function getPluginDir(
        $basedir   = ".",
        $webdir    = "plugins/",
        $Level     = 0,
        $recursive = true
    ) {
        $this->vprint("Checking for Plugins in ".$basedir."\n", 4);
        $plugindir = @opendir($basedir);
        if ($plugindir) {
            while ($file = readdir($plugindir)) {
                $files[] = $file;
            }
            natcasesort($files);
            $count = 0;
            foreach ($files as $file) {
                $file     = str_replace("/", "", trim($file));
                $webfile  = str_replace("//", "/", $webdir."/".$file);
                $basefile = str_replace("//", "/", $basedir."/".$file);
                if (($file != "..") && ($file != ".")) {
                    if (is_file($basefile)) {
                        $r = '/[.]*'.str_replace(".", "\\.", $this->extension).'*/';
                        if (preg_match($r, $file) && !preg_match('/[.]*~/', $file)) {
                            if (substr($file, 0, 2) == ".#") {
                                $this->vprint("Skipping CVS file $file\n");
                            } else {
                                $this->includeFile($file, $basedir, $webdir);
                            }
                        }
                    } else if (($file != "CVS")
                        && ($file != ".svn")
                        && (is_dir($basefile))
                    ) {
                        $dName  = str_replace($webdir, "", $webfile);
                        $search = array_search($dName, $this->_skipDir);
                        if (($search === false) && ($recursive)) {
                            $count += $this->getPluginDir(
                                $basefile."/",
                                $webfile."/",
                                $Level + 1
                            );
                        } else {
                            $this->vprint("Skipping directory ".$dName."\n");
                        }
                    }
                }
            }
            $return = $count;
        } else {
            $this->vprint("Error:  Directory ".$basedir." not found.\n\n", 4);
            $return = 0;
        }
        return($return);
    }

    /**
    *  Deals with the plugin files.
    *
    * Includes files and registers any Plugins it finds in those files.
    *
    * @param string $file    The full or relative path to the file to be included.
    * @param string $filedir The filesystem directory where the files are located.
    * @param string $webdir  The web directory where they are located (the path
    *     relative to DOCUMENT_ROOT)
    *
    * @return null
    */
    function includeFile($file, $filedir = "", $webdir = "")
    {
        static $cache;
        // This allows _setPluginVar to write to the cache
        $this->incFileCache = &$cache;
        $realFile = realpath($filedir.$file);
        if (empty($cache[$realFile]) || !is_array($cache[$realFile])) {
            $plugin_info = false;
            $this->vprint("Checking File:  ".$file."\n", 4);
            // This tells the cache what file to use
            $this->incFile = $realFile;
            try {
                if ($this->_checkFile($realFile)) {
                    // These files might need to be included more than once,
                    // so we use include
                    $freturn = include_once $realFile;
                    // Register a possible class.  This will try to register
                    // a class whether or not the file actually included
                    // It will fail silently if it doesn't exist
                    $class = $this->_stripFileExtension($file);
                    $this->registerClass($class);
                }
            } catch (ErrorException $e) {
                $this->vprint("Caught Error: ".$e->getMessage()."\n", 1);
                $freturn = false;
            } catch (Exception $e) {
                $this->vprint("Caught Exception: ".$e->getMessage()."\n", 1);
                $freturn = false;
            }

            // Have to make sure that this unset
            unset($this->incFile);

            // Bad things happened.  ;)
            if (!$freturn) {
                $this->vprint($freturn, 4);
                $this->vprint("\tErrors encountered parsing file.");
                $this->vprint("Skipping ".$file.".\n", 4);
                return;
            }
        } else {
            $this->vprint("Cache Hit: $realFile\n");
             // Have to make sure that this unset.  Otherwise could we double cache
            unset($this->incFile);
            $this->_setPluginVar($cache[$realFile]);
        }
        $this->file_count++;

    }


    /**
    * Strips the extension off of a file
    *
    * @param string $name The name of the file to be stripped
    *
    * @return string The stripped name
    */
    private function _stripFileExtension($name)
    {
        $pos = strpos($name, $this->extension);
        if ($pos > 0) {
            $pos-=1;  // Remove the trailing .
        }
        return substr($name, 0, $pos);
    }

    /**
    * Tries to register a class
    *
    * The class has to have a public static function 'register' or a public
    * static variable "register" that returns the correct array for
    * addGenericRaw.
    *
    * @param string $name The name of the class to try and register
    *
    * @return bool
    */
    public function registerClass($name)
    {
        if (!class_exists($name)) {
            return false;
        }
        $reg = eval(
            "if (isset($name::\$registerPlugin)) return $name::\$registerPlugin;"
        );
        if (method_exists($name, "registerPlugin")) {
            $reg = eval("return $name::registerPlugin();");
        }
        if (!empty($reg) && empty($reg["Class"])) {
            $reg["Class"] = ucfirst($name);
        }
        return $this->_addClassRaw($reg);
    }
    /**
    *  Deals with the plugin files.
    *
    * Checks the syntax of a file if the check is available
    *
    * @param string $file The file to be checked.
    *
    * @return bool
    */
    private function _checkFile($file)
    {
        return true;
    }


    /**
    * Function to register plugin functions.
    *
    * This function sets up the array containing function names and descriptions.
    *   It should not be called directly.
    *
    * @param array $info This must at least contain info["Name"] which must be
    *     the name of a valid function, or the plugin won't be registered.
    *     Anything else is stored with the name of the function in case it
    *     should ever be needed.
    *
    * @return null
    */
    function registerFunctionRaw($info)
    {
        $this->vprint("\tRegistering Function:  ".$info["Name"], 4);
        $this->vprint("\t\tType:  ".$info["Types"]."\t\t", 4);
        $plugins = array();
        if (!is_array($info)) {
            $this->vprint(" Failed (Bad arguments to registerFunction)", 4);
            return;
        }
        if (empty($info["Name"])) {
            $this->vprint(" Failed (Name not set), 4");
            return;
        }
        if (!function_exists($info["Name"])) {
            $this->vprint(" Failed (Function doesn't Exist)", 4);
            return;
        }
        if (is_array($info["Types"])) {
            foreach ($info["Types"] as $Type) {
                $plugins["Functions"][$Type][] = $info;
            }
        } else {
            if (trim($info["Types"]) != "") {
                foreach (explode(",", $info["Types"]) as $Type) {
                    $plugins["Functions"][trim($Type)][] = $info;
                }
            } else {
                $plugins["Functions"]["ALL_TYPES"][] = $info;
            }
        }
        $this->_setPluginVar($plugins);
        $this->vprint(" Done!", 4);
        $this->function_count++;
        $this->plugin_count++;
        $this->vprint("\n", 4);
    }

    /**
    * Adds a generic plugin to the list of valid plugins.
    *
    * Copys the info parameter into its array of generic plugins.
    *
    * @param array $info this is all of the information about the plugin.
    *
    * @return null
    */
    private function _setPluginVar($info)
    {
        if (!empty($this->incFile)) {
            $this->incFileCache[$this->incFile] = $info;
        }
        foreach ($info as $cat => $catInfo) {
            foreach ($catInfo as $type => $plugins) {
                foreach ($plugins as $key => $plugin) {
                    $this->plugins[$cat][$type][] = $plugin;
                }
            }
        }
    }

    /**
    * Adds a generic plugin to the list of valid plugins.
    *
    * Copys the info parameter into its array of generic plugins.
    *
    * @param array $info this is all of the information about the plugin.  Only
    *     info["Name"] is required.  Everything else is application dependent.
    *     These Plugins allow for doing almost anything.
    *
    * @return null
    */
    function addGenericRaw($info)
    {
        $info["Name"] = trim($info["Name"]);
        if (!isset($info["Type"])) {
            $info["Type"] = "ALL_TYPES";
        }
        $this->vprint("\tRegistering Generic:  ".$info["Name"], 4);
        $this->vprint("\t\tType:  ".$info["Type"], 4);
        if ($info["Name"] != "") {
            $this->vprint("  Done", 4);
            $plugins["Generic"][$info["Type"]][] = $info;
            $this->_setPluginVar($plugins);
            $this->generic_count++;
            $this->plugin_count++;
        } else {
            $this->vprint("  Failed", 4);
        }
        $this->vprint("\n", 4);
    }

    /**
    * Adds a generic plugin to the list of valid plugins.
    *
    * Copys the info parameter into its array of generic plugins.
    *
    * @param array $info this is all of the information about the plugin.  Only
    *     info["Name"] is required.  Everything else is application dependent.
    *     These Plugins allow for doing almost anything.
    *
    * @return null
    */
    private function _addClassRaw($info)
    {
        $info["Name"] = trim($info["Name"]);
        if (!isset($info["Type"])) {
            return;
        }
        $this->vprint("\tRegistering Class:  ".$info["Name"], 4);
        $this->vprint("\t\tType:  ".$info["Type"], 4);
        if ($info["Name"] != "") {
            $this->vprint("  Done", 4);
            $plugins["Class"][$info["Type"]][] = $info;
            // This is for backward compatibility
            $plugins["Generic"][$info["Type"]][] = $info;
            $this->_setPluginVar($plugins);
            $this->class_count++;
            $this->plugin_count++;
        } else {
            $this->vprint("  Failed", 4);
        }
        $this->vprint("\n", 4);
    }


    /**
    *  Used to compare Plugins for sorting.
    *
    * This function compares the names of the Plugins and returns the output
    * needed by usort.  It should only be used for this purpose.
    *
    * @param array $a The first argument for the compare
    * @param array $b The second argument for the compare
    *
    * @return null
    */
    public function comparePlugins($a, $b)
    {
        return strnatcasecmp($a["Name"], $b["Name"]);
    }

}

?>
