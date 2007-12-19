<?php
/**
 * Driver for the 0039-12 endpoints with the FET daughter board.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!class_exists("e00391201")) {

    /*
    define("e00391106", true);
    define("e00391102B_EEPROM_READ", "0A");
    define("e00391102B_SRAM_READ", "0B");
    define("e00391106_SETCONFIG", "6F");
    define("e00391106_SETFET0", "6E");
    define("e00391106_SETFET1", "6D");
    define("e00391106_SETFET2", "6C");
    define("e00391106_SETFET3", "6B");
    
    define("e00391102B_SENSOR_LENGTH", 28);
    define("e00391103B_SENSOR_LENGTH", 33);
    define("e00391102B_GROUP", 36);
    */
    define("E00391201_SETUP", 44);
    define("E00391201_FET0", 46);
    define("E00391201_FET1", 48);
    define("E00391201_FET2", 50);
    define("E00391201_FET3", 52);
    define("E00391201_FET0_MULT", 54);
    define("E00391201_FET1_MULT", 56);
    define("E00391201_FET2_MULT", 58);
    define("E00391201_FET3_MULT", 60);
    define("E00391201_SENSORS", 9);




    /**
     * Driver for the 0039-12 endpoint board and select firmwares
     *
     * @category   Drivers
     * @package    HUGnetLib
     * @subpackage Endpoints
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @version    SVN: $Id$    
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class e00391201 extends eDEFAULT
    {

        /** The hardware name */
        var $HWName = "0039-12 Endpoint";

        /** Average Table */
        var $average_table = "e00391201_average";
        /** history table */
        var $history_table = "e00391201_history";
        /** Devices served by this driver */
        var $devices = array(
        
            "0039-11-06-A" => array(
                "0039-12-00-A" => "BAD",
                "0039-12-01-A" => "BAD",
                "0039-12-02-A" => "BAD",
                "0039-12-01-B" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-11-07-A" => array(
                "0039-12-00-A" => "BAD",
                "0039-12-01-A" => "BAD",
                "0039-12-02-A" => "BAD",
                "0039-12-01-B" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-11-08-A" => array(
                "0039-12-01-B" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-20-04-C" => array(
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-20-05-C" => array(
                "0039-12-02-B" => "DEFAULT",
            ),
            "DEFAULT" => array(
            ),
        );
        /** Modes for the FET */        
        var $modes = array(
            0 => 'Digital', 
            1 => 'Analog - High Z', 
            2 => 'Analog - Voltage', 
            3 => 'Analog - Current'
        );
        
        
        /** Default location variable definition  */
        var $deflocation = array(
            '0039-20-04-C' => array("Fan 1", "Fan 1", "Fan 2", "Fan 2", "Fan 3", "Fan 3", "Fan 4", "Fan 4", "Main"),
            'DEFAULT' => array("Out 1 Current", "Out 1 Voltage", "Out 2 Current", "Out 2 Voltage", "Out 3 Current", "Out 3 Voltage", "Out 4 Current", "Out 4 Voltage", "Main Voltage"),
        );

        /** This is where the hardware devices default configurations go. */
        var $config = array(
            "0039-11-06-A" => array("Function" => "Fan Controller", "Sensors" => 9, "MainV" => 8),        
            "0039-20-04-C" => array("Function" => "Fan Controller", "Sensors" => 9, "MainV" => 8),        
            "0039-11-07-A" => array("Function" => "Power Controller", "Sensors" => 9, "MainV" => 8),
            "0039-11-08-A" => array("Function" => "Water Level Controller", "Sensors" => 5, "MainV" => 4),
            "0039-20-05-C" => array("Function" => "Water Level Controller", "Sensors" => 5, "MainV" => 4),
            "DEFAULT" => array("Function" => "Unknown", "Sensors" => 9),
        );

        /** Calibration data */
        var $caldata = array(
            "DEFAULT" => array(1.79, 16, 1.79, 16, 1.79, 16, 1.79, 16, 16),
            "0039-20-04-C" => array(3.58, 32, 3.58, 32, 3.58, 32, 3.58, 32, 32),
            "0039-20-05-C" => array(3.58, 32, 3.58, 32, 32),
            );
        /** The default labels for the sensor outputs. */
        var $labels = array(
            "DEFAULT" => array("Out1 Current", "Out1 Voltage", "Out2 Current", "Out2 Voltage", "Out3 Current","Out3 Voltage", "Out4 Current", "Out4 Voltage", "Main Voltage"),
            "0039-20-05-C" => array("Out3 Current","Out3 Voltage", "Out4 Current", "Out4 Voltage", "Main Voltage"),
            );
        /** The default  for the sensor outputs. */
        var $units = array(
            "DEFAULT" => array("A", "V", "A", "V", "A", "V", "A", "V", "V"),
            "0039-20-05-C" => array("A", "V", "A", "V", "V"),
            );
        /** The default types for the sensor outputs. */
        var $types = array(
            "DEFAULT" => array(0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x40),
        );
        /** The default sensor types for the sensor outputs. */
        var $sensorTypes = array(
            "DEFAULT" => array("FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard"),
        );

        /** The columns that are device specific go here */
        var $cols = array("FET0pMode" => "FET 0 Mode", 
                                "FET1pMode" => "FET 1 Mode", 
                                "FET2pMode" => "FET 2 Mode", 
                                "FET3pMode" => "FET 3 Mode", 
                                "NumSensors" => "# Sensors",
                                );



        /**
         * Checks a data record to determine what its status is.  It changes
         * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
         * is the status that the record had originally.
         *
         * @param array $Info The information array on the device
         * @param array &$Rec The data record to check
         *
         * @return none
         */
        function checkRecord($Info, &$Rec)
        {
            parent::checkRecordBase($Info, $Rec);    
            if ($Rec["Status"] == "BAD") return;
            if ($Rec["TimeConstant"] == 0) {
                $Rec["Status"]     = "BAD";
                $Rec["StatusCode"] = "Bad TC";
                return;
            }

        }

        /**
         * Interprets a config packet
         *
         * @param array &$Info devInfo array
         *
         * @return none
         */
        function interpConfig(&$Info)
        {

            $this->interpConfigDriverInfo($Info);
            $this->interpConfigHW($Info);
            $this->interpConfigFW($Info);

            $Info["ActiveSensors"] = $Info["NumSensors"];

            $this->_interpConfigFETSetup($Info);
            $this->interpConfigParams($Info);

            $Info["Types"] = (isset($this->types[$Info["FWPartNum"]])) ? $this->types[$Info["FWPartNum"]] : $this->types["DEFAULT"];

            $this->interpConfigSensorSetup($Info);

            if (isset($this->labels[$Info["FWPartNum"]])) {
                $Info["Labels"] = $this->labels[$Info["FWPartNum"]];
            } else {
                $Info["Labels"] = $this->labels["DEFAULT"];            
            }

        }
        /**
         * Interprets a config packet
         *
         * @param array &$Info devInfo array
         *
         * @return none
         */
        private function _interpConfigFETSetup(&$Info) 
        {
            $Info["Setup"] = hexdec(substr($Info["RawSetup"], E00391201_SETUP, 2));
            for ($i = 0; $i < 4; $i++) {
                $mode                   = (($Info["Setup"]>>($i*2)) & 3);
                $Info["FET".$i."Mode"]  = $mode;
                $Info["FET".$i."pMode"] = $this->modes[$mode];
            }                    

            $Info["FET0"]     = hexdec(substr($Info["RawSetup"], E00391201_FET0, 2));
            $Info["FET1"]     = hexdec(substr($Info["RawSetup"], E00391201_FET1, 2));
            $Info["FET2"]     = hexdec(substr($Info["RawSetup"], E00391201_FET2, 2));
            $Info["FET3"]     = hexdec(substr($Info["RawSetup"], E00391201_FET3, 2));
            $Info["FET0Mult"] = hexdec(substr($Info["RawSetup"], E00391201_FET0_MULT, 2));
            $Info["FET1Mult"] = hexdec(substr($Info["RawSetup"], E00391201_FET1_MULT, 2));
            $Info["FET2Mult"] = hexdec(substr($Info["RawSetup"], E00391201_FET2_MULT, 2));
            $Info["FET3Mult"] = hexdec(substr($Info["RawSetup"], E00391201_FET3_MULT, 2));
        }


    
        /**
         * The routine that interprets returned sensor data
         * 
         * @param array $Info    The device info array
         * @param array $Packets An array of packets to interpret
         *
         * @return array
         */
        function interpSensors($Info, $Packets) 
        {
 
            $this->interpConfig($Info);
        
            $ret = array();
            foreach ($Packets as $data) {
                $data = $this->checkDataArray($data);
                if (isset($data['RawData'])) {
                    $index = 0;
                    $data['NumSensors']    = $Info['NumSensors'];
                    $data["ActiveSensors"] = $Info["ActiveSensors"];
                    $data["Driver"]        = $Info["Driver"];
                    $data["DeviceKey"]     = $Info["DeviceKey"];
                    $data["Types"]         = $Info["Types"];
                    $data["DataIndex"]     = $data["Data"][$index++];
                    $data["TimeConstant"]  = 1;
                 
                    if (is_array($data["Data"])) {
                        for ($key = 0; $key < $Info["NumSensors"]; $key++) {
                            $data["raw"][$key]  = $data["Data"][$index++];
                            $data["raw"][$key] += $data["Data"][$index++] << 8;
                        }
                        
                    }    
                    $this->driver->sensors->decodeData($Info, $data);
                    // This changes the voltage across the FET into the output voltage
                    // Vo = Vmain - Vf 
                    $data["Data1"] = $data['data'][1] = round($data["Data8"] -  $data["Data1"], 4);
                    $data["Data3"] = $data['data'][3] = round($data["Data8"] -  $data["Data3"], 4);
                    $data["Data5"] = $data['data'][5] = round($data["Data8"] -  $data["Data5"], 4);
                    $data["Data7"] = $data['data'][7] = round($data["Data8"] -  $data["Data7"], 4);

                    // Check everything
                    $this->checkRecord($Info, $data);
                    $ret[] = $data;

                }
            }
            return $ret;
        }
    
    
        /**
         * Constructor
         *
         * @param object $driver An object of class Driver.
         *
         */
        function e00391201(&$driver) 
        {
            parent::__construct($driver);
        }
    }
}
if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "e00391201", "Type" => "driver", "Class" => "e00391201", "deviceJOIN" => "e00391200_location"));
}

?>
