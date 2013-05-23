<?php
/**
 * This file howses the socket class
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
namespace HUGnet\processes;
/** This is our base class */
require_once dirname(__FILE__)."/../ui/Daemon.php";
/** This is our base class */
require_once dirname(__FILE__)."/watchdog/Periodic.php";

/**
 * This code makes sure all of the other HUGnet stuff is running.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Watchdog extends \HUGnet\ui\Daemon
{
    /** This is the amount of time we wait */
    const WAIT_TIME = 30;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait;
    /** This is the start time of the current run */
    private $_plugins = array();
    /** This is where we keep the last time we did things */
    private $_last = array(
        "criticalError" => 0,
    );
    /** This is where we keep the last time we did things */
    protected $progConfig = array(
        "email" => null,
        "email_wait" => 1800,
        "max_poll_age" => 600,
    );
    /** This is our critical Error locations */
    private $_criticalError = array();
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        parent::__construct($config);
        /* Get our Device */
        $this->_plugins = \HUGnet\processes\watchdog\Periodic::plugins($this);
        $this->criticalError(
            "restart",
            "Watchdog process starting"
        );
    }
    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    *
    * @return null
    */
    static public function &factory(&$config = array())
    {
        $obj = new Watchdog($config);
        return $obj;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_plugins) as $key) {
            unset($this->_plugins[$key]);
        }
    }
    /**
    * This is our main routine
    *
    * @return null
    */
    public function main()
    {
        $this->_mainStart = time();
        foreach ($this->_plugins as $obj) {
            $obj->execute();
        }
        $this->criticalErrorMail();
        $this->_wait();
    }
    /**
    * This is our wait
    *
    * @return null
    */
    private function _wait()
    {
        parent::main(); // This should be called at least once per iteration
        $this->_wait = self::WAIT_TIME - (time() - $this->_mainStart);
        if (($this->_wait > 0) && $this->loop()) {
            $this->out("Waiting ".$this->_wait." seconds at ".date("Y-m-d H:i:s"));
            for (; ($this->_wait > 0) && $this->loop(); $this->_wait--) {
                parent::main();
                sleep(1);
            }
        }
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &device($config = array())
    {
        return false;
    }
    /**
    * This sets a critical error
    *
    * @param string $key   The key for the critical error
    * @param string $error The error message
    *
    * @return null
    */
    public function criticalError($key, $error)
    {
        $this->_criticalError[$key] = (string)$error;
    }
    /**
    * This clears an error
    *
    * @param string $key The key for the critical error
    *
    * @return null
    */
    public function clearError($key)
    {
        if (isset($this->_criticalError[$key])) {
            unset($this->_criticalError[$key]);
        }
    }
    /**
    * This sends out a critical error email
    *
    * This creates our email
    *
    * @return true on success, false on failure, null on not time yet.
    */
    protected function criticalErrorMail()
    {
        $last = &$this->_last["criticalError"];
        if (($last + $this->get("email_wait")) > time()) {
            return null;
        }
        if (empty($this->_criticalError)) {
            return true;
        }
        $email = $this->get("email");
        if (empty($email) || !is_string($email)) {
            return false;
        }
        $subject = "Critical Error on ".$this->system()->get("nodename");
        $message = "The following new errors have occurred:\n\n\n";
        foreach ($this->_criticalError as $text) {
            $message .= " * ".wordwrap($text)."\n";
        }
        $headers = array(
            "MIME-Version: 1.0",
            "Content-type: text/plain; charset=UTF-8",
            "From: HUGnet Admin <admin@".$this->system()->get("fqdn").">",
            "X-Mailer: PHP/".$this->system()->get("phpversion"),
        );
        $params = "";
        $ret = $this->mail(
            $email,
            $subject,
            $message,
            implode("\n", $headers),
            $params
        );
        if ($ret) {
            $this->_criticalError = array();
            $last = $this->system()->now();
        }
        return $ret;
    }
    /**
    * Wrapper to send out an email
    *
    * This wrapper is just for testing purposes, so I can isolate the call to 'mail'
    *
    * @param string $recip   The address to send the message to
    * @param string $subject The subject of the message
    * @param string $message The actual message
    * @param string $headers THe extra headers to send
    * @param string $params  Additional parameters to send
    *
    * @return mixed Array in test mode, bool in normal mode
    * @codeCoverageIgnoreStart
    * Can't test this call
    */
    protected function mail($recip, $subject, $message, $headers, $params)
    {
        return mail(
            $recip,
            $subject,
            $message,
            $headers,
            $params
        );
    }
    // @codeCoverageIgnoreEnd
    
}
?>
