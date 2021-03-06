<?php 
/**
 * This file houses the Display class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */


/** This is the HUGnet namespace */
namespace HUGnet\ui;

/** This is our base class */
require_once dirname(__FILE__)."/CLI.php";
/** This is our base class */
require_once dirname(__FILE__)."/Daemon.php";

/**
 * This code tests, serializes and programs HUGnetLab endpoints with 
 * bootloader code.
 *
 * This is an endpoint test class, essentially.  It loads an endpoint without
 * test firmware, runs the tests, writes the serial number and hardware version
 * and then programs the bootloader firmware into the endpoint.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @author     Jeff Liesmaki <jeffl@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Displays extends CLI
{

    private $_system;
    private $_headerWidth = 60;

    /*
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        parent::__construct($config);
        $sys = $this->system();
        $this->_system = &$sys;

    }

    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    *
    * @return object
    */
    static public function &factory(&$config = array())
    {
        $obj = new Displays($config);
        return $obj;
    }


    /**
    ************************************************************
    * Clear Screen Routine
    * 
    * This function clears screen area by outputting 24 carriage 
    * returns and line feeds.
    *
    * @return void
    * 
    */
    public static function clearScreen()
    {

        system("clear");
    }

    /**
    ************************************************************
    * Set Header Width 
    *
    * This function allows the setting of the width for the 
    * box of stars which surrounds the headers.
    *
    * @param $width an integer value between 40 and 100
    *
    * @return void
    */
    public function displaySetHeaderWidth($width)
    {
        if ($width >= 40 && $width <= 100) {
            $this->_headerWidth = $width;
        }

    }

    /**
    *******************************************************
    * Multiple Line Heading
    *
    * this function prints out the multiple line 
    * heading passed to it inside a box of stars.
    *
    * @param $heaingsArray  an array of heading strings
    *
    * @return void
    **/
    public function displayMLHeader($headingsArray=array())
    {

        $items = count($headingsArray);


        for ($i=0; $i < $items; $i++) {

            $length = strlen($headingsArray[$i]);

            /* if heading is too long for box then truncate it */
            /* and get the length of truncated string.         */
            if ($length > ($this->_headerWidth - 4)){
                $headingsArray[$i] = substr($headingsArray[$i], 0,
                                    ($this->_headerWidth -4));
                $length = strlen($headingsArray[$i]);
            }

            /* if not divisible by 2, then add a space */
            if (($length % 2) != 0) {
                $headingsArray[$i] .= " ";
                $length++;
            }

            $remainder = $this->_headerWidth - $length;

            $blankspc = $remainder/2 -1;

            $outstring = "*";
            for ($j=0;$j<$blankspc;$j++) {
                $outstring .= " ";
            }

            $outstring .= $headingsArray[$i]; 
            for ($j=0;$j<$blankspc;$j++) {
                $outstring .= " ";
            }

            $headingsArray[$i] = $outstring."*";

        }

        $this->_system->out(str_repeat("*", $this->_headerWidth));
        $this->_system->out("*".str_repeat(" ", ($this->_headerWidth-2))."*");

        for ($j=0; $j<$items; $j++) {
            $this->_system->out($headingsArray[$j]);
        }
        $this->_system->out("*".str_repeat(" ", ($this->_headerWidth-2))."*");
        $this->_system->out(str_repeat("*", $this->_headerWidth));

        $this->_system->out("\n\r");

    }


    /**
    ********************************************************
    * Print Header Routine
    *
    * This function prints out the heading passed to it
    * inside a box of stars.
    *
    * @param $heading string contain heading text
    *
    * @return void
    */
    public function displayHeader($heading)
    {
        $length = strlen($heading);

        /* if heading is too long for box then truncate it */
        /* and get the length of truncated string.         */
        if ($length > ($this->_headerWidth - 4)){
            $heading = substr($heading, 0, ($this->_headerWidth -4));
            $length = strlen($heading);
        }

        /* if not divisible by 2, then add a space */
        if (($length % 2) != 0) {
            $heading .= " ";
            $length++;
        }

        $remainder = $this->_headerWidth - $length;

        $blankspc = $remainder/2 -1;

        $outstring = "*";
        for ($i=0;$i<$blankspc;$i++) {
            $outstring .= " ";
        }

        $outstring .= $heading;
        for ($i=0;$i<$blankspc;$i++) {
            $outstring .= " ";
        }
        $outstring .= "*";

        $this->_system->out(str_repeat("*", $this->_headerWidth));
        $this->_system->out("*".str_repeat(" ", ($this->_headerWidth-2))."*");

        $this->_system->out($outstring);
        $this->_system->out("*".str_repeat(" ",($this->_headerWidth-2))."*");
        $this->_system->out(str_repeat("*", $this->_headerWidth));

        $this->_system->out("\n\r");

    }

    /**
    ***********************************************************
    * Display Small Header Routine
    *
    * This function displays the single line heading in a 
    * small box of stars.
    *
    */
    public function displaySMHeader($heading)
    {
        $length = strlen($heading);

        /* truncate heading if too long */
        if ($length > 56) {
            $heading = substr($heading, 0, 56);
            $length = strlen($heading);
        }
        
        $hwidth = $length + 4;
        $outstring = "* ";

        $outstring .= $heading;
        $outstring .= " *";

        $this->_system->out(str_repeat("*", $hwidth));
        $this->_system->out($outstring);
        $this->_system->out(str_repeat("*", $hwidth));
        $this->_system->out("\n\r");

    }



    /**
    ************************************************************
    * Display Header and Menu Routine
    *
    * This function displays the test and program tool header
    * and a menu which allows you to exit the program.
    *
    * @return string $choice menu selection
    */
    public function displayMenu($heading, $menuArray)
    {
        $this->displayHeader($heading);

        $items = count($menuArray);

        for ($i = 0;$i < $items; $i++) {
            /* convert numbers to capital letters */
            $menuChar = chr($i+65);

            $menuItem = $menuChar." ) ".$menuArray[$i];
            $this->_system->out($menuItem);
        }
        
        /* convert number to capital letter */
        $menuChar = chr($i+65);
        $menuItem = $menuChar." ) Exit";
        $this->_system->out($menuItem."\n\r");

        
        $choice = readline("\n\rEnter Choice(A - ".$menuChar."): ");
        
        return $choice;
    }



    /**
    ************************************************************
    * Display Passed Routine
    *
    * This function displays the passed message in a
    * visually obvious way so the user cannot miss it.
    *
    * @return void
    *
    */
    public function displayPassed()
    {
        $this->_system->out("\n\r");

        $this->_system->out("**************************************************");
        $this->_system->out("*                                                *");
        $this->_system->out("*                P A S S E D !                   *");
        $this->_system->out("*                                                *");
        $this->_system->out("**************************************************");

        $this->_system->out ("\n\r");

    }


    /**
    ************************************************************
    * Display Failed Routine
    *
    * This function displays the failed message in a
    * visually obvious way so the user cannot miss it.
    *
    * @return void
    *
    */
    public function displayFailed()
    {
        $this->_system->out("\n\r");

        $this->_system->out("**************************************************");
        $this->_system->out("*                                                *");
        $this->_system->out("*                F A I L E D !                   *");
        $this->_system->out("*                                                *");
        $this->_system->out("**************************************************");

        $this->_system->out ("\n\r");

    }



}


?>
 