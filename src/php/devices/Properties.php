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
    private $_epXml;
    private $_dbXml;

    /** @var The endpoint array */
    private $_EpArray = array();
    /** @var The daughterboard array */
    private $_DbArray = array();
    /** @var The part number of the endpoint */
    private $_endpointNum = "";
    /** @var The part number of the daughterboard */
    private $_daughterNum = "";


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

        if (is_null($endpointNum)) {
            $endpointNum = "0039-28-01-A";
        }

        /*
        if (is_null($daughterNum)) {
            $daughterNum = "0039-23-01-A";
        }
        */
        $epFileName = $this->ConvertToDevFilename($endpointNum);
        
        if (!empty($daughterNum)) {
            $dbFileName = $this->ConvertToDevFilename($daughterNum);
            $this->_dbXml = simplexml_load_file(dirname(__FILE__)."/../xml/".$dbFileName);
        } else {
            $this->_dbXml = simplexml_load_file(dirname(__FILE__)."/../xml/dev.xml");
        }
        $this->_epXml = simplexml_load_file(dirname(__FILE__)."/../xml/".$epFileName);

        /* fill the endpoint list array */
        $count = $this->_epXml->endpoints->count();
        for ($i=0; $i<$count; $i++) {
            $this->_EpArray[$i] = (string) ($this->_epXml->endpoints[$i]->HWPartNum);
        }

        /* fill the daughterboard list array */
        $count = $this->_dbXml->daughterboards->count();
        for ($i = 0; $i < $count; $i++) {
            $this->_DbArray[$i] = (string) ($this->_dbXml->daughterboards[$i]
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
    * this function takes the endpoint or daughterboard number passed 
    * to it and converts it to the dev XML file for that part number.
    *
    * @return string - $devFileName 
    *
    */
    private function ConvertToDevFilename($devnum)
    {
        $devFileName = "dev".substr($devnum,2,2).substr($devnum,5,2).".xml";

        return $devFileName;

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
    * this function empties the endpoint list array.
    *
    */
    private function emptyEpArray()
    {
        $epCount = count($this->_EpArray);
        for ($i = 0; $i<$epCount; $i++) {
            array_pop($this->_EpArray);
        }

    }

    /**
    **********************************************************************
    * this function empties the daughterboard list array
    *
    */
    private function emptyDbArray()
    {

        $dbCount = count($this->_DbArray);

        for ($i = 0; $i<$dbCount; $i++) {
            array_pop($this->_DbArray);
        }

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

        $epFileName = $this->ConvertToDevFilename($endpointNum);
        $dbFileName = $this->ConvertToDevFilename($daughterNum);
        
        $this->_epXml = simplexml_load_file(dirname(__FILE__).'/../xml/'.$epFileName);
        $this->_dbXml = simplexml_load_file(dirname(__FILE__).'/../xml/'.$dbFileName);

        /* first, empty the endpoint list array */
        $this->emptyEpArray();
        /* next, fill the endpoint list array */
        $count = $this->_epXml->endpoints->count();
        for ($i=0; $i<$count; $i++) {
            $this->_EpArray[$i] = (string) ($this->_epXml->endpoints[$i]->HWPartNum);
        }

        /* now, empty the daughterboard list array */
        $this->emptyDbArray();
        /* finally, fill the daughterboard list array */
        $count = $this->_dbXml->daughterboards->count();
        for($i = 0; $i < $count; $i++) {
            $this->_DbArray[$i] = (string) ($this->_dbXml->daughterboards[$i]
                ->HWPartNum);
        }

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
    public function getPinList()
    {
        $pinArray = array();

        if ($this->_useDB()) {
            $pinArray = $this->_dbPinList();
        } else if ($this->_useEP()) {
            $pinArray = $this->_epPinList();
        } else {
            $pinArray = false;
        }

        return $pinArray;
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

        if ($this->_useEP()) {
            $pinArray = $this->_epPinList();
        } else {
            $pinArray = false;
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

        if ($this->_useDB()) {
            $pinArray = $this->_dbPinList();
        } else {
            $pinArray = false;
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
    public function getPinProperties($pinName)
    {
        $pinArray = array();

        if ($this->_useDB()) {
            $pinArray = $this->_dbPinProperties($pinName);
        } else if ($this->_useEP()) {
            $pinArray = $this->_epPinProperties($pinName);
        } else {
            $pinArray = false;
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

        if ($this->_useEP()) {
            $pinArray = $this->_epPinProperties($pinName);
        } else {
            $pinArray = false;
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

        if ($this->_useDB()) {
            $pinArray = $this->_dbPinProperties($pinName);
        } else {
            $pinArray = false;
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

        if ($this->_useDB()) {
            $pinArray = $this->_dbToEpConnections();
        } else {
            $pinArray = false;
        }

        return $pinArray;
    }

    /**
    *********************************************************************
    * This function returns a boolean to tell whether or not we should be using
    * the daughterboard instead of the endpoint.
    *
    * @return bool Whether or not to use the daughterboard
    */
    private function _useDB()
    {
        return in_array($this->_daughterNum, $this->_DbArray);
    }
    /**
    *********************************************************************
    * This function returns a boolean to tell whether or not we should be using
    * the endpoint.
    *
    * @return bool Whether or not to use the endpoint
    */
    private function _useEP()
    {
        return in_array($this->_endpointNum, $this->_EpArray);
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
                $pinCount = $this->_epXml->endpoints[$i]->Pins->count();
                if ($pinCount == 0) {
                    $found = 1;
                }
                for ($j=0; $j< $pinCount; $j++) {
                    $found = 2;
                    $pinName = (string)$this->_epXml->endpoints[$i]->Pins[$j]->name;
                    $pinArray[$j]= $pinName;
                }
            }
        }

        if ($found == 1) {
            $pinArray = false;
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
            if ($this->_EpArray[$i] != $epName) {
                continue;
            }
            $found = 1;
            $pinCount = $this->_epXml->endpoints[$i]->Pins->count();
            if ($pinCount == 0) {
                $found = 2;
            } else {
                for ($j=0; $j< $pinCount; $j++) {
                    $pName = (string)$this->_epXml->endpoints[$i]->Pins[$j]->name;
                    if ($pName != $pinName) {
                        continue;
                    }
                    $found = 3;
                    $pinFunct = (string)$this->_epXml->endpoints[$i]
                        ->Pins[$j]->function;
                    $pinSeriesResValue = (string)$this->_epXml
                        ->endpoints[$i]->Pins[$j]->series;
                    $pinArray["properties"]= str_replace(" ", "", $pinFunct);
                    if ($pinSeriesResValue <> null) {
                        $pinShuntResValue = (string)$this->_epXml
                            ->endpoints[$i]->Pins[$j]->shunt->value;
                        $pinArray["seriesRes"] = $pinSeriesResValue;
                        $pinArray["shuntRes"] = $pinShuntResValue;
                        if ($pinShuntResValue <> "none") {
                            $pinShuntResLoc = (string)$this->_epXml
                                ->endpoints[$i]->Pins[$j]->shunt->location;
                            $pinShuntResPull = (string)$this->_epXml
                                ->endpoints[$i]->Pins[$j]->shunt->pull;
                            $pinArray["shuntLoc"] = $pinShuntResLoc;
                            $pinArray["shuntPull"] = $pinShuntResPull;
                        }
                        $pinHighVoltage = (string)$this->_epXml
                            ->endpoints[$i]->Pins[$j]->highvoltage;
                        $pinArray["highVoltage"] = $pinHighVoltage;
                    }
                    break;
                }
            }
        }
        if ($found <> 3) {
            $pinArray = false;
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
            if ($this->_DbArray[$i] != $dbNum) {
                continue;
            }
            $pinCount = $this->_dbXml->daughterboards[$i]->Pins->count();
            for ($j=0; $j< $pinCount; $j++) {
                $pinName = (string)($this->_dbXml->daughterboards[$i]
                    ->Pins[$j]->name);
                $pinArray[$j]= $pinName;
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
            if ($this->_DbArray[$i] != $dbName) {
                continue;
            }
            $found = 1;
            $pinCount = $this->_dbXml->daughterboards[$i]->Pins->count();
            for ($j=0; $j< $pinCount; $j++) {
                $pName = (string) $this->_dbXml->daughterboards[$i]
                    ->Pins[$j]->name;
                if ($pName != $pinName) {
                    continue;
                }
                $found = 2;
                $pfunct = (string) ($this->_dbXml->daughterboards[$i]
                    ->Pins[$j]->function);
                $pinArray["properties"] = $pfunct;
                $cCount = $this->_dbXml->daughterboards[$i]->Pins[$j]
                    ->connect->count();
                for ($k=0; $k<$cCount; $k++) {
                    $conEP = (string)($this->_dbXml->daughterboards[$i]
                        ->Pins[$j]->connect[$k]->device);
                    if ($conEP == $this->_endpointNum) {
                        $conPin = (string)($this->_dbXml->daughterboards[$i]
                            ->Pins[$j]->connect[$k]->conpin);

                        $pinArray["mbcon"] = $conPin;
                        break;
                    }
                }
                break;
            }
        } /* end function _dbPinProperties */
        
        if ($found <> 2) {
            $pinArray = false;
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
                $pinCount = $this->_dbXml->daughterboards[$i]->Pins->count();
                for ($j = 0; $j < $pinCount; $j++) {
                    $pinName = (string)($this->_dbXml->daughterboards[$i]
                        ->Pins[$j]->name);
                    $pinArray[$j][0]= $pinName;
                    $connectNum = $this->_dbXml->daughterboards[$i]->Pins[$j]
                        ->connect->count();
                    for ($times = 0; $times < $connectNum; $times++) {
                        $pinConnect = (string)$this->_dbXml->daughterboards[$i]
                            ->Pins[$j]->connect[$times]->device;
                        if ($pinConnect == $epNum) {
                            $epFound = 1;
                            $pinConnectname = (string)$this->_dbXml
                                ->daughterboards[$i]->Pins[$j]->connect[$times]
                                ->conpin;
                            $pinArray[$j][1] = $pinConnectname;
                        }
                    }
                }

                if ($epFound == 0) {
                    $pinArray = false;
                }
            }
        }
        
        return $pinArray;
    } /* end function _dbToEpConnections */


}


?>