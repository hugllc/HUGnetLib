<?php
/**
 * Class for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Jeff Liesmaki <jeffl@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is the HUGnet namespace */
namespace HUGnet\devices;


/**
 * Device properties.
 *
 * This is a test program for reading the devices.xml file into a data structure.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Jeff Liesmaki <jeffl@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.1
 */
class Properties
{
    private $_Xml;

    private $EpArray = array();
    private $DbArray = array();
    


    /**
    *********************************************************************
    * this function loads the devices xml file into an object and loads  
    * the arrays of endpoints and daughterboardd.
    *
    * @return void
    */
    protected function __construct($filename)
    {
        if (is_null($filename)) {
            $filename = dirname(__FILE__)."/../devices.xml";
        }

        $this->_Xml = simplexml_load_file($filename);

        /* fill the endpoint list array */
        $count = $this->_Xml->endpoints->count();
        for ($i=0; $i<$count; $i++) {
            $this->EpArray[$i] = (string) ($this->_Xml->endpoints[$i]->HWPartNum);
        }

        /* fill the daughterboard list array */
        $count = $this->_Xml->daughterboards->count();
        for($i = 0; $i < $count; $i++) {
            $this->DbArray[$i] = (string) ($this->_Xml->daughterboards[$i]
                ->HWPartNum);
        }

    } /* end function __construct */


    /**
    *********************************************************************
    * this function instantiates the class object and returns the object
    * to the caller.
    *
    * @return object device data object.
    *
    */
    public static function &factory($filename = null)
    {

        $object = new Properties($filename);
        return $object;

    }


    /**
    ********************************************************************
    * this function returns the list of endpoints available in 
    * the devices.xml file.
    *
    * @return array - $EpArray a list of endpoint hardware part numbers.
    *
    */
    public function getEndpoints()
    {

        return $this->EpArray;

    }

    /**
    ********************************************************************
    * this function returns the list of daughterboards available in 
    * the devices.xml file.
    *
    * @return array - $DbArray, a list of daughterboard hardware part
    *                            numbers.
    *
    */
    public function getDaughterboards()
    {

        return $this->DbArray;

    }


    /**
    *********************************************************************
    * this function returns a list of the pin names and their functions  
    * for the given endpoint.
    *
    * @param string $epName - hardware part number for the endpoint.
    *
    * @return array $pinArray - array of type string containing pin 
    *                           names.
    *
    */
    public function epPinList($epName)
    {

        $pinArray = array();
        $epCount = count($this->EpArray);
        $found = 0;

        for ($i=0; $i<$epCount; $i++) {
            if($this->EpArray[$i] == $epName) {
                $pinCount = $this->_Xml->endpoints[$i]->Pins->count();
                if ($pinCount == 0) {
                    $found = 1;
                }
                for ($j=0; $j< $pinCount; $j++) {
                    $found = 2;
                    $pinName = (string)($this->_Xml->endpoints[$i]->Pins[$j]->name);
                    $pinArray[$j]= $pinName;
                }
            }
        }

        if ($found == 0) {
            $pinArray[0] = "Error";
            $pinArray[1] = "Endpoint not found!";
        } elseif ($found == 1) {
            $pinArray[0] = "Error";
            $pinArray[1] = "No Pins to display!";
        }

        return $pinArray;
    } /* end function epPinList */

    /**
    *********************************************************************
    * this function returns the pin properties for a given endpoint and
    * a given pin name.
    *
    * @param string $epName - hardware part number of the endpoint
    * @param string $pinName - name of the pin 
    *
    * @return array $pinProperties - an array containing:
    *                                       [0] the pin function(s),
    *                                       [1] series resistor value
    *                                       [2] shunt resistor value
    *                                       [3] shunt resistor location 
    *                                       [4] shunt resistor pull
    *                                       [5] high voltage input flag
    *
    */
    public function epPinProperties($epName, $pinName)
    {
        $pinArray = array();
        $epCount = count($this->EpArray);
        $found = 0;

        for ($i=0; $i<$epCount; $i++) {
            if($this->EpArray[$i] == $epName) {
                $found = 1;
                $pinCount = $this->_Xml->endpoints[$i]->Pins->count();
                if ($pinCount == 0) {
                    $found = 2;
                } else {
                    for ($j=0; $j< $pinCount; $j++) {
                        $pName = (string)($this->_Xml->endpoints[$i]->Pins[$j]
                            ->name);
                        if ($pName == $pinName) {
                            $found = 3;
                            $pinFunct = (string)($this->_Xml->endpoints[$i]
                                ->Pins[$j]->function);
                            $pinSeriesResValue = (string)($this->_Xml->
                                endpoints[$i]->Pins[$j]->series);
                            $pinArray[0]= $pinFunct;
                            if ($pinSeriesResValue <> NULL) {
                                $pinShuntResValue = (string)($this->_Xml->
                                    endpoints[$i]->Pins[$j]->shunt->value);
                                $pinArray[1] = $pinSeriesResValue;
                                $pinArray[2] = $pinShuntResValue;
                                if ($pinShuntResValue <> "none") {
                                    $pinShuntResLoc = (string)($this->_Xml->
                                        endpoints[$i]->Pins[$j]->shunt->location);
                                    $pinShuntResPull = (string)($this->_Xml->
                                        endpoints[$i]->Pins[$j]->shunt->pull);
                                    $pinArray[3] = $pinShuntResLoc;
                                    $pinArray[4] = $pinShuntResPull;
                                } else {
                                    $pinArray[3] = "none";
                                    $pinArray[4] = "none";
                                }
                                $pinHighVoltage = (string)($this->_Xml->
                                    endpoints[$i]->Pins[$j]->highvoltage);
                                $pinArray[5] = $pinHighVoltage;
                            }
                        }
                    }
                }
            }
        }

        if ($found <> 3) {
            $pinArray[0] = "Error";

            switch($found) {
                case 0:
                    $pinArray[1] = "Endpoint not found!";
                    break;
                case 1: 
                    $pinArray[1] = "Pin not found!";
                    break;
                case 2:
                    $pinArray[1] = "No Pins to display!";
                    break;
            }
        }
        
        return $pinArray;

    } /* end function epPinPropterties */

    
    /**
    *********************************************************************
    * this function displays the list of pins for a given daughterboard.
    *
    * @param string $dbNum - daughterboard hardware part number.
    *
    * @return array $pinArray - a list of pin names for the daughterboard.
    *
    */
    public function dbPinList($dbNum)
    {
        $pinArray = array();
        $dbCount = count($this->DbArray);
        $found = 0;

        for ($i=0; $i<$dbCount; $i++) {
            if($this->DbArray[$i] == $dbNum) {
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                    for ($j=0; $j< $pinCount; $j++) {
                        $found = 1;
                        $pinName = (string)($this->_Xml->daughterboards[$i]->
                            Pins[$j]->name);
                        $pinArray[$j]= $pinName;
                    }
            }
        }
    
        if ($found == 0) {
            $pinArray[0] = "Error";
            $pinArray[1] = "Daughterboard not found!";
        }

        return $pinArray;
    } /* end function dbPinList */


