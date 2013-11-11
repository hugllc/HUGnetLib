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
class SSLCA
{
    
    /** File name of our CA Certificate*/
    const FILE_CERT = "ca.crt";
    /** File name of our CA Key */
    const FILE_KEY = "ca.key";
    /** File name of our CA CSR */
    const FILE_CSR = "ca.csr";
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
        $this->_certdata["commonName"] = (string)$system->get('uuid');
        $this->_ssldir = $this->system()->get("confdir")."/ssl/ca";
        $this->_setup();
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
        $object = new SSLCA($system, $dbtable);
        return $object;
    }

    /**
    * Returns the system object
    *
    * @return object The system object
    */
    private function _setup()
    {
        $dirs = array("", "/private", "/requests", "/signed", "/revoked");
        foreach($dirs as $dir) {
            if (!file_exists($this->_ssldir.$dir)) {
                mkdir($this->_ssldir.$dir, 0750, true);
            }
        }
        if (!file_exists($this->_ssldir."/".self::FILE_KEY)
            || !file_exists($this->_ssldir."/".self::FILE_CERT)
        ) {

            // Generate our private key
            $privkey = openssl_pkey_new();
            // Generate our CSR
            $csr = openssl_csr_new($this->_certdata, $privkey);
            // Self sign the CSR
            $sscert = openssl_csr_sign($csr, null, $privkey, self::CERT_VALID);
            // Export the Cert
            openssl_x509_export_to_file(
                $sscert, $this->_ssldir."/".self::FILE_CERT, false
            );
            // Export the key
            openssl_pkey_export_to_file(
                $privkey, $this->_ssldir."/".self::FILE_KEY
            );
            // Export the CSR (in case we want to get it signed by a real CA)
            openssl_csr_export_to_file(
                $csr, $this->_ssldir."/".self::FILE_CSR, false
            );
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/".self::FILE_CERT, 0640);
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/".self::FILE_CSR, 0640);
            // Set strict permissions on th ekey
            chmod($this->_ssldir."/".self::FILE_KEY, 0600);
            // Free the keys
            openssl_pkey_free($privkey);
            openssl_x509_free($sscert);
        }
        $this->_pkey = openssl_pkey_get_private(
            file_get_contents($this->_ssldir."/".self::FILE_KEY)
        );
        $this->_cert = openssl_x509_read(
            file_get_contents($this->_ssldir."/".self::FILE_CERT)
        );
    }
    /**
    * Imports a CSR
    *
    * @param string $csr  The csr to import
    * @param string $uuid The uuid of the system this csr belongs to.
    *
    * @return bool True on success, false on failure
    */
    public function saveCSR($csr, $uuid)
    {
        $data = openssl_csr_get_subject($csr, false);
        $ret = false;
        if (is_array($data) && ($data["commonName"] == $uuid)) {
            $file = $this->_ssldir."/requests/".$uuid.".csr";
            $fd = fopen($file, "w");
            if ($fd) {
                $ret = fwrite($fd, $csr);
                fclose($fd);
            }
        }
        return (bool)$ret;
    }
    /**
    * Signs a CSR
    *
    * The CSR should already be put in place by saveCSR()
    *
    * @param string $uuid The uuid of the system this csr belongs to.
    *
    * @return bool True on success, false on failure
    */
    public function signCSR($uuid)
    {
        $csrfile = $this->_ssldir."/requests/".$uuid.".csr";
        if (!file_exists($csrfile)) {
            return false;
        }
        $csr  = file_get_contents($csrfile);
        $data = openssl_csr_get_subject($csr, false);
        $ret  = false;
        if (is_array($data) && ($data["commonName"] == $uuid)) {
            $file = $this->_ssldir."/signed/".$uuid.".crt";
            if (!file_exists($file)) {
                $sscert = openssl_csr_sign(
                    $csr, $this->_cert, $this->_pkey, self::CERT_VALID
                );
                // Export the Cert
                $ret = openssl_x509_export_to_file(
                    $sscert, $file, false
                );
                openssl_x509_free($sscert);
            }
            unlink($csrfile);
        }
        return (bool)$ret;
    }
    /**
    * Signs a CSR
    *
    * The CSR should already be put in place by saveCSR()
    *
    * @param string $uuid The uuid of the system this csr belongs to.  'ca' for the
    *                     certficate of the CA.  
    *
    * @return string certificate in PEM format.  NULL if certificate not found.
    */
    public function cert($uuid)
    {
        $uuid = trim($uuid);
        if (strtolower($uuid) == "ca") {
            $file = $this->_ssldir."/ca.crt";
            if (file_exists($file)) {
                $ret = file_get_contents($file);
            }
        } else {
            $file = $this->_ssldir."/signed/".$uuid.".crt";
            if (file_exists($file)) {
                $ret = file_get_contents($file);
            }
        }
        return $ret;
    }
    /**
    * Revokes a certificate
    *
    * The CSR should already be put in place by saveCSR()
    *
    * @param string $uuid The uuid of the system this csr belongs to.  'ca' for the
    *                     certficate of the CA.  
    *
    * @return string certificate in PEM format.  NULL if certificate not found.
    */
    public function revoke($uuid)
    {
        $uuid = trim($uuid);
        $file = $this->_ssldir."/signed/".$uuid.".crt";
        $ret = false;
        if (file_exists($file)) {
            copy($file, $this->_ssldir."/revoked/".$uuid.".crt");
            unlink($file);
            $ret = true;
        }
        return $ret;
    }
}


?>
