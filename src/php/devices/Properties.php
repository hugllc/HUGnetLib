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

    /** @var The endpoint array */
    private $_EpArray = array();
    /** @var The daughterboard array */
    private $_DbArray = array();
    /** @var The part number of the endpoint */
    private $_endpointNum;
    /** @var The part number of the daughterboard */
    private $_daughterNum;


    /**
    *********************************************************************
    * this function loads the devices xml file into an object and loads  
    * the arrays of endpoints and daughterboards.
    *
    * @param string $filename    The file to use
    * @param string $endpointNum The part number of the endpoint
    * @param string $daughterNum The part number of the daughterboard
    * 
    * @return void
    */
    protected function __construct($filename, $endpointNum, $daughterNum)
    {
        if (is_null($filename)) {
            $filename = dirname(__FILE__)."/../devices.xml";
        }

        $this->_Xml = simplexml_load_file($filename);

        /* fill the endpoint list array */
        $count = $this->_Xml->endpoints->count();
        for ($i=0; $i<$count; $i++) {
            $this->_EpArray[$i] = (string) ($this->_Xml->endpoints[$i]->HWPartNum);
        }

        /* fill the daughterboard list array */
        $count = $this->_Xml->daughterboards->count();
        for ($i = 0; $i < $count; $i++) {
            $this->_DbArray[$i] = (string) ($this->_Xml->daughterboards[$i]
                ->HWPartNum);
        }
        $this->_endpointNum = $endpointNum;
        $this->_daughterNum = $daughterNum;

    } /* end function __construct */
    /**
    *********************************************************************
    * this function instantiates the class object and returns the object
    * to the caller.
    *
    * @param string $endpointNum The part number of the endpoint
    * @param string $daughterNum The part number of the daughterboard
    * @param string $filename    The file to use
    * 
    * @return object device data object.
    *
    */
    public static function &factory($endpointNum, $daughterNum, $filename = null)
    {
        $object = new Properties($filename, $endpointNum, $daughterNum);
        return $object;
    }


    /**
    ********************************************************************
    * this function returns the list of endpoints available in 
    * the devices.xml file.
    *
    * @return array - $_EpArray a list of endpoint hardware part numbers.
    *
    */
    public function getEndpoints()
    {

        return $this->_EpArray;

    }

    /**
    ********************************************************************
    * this function returns the list of daughterboards available in 
    * the devices.xml file.
    *
    * @return array - $_DbArray, a list of daughterboard hardware part
    *                            numbers.
    *
    */
    public function getDaughterboards()
    {

        return $this->_DbArray;

    }

    /**
    *********************************************************************
    * this function returns the endpoint hardware number with which the 
    * object was created.
    *
    * @return string - endpoint hardware partnumber.
    *
    *
    */
    public function getEndpointNum()
    {
        return $this->_endpointNum;
 
    }

    /**
    *********************************************************************
    * this function returns the daughterboard hardware number with which 
    * the object was created.
    *
    * @return string - daughterboard hardware partnumber.
    *
    *
    */
    public function getDaughterboardNum()
    {
        return $this->_daughterNum;
 
    }

    /**
    *********************************************************************
    * this function sets a new endpoint and daughterboard part number 
    * for the object.
    *
    * @param string $endpointNum new endpoint hardware number
    * @param string $daughterNum new daughterboard hardware part number
    *
    * @return array  - success or error with condition 
    *
    */
    public function setPartNumbers($endpointNum, $daughterNum)
    {
        $result = array();

        if (in_array($endpointNum, $this->_EpArray)) {
            if (in_array($daughterNum, $this->_DbArray)) {
                $this->_endpointNum = $endpointNum;
                $this->_daughterNum = $daughterNum;
                $result[0] = 'Okay';
            } else {
                $result[0] = 'Error';
                $result[1] = 'Invalid Daughterboard Number!';
            }
        } else {
            $result[0] = 'Error';
            $result[1] = 'Invalid Endpoint Number!';
        }

        return $result;
    }


    /**
    *********************************************************************
    * this function gets the pin list for the object endpoint. 
    *
    * @return array $pinArray - array of type string containing pin 
    *                           names.
    *
    */
    public function getEpPinList()
    {
        $pinArray = array();

        if (in_array($this->_endpointNum, $this->_EpArray)) {
            $pinArray = $this->_epPinList();
        } else {
            $pinArray[0] = "Error";
            $pinArray[1] = "Endpoint not found!";
        }

        return $pinArray;
    }

    /**
    *********************************************************************
    * this function gets the pin list for the object daughterboard. 
    *
    * @return array $pinArray - array of type string containing pin 
    *                           names.
    *
    */
    public function getDbPinList()
    {
        $pinArray = array();

        if (in_array($this->_daughterNum, $this->_DbArray)) {
            $pinArray = $this->_dbPinList();
        } else {
            $pinArray[0] = "Error";
            $pinArray[1] = "Daughterboard not found!";
        }

        return $pinArray;
    }
            
    /**
    *********************************************************************
    * this function returns the pin properties for the endpoint and
    * a given pin name.
    *
    * @param string $pinName name of the pin 
    *
    * @return array $pinArray - an array containing:
    *                              [0] the pin function(s),
    *                              [1] series resistor value
    *                              [2] shunt resistor value
    *                              [3] shunt resistor location 
    *                              [4] shunt resistor pull
    *                              [5] high voltage input flag
    *
    */
    public function getEpPinProperties($pinName)
    {
        $pinArray = array();

        if (in_array($this->_endpointNum, $this->_EpArray)) {
            $pinArray = $this->_epPinProperties($pinName);
        } else {
            $pinArray[0] = "Error";
            $pinArray[1] = "Endpoint not found!";
        }

        return $pinArray;
    }

    /**
    *********************************************************************
    * this function returns the pin properties for the daughterboard and
    * a given pin name.
    *
    * @param string $pinName name of the pin 
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
    public function getDbPinProperties($pinName)
    {
        $pinArray = array();

        if (in_array($this->_daughterNum, $this->_DbArray)) {
            $pinArray = $this->_dbPinProperties($pinName);
        } else {
            $pinArray[0][0] = "Error";
            $pinArray[0][1] = "Daughterboard not found!";
        }

        return $pinArray;
    }
            
    /**
    *********************************************************************
    * this function returns a list of the daughterboard pins and their
    * connection to the given endpoint provided the hardware part number
    * is in the set of daughter boards.
    * 
    * @return array $pinArray - 2 dimensional array containing 
    *                           [x][0] daughterboard pin name
    *                           [x][1] connecting endpoint pin name
    *
    */
    public function getDbToEpConnections()
    {
        $pinArray = array();

        if (in_array($this->_daughterNum, $this->_DbArray)) {
            $pinArray = $this->_dbToEpConnections();
        } else {
            $pinArray[0][0] = "Error";
            $pinArray[0][1] = "Daughterboard not found!";
        }

        return $pinArray;
    }


    /**
    *********************************************************************
    * this function returns a list of the pin names and their functions  
    * for the given endpoint.
    *
    * @return array $pinArray - array of type string containing pin 
    *                           names.
    *
    */
    private function _epPinList()
    {
        $epName = $this->_endpointNum;

        $pinArray = array();
        $epCount = count($this->_EpArray);
        $found = 0;

        for ($i=0; $i<$epCount; $i++) {
            if ($this->_EpArray[$i] == $epName) {
                $pinCount = $this->_Xml->endpoints[$i]->Pins->count();
                if ($pinCount == 0) {
                    $found = 1;
                }
                for ($j=0; $j< $pinCount; $j++) {
                    $found = 2;
                    $pinName = (string)$this->_Xml->endpoints[$i]->Pins[$j]->name;
                    $pinArray[$j]= $pinName;
                }
            }
        }

        if ($found == 1) {
            $pinArray[0] = "Error";
            $pinArray[1] = "No Pins to display!";
        }

        return $pinArray;
    } /* end function _epPinList */

    /**
    *********************************************************************
    * this function returns the pin properties for a given endpoint and
    * a given pin name.
    *
    * @param string $pinName name of the pin 
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
    private function _epPinProperties($pinName)
    {
        $epName = $this->_endpointNum;
        $pinArray = array();
        $epCount = count($this->_EpArray);
        $found = 0;

        for ($i=0; $i<$epCount; $i++) {
            if ($this->_EpArray[$i] == $epName) {
                $found = 1;
                $pinCount = $this->_Xml->endpoints[$i]->Pins->count();
                if ($pinCount == 0) {
                    $found = 2;
                } else {
                    for ($j=0; $j< $pinCount; $j++) {
                        $pName = (string)$this->_Xml->endpoints[$i]->Pins[$j]
                            ->name;
                        if ($pName == $pinName) {
                            $found = 3;
                            $pinFunct = (string)$this->_Xml->endpoints[$i]
                                ->Pins[$j]->function;
                            $pinSeriesResValue = (string)$this->_Xml
                                ->endpoints[$i]->Pins[$j]->series;
                            $pinArray[0]= $pinFunct;
                            if ($pinSeriesResValue <> null) {
                                $pinShuntResValue = (string)$this->_Xml
                                    ->endpoints[$i]->Pins[$j]->shunt->value;
                                $pinArray[1] = $pinSeriesResValue;
                                $pinArray[2] = $pinShuntResValue;
                                if ($pinShuntResValue <> "none") {
                                    $pinShuntResLoc = (string)$this->_Xml
                                        ->endpoints[$i]->Pins[$j]->shunt->location;
                                    $pinShuntResPull = (string)$this->_Xml
                                        ->endpoints[$i]->Pins[$j]->shunt->pull;
                                    $pinArray[3] = $pinShuntResLoc;
                                    $pinArray[4] = $pinShuntResPull;
                                } else {
                                    $pinArray[3] = "none";
                                    $pinArray[4] = "none";
                                }
                                $pinHighVoltage = (string)$this->_Xml
                                    ->endpoints[$i]->Pins[$j]->highvoltage;
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
    * @return array $pinArray - a list of pin names for the daughterboard.
    *
    */
    private function _dbPinList()
    {
        $dbNum = $this->_daughterNum;
        $pinArray = array();
        $dbCount = count($this->_DbArray);

        for ($i=0; $i<$dbCount; $i++) {
            if ($this->_DbArray[$i] == $dbNum) {
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                for ($j=0; $j< $pinCount; $j++) {
                    $pinName = (string)($this->_Xml->daughterboards[$i]
                        ->Pins[$j]->name);
                    $pinArray[$j]= $pinName;
                }
            }
        }
    
        return $pinArray;
    } /* end function _dbPinList */


    /**
    *********************************************************************
    * this function returns the pin function for a given daughter and a
    * give pin name.
    *
    * @param string $pinName name of the pin 
    *
    * @return array $pinProperties a 2 dimensional array containing: 
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
    private function _dbPinProperties($pinName)
    {
        $dbName = $this->_daughterNum;
        $pinArray = array();
        $dbCount = count($this->_DbArray);
        $found = 0;

        for ($i=0; $i<$dbCount; $i++) {
            if ($this->_DbArray[$i] == $dbName) {
                $found = 1;
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                for ($j=0; $j< $pinCount; $j++) {
                    $pName = (string) $this->_Xml->daughterboards[$i]
                        ->Pins[$j]->name;
                    if ($pName == $pinName) {
                        $found = 2;
                        $pfunct = (string) ($this->_Xml->daughterboards[$i]
                            ->Pins[$j]->function);
                        $pinArray[0][0] = $pfunct;
                        $cCount = $this->_Xml->daughterboards[$i]->Pins[$j]
                            ->connect->count();
                        for ($k=0; $k<$cCount; $k++) {
                            $conEP = (string)($this->_Xml->daughterboards[$i]
                                ->Pins[$j]->connect[$k]->device);
                            $conPin = (string)($this->_Xml->daughterboards[$i]
                                ->Pins[$j]->connect[$k]->conpin);
                            $pinArray[$k+1][0] = "Connect";
                            $pinArray[$k+1][1] = $conEP;
                            $pinArray[$k+1][2] = $conPin;
                        }
                    }
                }

            }
        } /* end function _dbPinProperties */
        
        if ($found <> 2) {
            $pinArray[0][0] = "Error";
            if ($found == 1) {
                $pinArray[0][1] = "Pin not found!";
            }
        }

        return $pinArray;
    }


    /**
    *********************************************************************
    * this function returns a list of the daughterboard pins and their
    * connection to the given endpoint.
    * 
    * @return array $pinArray - 2 dimensional array containing 
    *                           [x][0] daughterboard pin name
    *                           [x][1] connecting endpoint pin name
    *
    */
    private function _dbToEpConnections()
    {
        $dbNum = $this->_daughterNum;
        $epNum = $this->_endpointNum;
        $pinArray = array();
        $dbCount = count($this->_DbArray);
        $epFound = 0;

        for ($i = 0; $i < $dbCount; $i++) {
            if ($this->_DbArray[$i] == $dbNum) {
                $dbFound = 1;
                $pinCount = $this->_Xml->daughterboards[$i]->Pins->count();
                for ($j = 0; $j < $pinCount; $j++) {
                    $pinName = (string)($this->_Xml->daughterboards[$i]
                        ->Pins[$j]->name);
                    $pinArray[$j][0]= $pinName;
                    $connectNum = $this->_Xml->daughterboards[$i]->Pins[$j]
                        ->connect->count();
                    for ($times = 0; $times < $connectNum; $times++) {
                        $pinConnect = (string)$this->_Xml->daughterboards[$i]
                            ->Pins[$j]->connect[$times]->device;
                        if ($pinConnect == $epNum) {
                            $epFound = 1;
                            $pinConnectname = (string)$this->_Xml
                                ->daughterboards[$i]->Pins[$j]->connect[$times]
                                ->conpin;
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
        
        return $pinArray;
    } /* end function _dbToEpConnections */


}


?>