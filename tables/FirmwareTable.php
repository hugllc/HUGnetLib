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
            "Columns" => array("Target", "FWPartNum", "Version")
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /**
    * Gets the latest firmware for the device
    *
    * @return bool True on success, false on failure
    */
    public function getLatest()
    {
        $data  = array($this->FWPartNum, $this->RelStatus, 0, $this->Target);
        $where  = " FWPartNum = ? AND RelStatus <= ?";
        $where .= " AND Active <> ? AND Target = ?";
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
                return(1);
            } else if ($v1[$i] < $v2[$i]) {
                return(-1);
            }
        }
        return(0);

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
    * function to set RelStatus
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setHWPartNum($value)
    {

        $ret = preg_match(
            "/[0-9]{4}-[0-9]{2}/",
            $value,
            $match
        );
        if ($ret > 0) {
            $this->data["HWPartNum"] = $match[0];
        }
    }
}
?>
