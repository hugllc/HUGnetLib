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
require_once dirname(__FILE__)."/../interfaces/OutputPluginInterface.php";

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
abstract class OutputPluginBase extends HUGnetClass implements OutputPluginInterface
{
    /** @var This is to register the class */
    public $output = array();

    /**
    * Disconnects from the database
    *
    * @param array $params The parameter array
    * @param array $header The header to use.  This defines the data shown
    */
    public function __construct($params = null, $header = null)
    {
        $this->setParams($params);
        $this->setHeader($header);
    }
    /**
    * This function implements the output before the data
    *
    * @param array $header The array to define the output
    *
    * @return String the text to output
    */
    public function setHeader($header)
    {
        if (is_array($header) && empty($this->header)) {
            $this->header = $header;
        }
    }
    /**
    * This function implements the output before the data
    *
    * @param array $params the parameter array to save
    *
    * @return String the text to output
    */
    protected function setParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $this->setParam($key, $val);
            }
        }
    }
    /**
    * This function implements the output before the data
    *
    * @param string $name The name of the variable
    * @param mixed  $val  The value to set
    *
    * @return String the text to output
    */
    protected function setParam($name, $val)
    {
        if (isset($this->params[$name])) {
            $this->params[$name] = $val;
        }
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toString($default = true)
    {
        return $this->pre().$this->body().$this->post();
    }

    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function body()
    {
        return $this->text;
    }

    /**
    * This function implements the output before the data
    *
    * @return String the text to output
    */
    public function pre()
    {
        return "pre";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function post()
    {
        return "post";
    }

}
?>
