#!/usr/local/bin/php
<?php
/**
 *   Script to check the database
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Scripts
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
   $inc = ini_get('include_path');
   $inc .= ":".dirname(__FILE__)."/pear";
   ini_set('include_path', $inc);
   require_once 'MDB.php';
   
   $dsn = "mysql://root:my*sql@socket(/tmp/mysql.sock)/";


	$db = MDB::connect($dsn);

	if (PEAR::isError($db)) {
		die("Error connecting to the database\r\n");
	}
	print "\r\n\r\nDatabase Check\r\n\r\n";

   $res = $db->query("show slave status");
	if (PEAR::isError($res)) {
		die("Error connecting to the database\r\n");
	}


   $query = "SHOW SLAVE STATUS";
   $res = $db->query($query);

	while($row = $db->fetchRow($res, MDB_FETCHMODE_ASSOC)) {
		foreach($row as $key => $val) {
         if ((stristr($key, "slave") !== FALSE) || (stristr($key, "master_host") !== FALSE) || (stristr($key, "seconds_behind") !== FALSE)){
            print $key." -> ".$val."\n";
         }
      }
      if ((trim(strtolower($row['Slave_IO_Running'])) == 'no')
      	|| (trim(strtolower($row['Slave_SQL_Running'])) == 'no')) {
		   $res = $db->query('SLAVE START');	
		   print "Trying to restart slave\r\n";
      }
	}

	print "\r\n";

   $query = "SHOW DATABASES";
   $res = $db->query($query);
   $databases = array();
	while($row = $db->fetchInto($res)) {
		$databases[] = $row[0];
	}


   foreach($databases as $thedb) {
		print "Checking DB ".$thedb."\n";
		$ndb = MDB::connect($dsn.$thedb);
		$ret = $ndb->query("show tables");
		$tables = array();
		if ($ndb->isError($ret)) {
			print "ERROR\n";		
		}
		while($row = $ndb->fetchInto($ret)) {
			$tables[] = $row[0];
		}

		foreach($tables as $table) {
			$ret = $ndb->query("lock tables ".$table." write");
			$ret = $ndb->query("flush table ".$table);
			$ret = $ndb->query("check table ".$table);
			$data = $ndb->fetchRow($ret);
			if ($data[3] != "OK") {
				$ret = $ndb->query("repair table ".$table);
				$data = $ndb->fetchRow($ret);
				var_dump($data);
			}
			$ret = $ndb->query("optimize table ".$table);
			$ret = $ndb->query("unlock tables");
		}
		$ndb->disconnect();
   }

	print "\r\n";

?>
