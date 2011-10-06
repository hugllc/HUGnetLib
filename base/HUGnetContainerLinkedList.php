<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/HUGnetContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetContainerLinkedList extends HUGnetContainer
{
    /** @var object The link to the next element */
    public $next;
    /** @var object The link to the prev element */
    public $prev;

    /**
    * This is the constructor
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param object &$next The next object in the list
    * @param object &$prev The previous object in the list
    */
    function __construct($data="", &$next=null, &$prev=null)
    {
        parent::__construct($data, $next, $prev);
    }
    /**
    * Create a link on the 'next' var
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $extra This should be an extension of the devInfo object
    *
    * @return bool True on success
    */
    public function linkNext($data="", $extra=null)
    {
        return $this->_link("next", "prev", $data, $extra);
    }
    /**
    * Create a link on the 'prev' var
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $extra This should be an extension of the devInfo object
    *
    * @return bool True on success
    */
    public function linkPrev($data="", $extra=null)
    {
        return $this->_link("prev", "next", $data, $extra);
    }
    /**
    * break the next link
    *
    * @return bool True on success
    */
    public function breakNext()
    {
        return $this->_break("next", "prev");
    }
    /**
    * Break the previous link
    *
    * @return bool True on success
    */
    public function breakPrev()
    {
        return $this->_break("prev", "next");
    }
    /**
    * Add a link in the chain, keeping the chain intact
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $extra This should be an extension of the devInfo object
    *
    * @return bool True on success
    */
    public function insertNext($data="", $extra=null)
    {
        return $this->_link("next", "prev", $data, $extra, true);
    }
    /**
    * Add a link to the chain, keeping the chain intact
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $extra This should be an extension of the devInfo object
    *
    * @return bool True on success
    */
    public function insertPrev($data="", $extra=null)
    {
        return $this->_link("prev", "next", $data, $extra, true);
    }
    /**
    * Remove a link in the chain keeping the chain intact
    *
    * @return bool True on success
    */
    public function removeNext()
    {
        return $this->_break("next", "prev", true);
    }
    /**
    * Remove a link in a chain, keeping the chain intact
    *
    * @return bool True on success
    */
    public function removePrev()
    {
        return $this->_break("prev", "next", true);
    }

    /**
    * Returns a reference to the first item in the list
    *
    * @return &object
    */
    public function &first()
    {
        $ptr =& $this;
        while (!is_null($ptr->prev)) {
            $ptr =& $ptr->prev;
        }
        return $ptr;
    }

    /**
    * Returns a reference to the first item in the list
    *
    * @return &object
    */
    public function &last()
    {
        $ptr =& $this;
        while (!is_null($ptr->next)) {
            $ptr =& $ptr->next;
        }
        return $ptr;
    }
    /**
    * Returns a pointer to the next object in the list
    *
    * @return &object
    */
    public function &next()
    {
        return $this->next;
    }
    /**
    * Returns a pointer to the next object in the list
    *
    * @return &object
    */
    public function &prev()
    {
        return $this->prev;
    }

    /**
    * Converts the object to a string
    *
    * @param string $var      The name of the variable to link
    * @param string $otherVar The name of the variable to link on the other object
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $extra    This should be an extension of the devInfo object
    * @param bool   $insert   This tells us whether we should insert a record
    *                         between two records or not
    *
    * @return mixed The value of the attribute
    */
    private function _link($var, $otherVar, $data, $extra=null, $insert=false)
    {
        $class = get_class($this);
        $obj = new $class($data, $extra);
        if ($this->_linked($var) && $insert) {
            $obj->$var = &$this->$var;
            $obj->$var->$otherVar = &$obj;
        }
        if (!$this->_linked($var) || $insert) {
            $this->$var = &$obj;
            $obj->$otherVar = &$this;
            return true;
        }
        return false;
    }

    /**
    * Converts the object to a string
    *
    * @param string $var The name of the variable to break
    *
    * @return bool True if the variable is linked
    */
    private function _linked($var)
    {
        $class = get_class($this);
        return is_object($this->$var) && (get_class($this->$var) == $class);
    }
    /**
    * Converts the object to a string
    *
    * @param string $var      The name of the variable to break
    * @param string $otherVar The name of the variable to break on the other object
    * @param bool   $remove   This tells us whether we should remove a record
    *                         from between two records or not
    *
    * @return bool True on success
    */
    private function _break($var, $otherVar, $remove = false)
    {
        if ($remove && is_object($this->$var->$var)) {
            $this->$var = &$this->$var->$var;
            $this->$var->$otherVar = &$this;
            return true;
        }
        if ($this->_linked($var)) {
            $this->$var->$otherVar = null;
        }
        $this->$var = null;
        return true;
    }

}
?>
