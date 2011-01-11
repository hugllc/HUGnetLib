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
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
/** This is for the base class */
require_once dirname(__FILE__)."/GenericMock.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Mocks
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Graph extends GenericMock
{

    /** @var array The stuff to build */
    public $subs = array(
        "legend", "title", "img", "xgrid", "xaxis", "yaxis", "y2axis",
    );
    /** @var array The stuff to build */
    public $titles = array(
        "xaxis", "yaxis", "y2axis"
    );
    /**
    * Build everything
    *
    */
    public function __construct()
    {
        foreach ($this->subs as $sub) {
            $this->$sub = new GenericMock();
        }
        foreach ($this->titles as $title) {
            $this->$title->title = new GenericMock();
        }
        $this->construct = func_get_args();
    }
    /**
    * Build everything
    *
    * @return None
    */
    public function stroke()
    {
        foreach ($this->subs as $sub) {
            $this->calls[$sub] = $this->$sub->calls;
        }
        foreach ($this->titles as $title) {
            $this->calls[$title]["title"] = $this->$title->title->calls;
        }
    }
    /**
    * Build everything
    *
    * @param array $Line The configuration array.
    *
    * @return None
    */
    public function add(GenericMock $Line)
    {
        $this->calls["Add"][] = $Line->calls;
    }
    /**
    * Build everything
    *
    * @param array $Line The configuration array.
    *
    * @return None
    */
    public function addY2(GenericMock $Line)
    {
        $this->calls["AddY2"][] = $Line->calls;
    }
}

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Mocks
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LinePlot extends GenericMock
{
}