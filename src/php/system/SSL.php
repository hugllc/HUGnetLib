<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class SSL 
{
    
    /** File name of our CA Certificate*/
    const CA_CERT = "ca.crt";
    /** File name of our CA Key */
    const CA_KEY = "ca.key";
    /** File name of our CA CSR */
    const CA_CSR = "ca.csr";
    /** Number of days the certificates are valid */
    const CERT_VALID = 3650;
    /** @var array The data array to use for certificates */
    private $_certdata = array(
        "countryName" => 'US', 
        "stateOrProvinceName" => 'Minnesota', 
        "localityName" => 'Pine River', 
        "organizationName" => 'Hunt Utilities Group LLC', 
        "organizationalUnitName" => 'HUGnet', 
        "commonName" => '', 
    );
    /** @var object The system object*/
    private $_system = null;
    /** @var object The directory all this stuff is in */
    private $_ssldir = null;
    /**
    * This is the constructor
    *
    * @param object &$system This is the system object
    * @param mixed  $data    This is an array or string to create the object from
    */
    protected function __construct(&$system, $data="")
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $this->_system = $system;
        $this->_certdata["commonName"] = (string)$system->get('fqdn');
        $this->_ssldir = $this->system()->get("confdir")."/ssl";
        if (!file_exists($this->_ssldir)) {
            mkdir($this->_ssldir."/ca", 0755, true);
        }
    }
    /**
    * Returns the system object
    *
    * @return object The system object
    */
    protected function &system()
    {
        return $this->_system;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_system);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    *
    * @return null
    */
    public static function &factory(&$system, $data=null)
    {
        $object = new SSL($system, $dbtable);
        return $object;
    }

    /**
    * Returns the system object
    *
    * @return object The system object
    */
    public function setupCA()
    {
        if (!file_exists($this->_ssldir."/ca/".self::CA_KEY)
            || !file_exists($this->_ssldir."/ca/".self::CA_CERT)
        ) {

            // Generate our private key
            $privkey = openssl_pkey_new();
            // Generate our CSR
            $csr = openssl_csr_new($this->_certdata, $privkey);
            // Self sign the CSR
            $sscert = openssl_csr_sign($csr, null, $privkey, self::CERT_VALID);
            // Export the Cert
            openssl_x509_export_to_file(
                $sscert, $this->_ssldir."/ca/".self::CA_CERT, false
            );
            // Export the key
            openssl_pkey_export_to_file(
                $privkey, $this->_ssldir."/ca/".self::CA_KEY
            );
            // Export the CSR (in case we want to get it signed by a real CA)
            openssl_csr_export_to_file(
                $csr, $this->_ssldir."/ca/".self::CA_CSR, false
            );
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/ca/".self::CA_KEY, 0600);
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/ca/".self::CA_CERT, 0644);
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/ca/".self::CA_CSR, 0644);

        }
    }

}


?>
