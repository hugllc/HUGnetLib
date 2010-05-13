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
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetDBTable.php";
/** This is for the configuration */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceParamsContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceSensorsContainer.php";
require_once dirname(__FILE__)."/DevicesTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FirmwareTable extends HUGnetDBTable
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
        "Data" => array(
            "Name" => "Data",
            "Type" => "longtext",
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
            "Type" => "datetime",
            "Default" => '1970-01-01 00:00:00',
        ),
        "FileType" => array(
            "Name" => "FileType",
            "Type" => "varchar(4)",
            "Default" => 'SREC',
        ),
        "RelStatus" => array(
            "Name" => "RelStatus",
            "Type" => "tinyint(4)",
            "Default" => self::DEV,
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
            "Columns" => array("FWPartNum", "Version")
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
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        parent::__construct($data);
        $this->create();
    }
    /**
    * Gets the latest firmware for the device
    *
    * @return bool True on success, false on failure
    */
    public function getLatest()
    {
        $data  = array($this->FWPartNum, $this->RelStatus, 0);
        $where  = " FWPartNum = ? AND RelStatus <= ?";
        $where .= " AND Active <> ?";
        if (!empty($this->HWPartNum)) {
            $where .= " AND HWPartNum = ?";
            $data[] = $this->HWPartNum;
        }
        return $this->selectOneInto($where, $data);
    }
    /**
    * Runs a function using the correct driver for the endpoint
    *
    * @param string $ver1 The first version to use in the compare
    * @param string $ver2 The second version to use in the compare
    *
    * @return int -1 if $ver1 < $ver2, 0 if $ver1 == $ver2, 1 if $ver1 > $ver2
    */
    public function compareVersion($ver1, $ver2 = null)
    {
        $useVer2 = (empty($ver2)) ? $this->Version : $ver2;
        $v1 = explode(".", $ver1);
        $v2 = explode(".", $useVer2);
        for ($i = 0; $i < 3; $i++) {
            if ($v1[$i] > $v2[$i]) {
                return 1;
            } else if ($v1[$i] < $v2[$i]) {
                return -1;
            }
        }
        return 0;
    }
    /**
    * This function outputs this firmware into a file that can be stored on
    * a web site.
    *
    * @param string $path Where to store the file
    *
    * @return bool True on success, false on failure
    */
    public function toFile($path = ".")
    {
        $filename  = str_replace("-", "", $this->FWPartNum)."-".$this->Version.".gz";
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
        $this->filename = $file;
        $stuff = implode("", gzfile($path."/".$this->filename));
        if (empty($stuff)) {
            return false;
        }
        $this->fromString($stuff);
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
        $this->filename = $filename;
        $this->FWPartNum = substr($this->filename, 0, 9);
        $this->Version = substr($this->filename, 10, 8);
        $where = "`FWPartNum` = ? AND `Version` = ?";
        $data = array($this->FWPartNum, $this->Version);
        if (!empty($this->md5)) {
            $where .= " AND `md5` = ?";
            $data[] = $this->md5;
        }
        $ret = $this->myDriver->selectWhere($where, $data);
        $this->myDriver->fetchInto();
        return  !is_null($this->id);
    }
    /**
    * Checks to see if our deviceID exists in the database
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists()
    {

        return (bool) $this->myDriver->countWhere(
            "FWPartNum = ? AND Version = ?",
            array($this->FWPartNum, $this->Version),
            "id"
        );
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
        $this->data["Date"] = $this->sqlDate($value);
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
        $this->data["FWPartNum"] = DevicesTable::formatPartNum($value);
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
        $this->data["HWPartNum"] = substr(DevicesTable::formatPartNum($value), 0, 7);
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
        $this->data["Version"] = DevicesTable::formatVersion($value);
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
        if (substr($value, 0, 3) == "MD5") {
            preg_match(
                "/[0-9]{8}[A-Z]{1}\-[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}.gz/",
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
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    public function getCode($empty="FF")
    {
        return $this->_interpSREC($this->Code);
    }
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    public function getData($empty="FF")
    {
        return $this->_interpSREC($this->Data);
    }
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $srec  The S record to change.
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    private function _interpSREC($srec, $empty="FF")
    {
        // Put the srec into the buffer
        $srec = explode("\n", $srec);
        foreach ((array)$srec as $rec) {
            if (substr($rec, 0, 2) == "S1") {
                // Set up all the stuff to put into the buffer
                $size  = hexdec(substr($rec, 2, 2));
                $size -= 3;
                $addr  = hexdec(substr($rec, 4, 4));
                $data  = substr($rec, 8, ($size*2));
                // Make sure the buffer is big enough for the data
                $buffer = str_pad($buffer, ($addr + $size)*2, $empty, STR_PAD_RIGHT);
                // Put the data into the buffer
                $buffer = substr_replace($buffer, $data, $addr*2, $size*2);
            }
        }
        // remove the extra
        while (substr($buffer, -2) == $empty) {
            $buffer = substr($buffer, 0, -2);
        }
        // return the buffer
        return $buffer;
    }
}
?>
