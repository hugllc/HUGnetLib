<?php
/**
 * Tests the unit conversion class
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
 * @category   UnitConversion
 * @package    HUGnetLibTest
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

require_once dirname(__FILE__).'/../unitConversion.php';
require_once dirname(__FILE__).'/../base/UnitBase.php';
require_once dirname(__FILE__).'/../lib/plugins.inc.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class UnitConversionMock extends unitConversion
{
    /** The units array */
    var $units = array();

    /**
     * This registers the sensor Plugins so we know what code we have available.
     *
     * @param object &$plugins This is a object of type plugin
     *
     * @see plugin
      */
    function __construct(&$plugins = "")
    {
        $this->registerUnits(array("Class" => "test1Units", "Name" => "Test"));
        $this->registerUnits(array("Class" => "test2Units", "Name" => "Test2"));
    }


}
/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test1Units extends UnitBase
{
    var $units = array(
        'A' => array(
            'longName' => 'A',
            'varType' => 'float',
            'convert' => array(
                'B' => 'aToB',
                'C' => 'aToC',
            ),
        ),
        'B' => array(
            'longName' => 'B',
            'varType' => 'float',
            'convert' => array(
                'A' => 'bToA',
                'C' => 'bToC',
            ),
            'preferred' => 'A',
        ),
        'C' => array(
            'longName' => 'C',
            'varType' => 'float',
            'convert' => array(
                'A' => 'cToA',
                'B' => 'cToB',
            ),
            'preferred' => 'A',
        ),
    );
    /**
     * Converts units A to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToB($W, $time, $type)
    {
        return 2*$W;
    }

    /**
     * Converts units B to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToA($W, $time, $type)
    {
        return $W/2;
    }
    /**
     * Converts units A to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToC($W, $time, $type)
    {
        return 4*$W;
    }

    /**
     * Converts units C to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToA($W, $time, $type)
    {
        return $W/4;
    }
    /**
     * Converts units B to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToC($W, $time, $type)
    {
        return 10*$W;
    }

    /**
     * Converts units C to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToB($W, $time, $type)
    {
        return $W/10;
    }

}

/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test2Units extends UnitBase
{
    var $units = array(
        'D' => array(
            'longName' => 'D',
            'varType' => 'float',
            'convert' => array(
                'E' => 'aToB',
                'F' => 'aToC',
            ),
        ),
        'E' => array(
            'longName' => 'E',
            'varType' => 'float',
            'convert' => array(
                'D' => 'bToA',
                'F' => 'bToC',
            ),
            'preferred' => 'D',
        ),
        'F' => array(
            'longName' => 'C',
            'varType' => 'float',
            'convert' => array(
                'D' => 'cToA',
                'E' => 'cToB',
            ),
        ),
    );
    /**
     * Converts units A to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToB($W, $time, $type)
    {
        return 2*$W;
    }

    /**
     * Converts units B to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToA($W, $time, $type)
    {
        return $W/2;
    }
    /**
     * Converts units A to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToC($W, $time, $type)
    {
        return 4*$W;
    }

    /**
     * Converts units C to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToA($W, $time, $type)
    {
        return $W/4;
    }
    /**
     * Converts units B to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToC($W, $time, $type)
    {
        return 10*$W;
    }

    /**
     * Converts units C to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToB($W, $time, $type)
    {
        return $W/10;
    }


}
/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test3Units extends UnitBase
{
    // This is a string for a reason.  Leave it that way.  ;)
    // This is to test what happens if a bad units variable is used.
    var $units = "Bogus Units";
}
?>
