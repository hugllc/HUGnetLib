<?php
/**
 * Tests the device class
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Base
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__)."/../..");
}


/**
 * General test class for database classes
 *
 * @category   Base
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DatabaseTest extends PHPUnit_Framework_TestCase
{
    /** @var string The name of the id column */
    protected $id = "id";

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function setUp()
    {
        if (empty($this->table)) {
            throw new exception(get_class($this)."->table not defined!", -1);
        }
        $this->config["file"] = tempnam(sys_get_temp_dir(), get_class($this));
        $this->file           = $this->config["file"];
        if (!empty($this->id)) {
            $this->config["id"] = $this->id;
        }
        if (!empty($this->table)) {
            $this->config["table"] = $this->table;
        }
        $this->config["servers"][0] = array(
            'host' => 'localhost',
            'user' => '',
            'pass' => '',
            'dsn' => "sqlite:".$this->file,
        );
        HUGnetDB::setConfig($this->config);
        $this->pdo =& HUGnetDB::createPDO($this->config);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function tearDown()
    {
        if (is_object($this->pdo) && (get_class($this->pdo) == "PDO")) {
            $this->pdo->query("delete from ".$this->table);
            $this->pdo = null;
        }
        if (file_exists($this->config["file"])) {
            unlink($this->config["file"]);
        }
        unset($this->config);
    }

    /**
     *  This pre-loads two rows into the database into the database
     *
     * This function has some quirks.  The first element in the array
     * MUST have ALL of the keys you want to enter into the database.
     * The function gets the keys to enter from that.
     *
     * @param array $values array of values to load into the database
     *
     * @return null
     */
    protected function load($values)
    {
        if (count($values) < 1) {
            return;
        }
        $keys   = array_keys($values[0]);
        $sep    = "";
        $qMarks = "";
        foreach ($keys as $k) {
            $qMarks .= $sep." ? ";
            $sep     = ",";
        }
        $query  = "INSERT INTO `".$this->table."`";
        $query .= "(".implode($keys, ",").") VALUES(".$qMarks.");";
        $res    = $this->pdo->prepare($query);
        if (!is_object($res)) {
            return;
        }
        $count = 0;
        foreach ($values as $val) {
            $data = $this->prepareData($val, $keys);
            if ($res->execute($data)) {
                $count++;
            } else {
                var_dump($res->errorInfo());
            }
        }
    }

    /**
     * Returns an array made for the execute query
     *
     * @param array $data The data to prepare
     * @param array $keys The keys to use
     *
     * @return array
     */
    protected function prepareData($data, $keys)
    {
        if (!is_array($keys)) {
            return array();
        }
        $ret = array();
        foreach ($keys as $k) {
            $ret[] = $data[$k];
        }
        return $ret;

    }

    /**
     *  Gets a record from the database
     *
     * @param mixed $id The id of the element to get from the database.
     *
     * @return array
     */
    protected function getSingle($id)
    {
        $query = "SELECT * FROM ".$this->table." WHERE ".$this->id."=".(int)$id.";";
        $ret   = $this->pdo->query($query);
        if (!is_object($ret)) {
            return array();
        }
        $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
        return $ret[0];
    }

    /**
     * Gets all of the database records
     *
     * @return array
     */
    protected function getAll()
    {
        $query = "SELECT * FROM `".$this->table."`;";
        $res   = $this->pdo->query($query);
        if (!is_object($res)) {
            var_dump($query);
            var_dump($this->pdo->errorInfo());
            return false;
        }
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Test the table variable
     *
     * @return null
     */
    public function testTableString()
    {
        $table = $this->readAttribute($this->o, "table");
        $this->assertType("string", $table);
    }

    /**
     * Test the table variable
     *
     * @return null
     */
    public function testTableEmpty()
    {
        $table = $this->readAttribute($this->o, "table");
        $this->assertFalse(empty($table));
    }

    /**
     * Test the table variable
     *
     * @return null
     */
    public function testIdString()
    {
        $id = $this->readAttribute($this->o, "id");
        $this->assertType("string", $id);
    }

    /**
     * Test the table variable
     *
     * @return null
     */
    public function testIdEmpty()
    {
        $id = $this->readAttribute($this->o, "id");
        $this->assertFalse(empty($id));
    }


    /**
     * This function tests to see if there are any fields defined
     *
     * @return null
     */
    function testFieldCount()
    {
        $columns = $this->readAttribute($this->o, "_columns");
        $fields  = $this->readAttribute($this->o, "fields");
        $this->assertSame(
            $columns,
            count($fields),
            "Table was either not built or modified."
        );
    }

}

?>
