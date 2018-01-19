<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** We might need things from this class */
require_once dirname(__FILE__)."/Devices.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../../interfaces/DBTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Firmware extends \HUGnet\db\Table
    implements \HUGnet\interfaces\DBTable
{
    /** These are the constants for RelStatus */
    /** @var int Released code */
    const RELEASE = 0;
    /** @var int Released code */
    const BETA = 2;
    /** @var int Released code */
    const DEV = 8;
    /** @var int Released code */
    const BAD = 64;
    /** @var array This is for looking up RelStatus from the old way*/
    static protected $relStatus = array(
        "BAD" => self::BAD,
        "BETA" => self::BETA,
        "DEV" => self::DEV,
        "RELEASE" => self::RELEASE,
    );

    /** @var string This is the table we should use */
    public $sqlTable = "firmware";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "id";
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "Version DESC";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "INTEGER",
            "AutoIncrement" => true,
            "Primary" => true,
        ),
        "Version" => array(
            "Name" => "Version",
            "Type" => "varchar(8)",
            "Default" => '',
        ),
        "Code" => array(
            "Name" => "Code",
            "Type" => "longtext",
            "Default" => '',
        ),
        "CodeHash" => array(
            "Name" => "CodeHash",
            "Type" => "varchar(64)",
            "Default" => '',
        ),
        "Data" => array(
            "Name" => "Data",
            "Type" => "longtext",
            "Default" => '',
        ),
        "DataHash" => array(
            "Name" => "DataHash",
            "Type" => "varchar(64)",
            "Default" => '',
        ),
        "FWPartNum" => array(
            "Name" => "FWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "HWPartNum" => array(
            "Name" => "HWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => '0',
        ),
        "FileType" => array(
            "Name" => "FileType",
            "Type" => "varchar(4)",
            "Default" => 'SREC',
        ),
        "RelStatus" => array(
            "Name" => "RelStatus",
            "Type" => "tinyint(4)",
            "Default" => self::BAD,
        ),
        "Tag" => array(
            "Name" => "Tag",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "Target" => array(
            "Name" => "Target",
            "Type" => "varchar(16)",
            "Default" => '',
        ),
        "Active" => array(
            "Name" => "Active",
            "Type" => "tinyint(4)",
            "Default" => 1,
        ),
        "md5" => array(
            "Name" => "md5",
            "Type" => "varchar(64)",
            "Null" => true,
        ),
    );
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    */
    public $sqlIndexes = array(
        "Version" => array(
            "Name" => "Version",
            "Unique" => true,
            "Columns" => array(0 => "HWPartNum", 1 => "FWPartNum", 2 => "Version")
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
        "filename" => "",
    );
    /** @var array This is where the data is stored */
    protected $data = array();
    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        parent::__construct($system, $data, $connect);
        $this->create();
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        parent::fromArray($array);
        $DataHash = $this->get("DataHash");
        if (empty($DataHash)) {
            $this->set("DataHash", md5($this->get("Data")));
        }
        $CodeHash = $this->get("CodeHash");
        if (empty($CodeHash)) {
            $this->set("CodeHash", md5($this->get("Code")));
        }
    }
    /**
    * Returns an array with only the values the database cares about
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toDB($default = true)
    {
        $array = parent::toDB($default);
        if ($array["HWPartNum"] == "0039-21") {
            if (trim(strtolower($array["Target"])) == "atmega16") {
                $array["HWPartNum"] = "0039-21-01";
            } else if (trim(strtolower($array["Target"])) == "atmega324p") {
                $array["HWPartNum"] = "0039-21-02";
            }
        }
        return (array)$array;
    }
 
    /**
    * Checks the hash of the data and code.
    *
    * @return bool True if hash is good, false otherwise
    */
    public function checkHash()
    {
        if (($this->get("DataHash") != md5($this->get("Data")))
            || ($this->get("CodeHash") != md5($this->get("Code")))
        ) {
            return false;
        }
        return true;
    }
    /**
    * This function outputs this firmware into a file that can be stored on
    * a web site.
    *
    * @param string $path      Where to store the file
    * @param string &$filename The filename to use.  Gets set to the filename used
    * @param string $fileextra Extra stuff to append to the file name
    *
    * @return bool True on success, false on failure
    */
    public function toFile($path = ".", &$filename = null, $fileextra = "")
    {
        if (is_null($filename) || empty($filename)) {
            $filename  = str_replace("-", "", $this->get("HWPartNum"));
            $filename .= "-".str_replace("-", "", $this->get("FWPartNum"));
            $filename .= "-".$this->get("Version").$fileextra.".gz";
        }
        return (bool)file_put_contents(
            $path."/".$filename,
            gzencode((string)$this)
        );
    }
    /**
    * This function outputs this firmware into a file that can be stored on
    * a web site.
    *
    * @param string $file The filename to use.  Could be an md5sum line also
    * @param string $path Where to get the file
    *
    * @return bool True on success, false on failure
    */
    public function fromFile($file, $path = ".")
    {
        $this->set("filename", $file);
        $stuff = file_get_contents($path."/".$this->get("filename"));
        if (empty($stuff)) {
            return false;
        }
        // If the md5 is set and bad, fail
        $md5 = $this->get("md5");
        if (!empty($md5) && ($md5 != md5($stuff))
        ) {
            return false;
        }
        $filename = $this->get("filename");
        if (!empty($filename)) {
            file_put_contents(sys_get_temp_dir()."/".$filename, $stuff);
            $stuff = implode("", gzfile(sys_get_temp_dir()."/".$filename));
            @unlink(sys_get_temp_dir()."/".$filename);
            $this->fromString($stuff);
            $this->set("md5", $md5);
            $this->set("filename", $file);
            unset($stuff);
        }
        return true;
    }
    /**
    * This function checks to see if a file exists in the database
    *
    * @param string $filename The filename or MD5 line.
    *
    * @return bool True on success, false on failure
    */
    public function checkFile($filename)
    {
        $this->clearData();
        if (is_null($filename)) {
            return false;
        }
        $this->set("filename", $filename);
        $fname = explode("-", $this->get("filename"));
        $this->set("HWPartNum", $fname[0]);
        $this->set("FWPartNum", $fname[1]);
        $this->set("Version", $fname[2]);
        $where = array(
            "HWPartNum" => $this->get("HWPartNum"),
            "FWPartNum" => $this->get("FWPartNum"),
            "Version"   => $this->get("Version")
        );
        $md5 = $this->get("md5");
        if (!empty($md5)) {
            $where["md5"] = $md5;
        }
        $this->dbDriver()->selectWhere($where);
        $this->dbDriver()->fetchInto();
        return  !is_null($this->get("id"));
    }
    /**
    * Checks to see if our deviceID exists in the database
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists()
    {

        return (bool) $this->dbDriver()->countWhere(
            array(
                "FWPartNum" => $this->get("FWPartNum"), 
                "Version"   => $this->get("Version"),
            ),
            array(),
            "id"
        );
    }
    /**
    * Sets the part number
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    public function fixHWPartNum($value)
    {
        $val = Devices::formatPartNum($value);
        $substr = substr($val, 0, 7);
        if (($substr == "0039-21") && (strlen($val) >= 10)) {
            $substr = trim(substr($val, 0, 10));
        }
        return $substr;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set Date
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDate($value)
    {
        $this->data["Date"] = self::unixDate($value);
    }
    /**
    * function to set RelStatus
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setRelStatus($value)
    {
        if (isset(self::$relStatus[$value])) {
            $value = self::$relStatus[$value];
        }
        $this->data["RelStatus"] = (int)$value;
    }
    /**
    * Sets the part number
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setFWPartNum($value)
    {
        $this->data["FWPartNum"] = Devices::formatPartNum($value);
    }
    /**
    * Sets the part number
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setHWPartNum($value)
    {
        $this->data["HWPartNum"] = $this->fixHWPartNum($value);
    }
    /**
    * Hexifies a version in x.y.z form.
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setVersion($value)
    {
        $this->data["Version"] = Devices::formatVersion($value);
    }
    /**
    * This function gets the filename and md5 sum
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setFilename($value)
    {
        $value = trim($value);
        if ((substr($value, 0, 3) == "MD5") || (stristr($value, " ") !== false)) {
            preg_match(
                "/([0-9]{6,8}\-){1}[0-9]{8}[A-Z]{1}\-[0-9]{1,2}\."
                ."[0-9]{1,2}\.[0-9]{1,2}.gz/",
                $value,
                $match
            );
            $this->data["filename"] = $match[0];
            $stuff = explode("=", $value);
            $this->data["md5"] = trim($stuff[1]);
        } else {
            $this->data["filename"] = trim($value);
        }
    }
}
?>