    /**
    *********************************************************************
    * this function returns the pin function for a given daughter and a
    * give pin name.
    *
    * @param string $dbName - hardware number of the daughterboard
    * @param string $pinName - name of the pin 
    *
    * @return array $pinProperties - a 2 dimensional array containing: 
    *                                   [0][0] the pin function(s)
    *                                   [x][0] connect
    *                                   [x][1] connecting endpoint number
    *                                   [x][2] connecting pin name
    *
    *                                   currently the max connections is
    *                                   2, so values for x would be 1
    *                                   or 2.
    *
    */
    public function dbPinProperties($dbName, $pinName)
    {
        $pinArray = array();
        $dbCount = count($this->DbArray);
        $found = 0;

        for ($i=0; $i<$dbCount; $i++) {
            if($this->DbArray[$i] == $dbName) {
                $found = 1;
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                for ($j=0; $j< $pinCount; $j++) {
                    $pName = (string) ($this->_Xml->daughterboards[$i]->Pins[$j]->
                        name);
                    if ($pName == $pinName) {
                        $found = 2;
                        $pfunct = (string) ($this->_Xml->daughterboards[$i]->
                            Pins[$j]->function);
                        $pinArray[0][0] = $pfunct;
                        $cCount = $this->_Xml->daughterboards[$i]->Pins[$j]->
                            connect->count();
                        for ($k=0; $k<$cCount; $k++) {
                            $conEP = (string)($this->_Xml->daughterboards[$i]->
                                Pins[$j]->connect[$k]->device);
                            $conPin = (string)($this->_Xml->daughterboards[$i]->
                                Pins[$j]->connect[$k]->conpin);
                            $pinArray[$k+1][0] = "Connect";
                            $pinArray[$k+1][1] = $conEP;
                            $pinArray[$k+1][2] = $conPin;
                        }
                    }
                }

            }
        } /* end function dbPinProperties */
        
        if ($found <> 2) {
            $pinArray[0][0] = "Error";
            if ($found == 0) {
                $pinArray[0][1] = "Daughterboard not found!";
            } else {
                $pinArray[0][1] = "Pin not found!";
            }
        }

        return $pinArray;
    }


    /**
    *********************************************************************
    * this function returns a list of the daughterboard pins and thier
    * connection to the given endpoint.
    * 
    * @param string $dbNum - daughterboard hardware part number
    * @param string $epNum - endpoint part number
    *
    * @return array $pinArray - 2 dimensional array containing 
    *                           [x][0] daughterboard pin name
    *                           [x][1] connecting endpoint pin name
    *
    */
    public function dbToEpConnections($dbNum, $epNum)
    {
        $pinArray = array();
        $dbCount = count($this->DbArray);
        $dbFound = 0;
        $epFound = 0;

        for ($i = 0; $i < $dbCount; $i++) {
            if($this->DbArray[$i] == $dbNum) {
                $dbFound = 1;
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                for ($j = 0; $j < $pinCount; $j++) {
                    $pinName = (string)($this->_Xml->daughterboards[$i]->
                        Pins[$j]->name);
                    $pinArray[$j][0]= $pinName;
                    $connectNum = $this->_Xml->daughterboards[$i]->Pins[$j]->
                        connect->count();
                    for ($times = 0; $times < $connectNum; $times++)  {
                        $pinConnect = (string)$this->_Xml->daughterboards[$i]->
                            Pins[$j]->connect[$times]->device;
                        if ($pinConnect == $epNum) {
                            $epFound = 1;
                            $pinConnectname = (string)$this->_Xml->
                                daughterboards[$i]->Pins[$j]->connect[$times]->conpin;
                            $pinArray[$j][1] = $pinConnectname;
                        }
                    }
                }

                if ($epFound == 0) {
                    $pinArray[0][0] = "Error";
                    $pinArray[0][1] = "Endpoint not found in connections";
                }
            }
        }
        
        if ($dbFound == 0) {
            $pinArray[0][0] = "Error";
            $pinArray[0][1] = "Daughterboard not found!";
        }

        return $pinArray;
    } /* end function dbToEpConnections */


}


?>