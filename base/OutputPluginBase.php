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
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/OutputPluginInterface.php";

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
abstract class OutputPluginBase extends HUGnetClass implements OutputPluginInterface
{
    /** @var This is to register the class */
    public $output = array();

    /**
    * Disconnects from the database
    *
    * @param array $output The array to output
    */
    public function __construct(&$output = null)
    {
        $this->setOutput($output);
    }
    /**
    * This function implements the output before the data
    *
    * @param array $output The array to output
    * 
    * @return String the text to output
    */
    protected function setOutput($output)
    {
        if (!is_null($output)) {
            $this->output = $output;
        }
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
    * @param array $output The array to output
    *
    * @return String the text to output
    */
    public function row($output = null)
    {
        $this->setOutput($output);
        $this->toString();
    }
    /**
    * This function implements the output before the data
    *
    * @return String the text to output
    */
    public function pre()
    {
        $this->text .= "pre";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function post()
    {
        $this->text .= "post";
    }


}
?>
