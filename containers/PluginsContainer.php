<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";

define("HUGNET_PLUGIN_BASE_PATH", realpath(dirname(__FILE__)."/../plugins/"));

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PluginsContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "dir"       => HUGNET_PLUGIN_BASE_PATH, // This is the plugin path
        "extension" => ".php",
    );
    /** @var array The directory to skip. */
    protected $cache = array();
    /** @var array Plugin Functions */
    protected $plugins = array();
    /** @var array This is where information on the plugin files is stored. */
    protected $plugin_info = array();

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        parent::__construct($data);
        $this->getPluginDir($this->dir);
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
    * @param int    $Level     Depreciated
    * @param bool   $recursive Whether to be recursive or not
    *
    * @return null
    */
    protected function getPluginDir(
        $basedir   = ".",
        $Level     = 0
    ) {
        $this->vprint("Checking for Plugins in ".$basedir."\n", 4);
        $plugindir = @opendir($basedir);
        $files = array();
        if ($plugindir) {
            while ($file = readdir($plugindir)) {
                $files[] = $file;
            }
            closedir($plugindir);
        }
        natcasesort($files);
        $count = 0;
        foreach ($files as $file) {
            $file     = str_replace("/", "", trim($file));
            $basefile = realpath($basedir."/".$file);
            if (substr($file, 0, 1) == ".") {
                continue;
            }
            if (is_file($basefile)) {
                $ext = substr($file, (-1*strlen($this->extension)));
                if ($ext == $this->extension) {
                    $this->includeFile($file, $basedir);
                }
            } else if (is_dir($basefile)) {
                $count += $this->getPluginDir(
                    $basefile."/",
                    $Level + 1
                );
            }
        }
        return $count;
    }

    /**
    *  Deals with the plugin files.
    *
    * Includes files and registers any Plugins it finds in those files.
    *
    * @param string $file    The full or relative path to the file to be included.
    * @param string $filedir The filesystem directory where the files are located.
    *
    * @return null
    */
    protected function includeFile($file, $filedir = "")
    {
        $realFile = realpath($filedir.$file);
        if (empty($this->cache[$realFile]) || !is_array($this->cache[$realFile])) {
            $plugin_info = false;
            $this->vprint("Checking File:  ".$file."\n", 4);
            try {
                // These files might need to be included more than once,
                // so we use include
                $freturn = include_once $realFile;
                // Register a possible class.  This will try to register
                // a class whether or not the file actually included
                // It will fail silently if it doesn't exist
                $class = $this->_stripFileExtension($file);
                $this->registerClass($class);
            } catch (ErrorException $e) {
                $this->vprint("Caught Error: ".$e->getMessage()."\n", 1);
                $freturn = false;
            } catch (Exception $e) {
                $this->vprint("Caught Exception: ".$e->getMessage()."\n", 1);
                $freturn = false;
            }

            // Bad things happened.  ;)
            if (!$freturn) {
                $this->vprint($freturn, 4);
                $this->vprint("\tErrors encountered parsing file.");
                $this->vprint("Skipping ".$file.".\n", 4);
                return;
            }
        } else {
            $this->vprint("Cache Hit: $realFile\n", 4);
             // Have to make sure that this unset.  Otherwise could we double cache
            $class = $this->_stripFileExtension($file);
            $this->registerClass($class);
        }
        $this->file_count++;

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
    private function registerClass($name)
    {
        if (!class_exists($name)) {
            return false;
        }
        $reg = eval(
            "return $name::\$registerPlugin;"
        );
        if (empty($reg) && method_exists($name, "registerPlugin")) {
            $reg = eval("return $name::registerPlugin();");
        }
        if (!is_array($reg) || empty($reg)) {
            return false;
        }
        if (empty($reg["Flags"])) {
            $reg["Flags"] = array($reg["Name"]);
        }
        foreach ((array)$reg["Flags"] as $flag) {
            $this->plugins[$reg["Type"]][$flag] = $reg;
        }
        return true;
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
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = false)
    {
        return parent::toString($default);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = false)
    {
        return parent::toArray($default);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

    /**
    * function to set To
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setPluginExtension($value)
    {
        $value = (string)$value;
        if (substr($value, 0, 1) !== ".") {
            $value = ".".$value;
        }
        $this->data["PluginExtension"] = $value;
    }

}
?>
