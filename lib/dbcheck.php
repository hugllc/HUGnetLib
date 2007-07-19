#!/usr/local/bin/php
<?php
/**
	$Id$
	@file dbcheck.php
	@brief Script to maintain databases
	@author Scott L. Price (prices@dflytech.com)
	@date 04/12/04

	
	
	$Log: dbcheck.php,v $
	Revision 1.2  2005/11/23 15:19:40  prices
	It should work more easily out of the box.
	
	Revision 1.1  2005/11/10 04:19:55  prices
	periodic
	
	Revision 1.2  2005/06/10 14:42:02  prices
	Added a check for a slave.
	
	Revision 1.1.1.1  2005/05/28 02:01:12  prices
	Another restart
	
	
	Revision 1.4  2005/05/23 15:14:27  prices
	Changed the license to BSD.  All future releases will be under that license.
	
	Revision 1.3  2005/05/18 21:59:54  prices
	Lots of changes and added files.
	
	Revision 1.1.1.1  2005/05/06 22:10:03  prices
	Rewrite
	
	Revision 1.1  2005/05/06 13:32:48  prices
	Checks the databases for problems and fixes them.
	
	

	@verbatim
	Copyright (c) 2005 Scott Price
	All rights reserved.
	Released under the BSD License:

	Redistribution and use in source and binary forms, with or without modification, 
	are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, 
    this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, 
    this list of conditions and the following disclaimer in the documentation and/or 
    other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
	"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
	LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
	FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
	COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
	INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
	BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
	CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
	LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
	ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	POSSIBILITY OF SUCH DAMAGE.
	@endverbatim


	@cond SCRIPTS
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
/**
	@endcond
*/

?>
