<php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/** $Id$
	@file log.inc.php
	@author Scott L. Price (prices@dflytech.com)
	@date 04/09/2004
	@brief This is the cointainer include file.

	$Log: log.inc.php,v $
	Revision 1.1  2005/11/10 04:19:55  prices
	periodic
	
	Revision 1.2  2005/07/26 18:39:28  prices
	Fixed some problems.
	
	Revision 1.1.1.1  2005/05/28 02:01:12  prices
	Another restart
	
	
	Revision 1.6  2005/05/23 15:14:27  prices
	Changed the license to BSD.  All future releases will be under that license.
	
	Revision 1.5  2005/05/20 01:51:13  prices
	Something happened to the old version.  It was corrupt.
	
	Revision 1.3  2005/05/18 21:59:54  prices
	Lots of changes and added files.
	
	Revision 1.2  2005/05/18 20:22:34  prices
	Fixes.
	
	Revision 1.1.1.1  2005/05/06 22:10:03  prices
	Rewrite
	
	Revision 1.1  2005/05/03 16:08:05  prices
	class for logging page hits.
	
	Revision 1.17  2004/09/09 16:35:34  prices
	Added a couple of bots.
	
	Revision 1.16  2004/08/29 21:22:22  prices
	Added a spider
	
	Revision 1.15  2004/08/26 16:49:08  prices
	Added delete var and some more bot names.
	
	Revision 1.14  2004/07/01 16:14:02  prices
	Added another spider
	
	Revision 1.13  2004/05/18 21:51:09  prices
	Added a way to do sites.
	
	Revision 1.12  2004/05/13 12:34:50  prices
	Added some total fields in the stats.  I also added a couple of search engines.
	
	Revision 1.11  2004/05/12 17:51:20  prices
	Many changes and bug fixes.  Most having to do with spiders.
	
	Revision 1.10  2004/04/28 16:26:51  prices
	Refined the SaveAfter function to simplify it.
	
	Revision 1.9  2004/04/28 14:25:37  prices
	We are now able to send a log after the page is sent to the user.
	
	Revision 1.8  2004/04/28 13:54:54  prices
	Changed a print out.
	
	Revision 1.7  2004/04/23 16:04:17  prices
	Moved the class here, with the actual logging function being part of the class now.
	
	Revision 1.6  2004/04/22 19:14:58  prices
	Added the script name to the log
	
	Revision 1.5  2004/04/22 16:43:35  prices
	Added entry for security.
	
	Revision 1.4  2004/04/09 17:05:43  prices
	It now logs the server name correctly.
	
	Revision 1.3  2004/04/09 17:01:12  prices
	Now setting $LogDB will change what database the page accesses are made to.
	
	Revision 1.2  2004/04/09 16:52:53  prices
	It now logs the request method
	
	Revision 1.1  2004/04/09 16:50:55  prices
	Inception.  This file logs the web access.
	
	
	Including this file will log the page to the "Log" database table.

	@par

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

*/
//add_debug_output("******** Start of file ".__FILE__." *********<BR>\n<BR>\n ");

if (!class_exists(pageLog)) {
	/** 
	    @brief Creates menus on web pages
	    @todo Add more options for different kinds of menus
	    This class creates menus based on an array input.
	   
	*/
	class pageLog extends MDB_QueryWrapper {
		var $table = "Log";				//!< The database table to use
		var $primaryCol = "LogKey";	 //!< This is the Field name for the key of the record

	
		function SaveAfter() {
			register_shutdown_function(array(&$this, 'SaveLog'));
		}
		
		function Save() {
			$this->SaveLog();
		}
	
		function SpiderList() {
			$Bots = array(	"GoogleBot", "TurnitinBot", "QuepasaCreep", "SurveyBot", "google",
								"msnbot", "Baiduspider", "ZyBorg", "Uptimebot",
								"grub-client", "Clustered-Search-Bot", "IlTrovatore-Setaccio",
								"INGRID", "NPBot", "Infoseek SideWinder", "Netcraft Web Server Survey",
								"GoForIt.com", "Slurp", "http://www.almaden.ibm.com/cs/crawler",
								"mozDex", "Ask Jeeves", "semanticdiscovery","Vagabondo",
								"WinHttp.WinHttpRequest", "Microsoft-WebDAV-MiniRedir",
								"Gigabot", "Xenu Link Sleuth", "psbot", "Scooter",
								"Gaisbot", "Seekbot", "sohu-search", "Nutch", "walhello","NG", "Pompos",
								"LinkWalker", "ObjectsSearch", "ZipppBot", "PEERbot", "SSM Agent",
								"Clushbot", "NaverBot", "webcollage",
								);
			return($Bots);			
		}
	
		function IsSpider($Record) {

			foreach($this->SpiderList() as $bot) {
				if (@stristr($Record["UserAgent"], $bot) !== FALSE) return(TRUE);
			}
			if (trim($Record["UserAgent"]) == "") return(TRUE);
			return($false);
		}
	
		function MarkSpiders() {

/*
			$where = $this->Modify["WHERE"];
			$this->Modify["WHERE"] = array();
			$sets = $this->Modify["SETS"];
			$this->Modify["SETS"] = array("Spider");
			$this->Modify["WHERESEARCH"] = array("UserAgent");
			$requires = $this->Modify["REQUIRES"];
			unset($this->Modify["REQUIRES"]);
			foreach($this->SpiderList() as $bot) {
				$Info = array();
				$Info["Spider"] = "YES";
				$Info["UserAgent"] = $bot;
				$this->Modify($Info);
			}
			unset($this->Modify["WHERESEARCH"]);
			$this->Modify["WHERE"] = array("UserAgent");
			$Info["Spider"] = "YES";
			$Info["UserAgent"] = "";
			$this->Modify($Info);
			

			$this->Modify["WHERE"] = $where;
			$this->Modify["SETS"] = $sets;
			$this->Modify["REQUIRES"] = $requires;
*/
		}


		function SaveLog() {
			if (($_SERVER["HTTPS"] == "on") || ($log["LocalPort"] == 80)) {
				$proto = "https://";
			} else {
				$proto = "http://";
			}
			$Info["Date"] = date("Y-m-d H:i:s");
			$Info["RemoteAddress"] = $_SERVER["REMOTE_ADDR"];
			$Info["LocalAddress"] = $_SERVER["SERVER_ADDR"];
			$Info["LocalName"] = $proto.trim($_SERVER["HTTP_HOST"]);
			$Info["LocalPort"] = $_SERVER["SERVER_PORT"];
			$Info["Referrer"] = $_SERVER["HTTP_REFERER"];
			$Info["RequestURI"] = $_SERVER["REQUEST_URI"];
			if (isset($_SERVER["SCRIPT_FILENAME"])) {
				$Info["ScriptPath"] = $_SERVER["SCRIPT_FILENAME"];
			} else {
				$Info["ScriptPath"] = $_SERVER["PATH_TRANSLATED"];
			}
			$Info["ScriptName"] = $_SERVER["SCRIPT_NAME"];
			$Info["UserAgent"] = $_SERVER["HTTP_USER_AGENT"];
			$Info["RequestMethod"] = $_SERVER["REQUEST_METHOD"];
			if (isset($_SERVER["HTTPS"])) {
				$Info["Secure"] = $_SERVER["HTTPS"];
			} else {
				$Info["Secure"] = "off";
			}
			if (isset($_SESSION["perm_user_id"])) {
				$Info["UserName"] = $_SESSION["perm_user_id"];
			}
			if ($this->IsSpider($Info)) {
				$Info["Spider"] = "YES";
			}
			if ($_SESSION["BADBOT"]) {
				$Info["Spider"] = "BAD";
			}
			foreach($Info as $k => $i) {
				if ($i === NULL) $Info[$k] = "";
			}

			$ret = $this->add($Info);
		}
		
	
		function AnalyzeLogs($res, $Spider="NO") {
			$this->Logs = array();
			$Visits = array();

			foreach($res as $log) {
				if ((trim(strtoupper($Spider)) == trim(strtoupper($log["Spider"]))) || (trim(strtoupper($Spider)) == "ALL")) {
					// Get the remote domain and TLD
					$Remote = explode(".", $log["RemoteName"]);
					if (!is_numeric($Remote[count($Remote) - 1])) {
						$TLD = strtolower(trim($Remote[count($Remote) - 1]));
						$domain = strtolower(trim($Remote[count($Remote) - 2])).".".strtolower(trim($Remote[count($Remote) - 1]));
					}
		
					// Get the unix date of the log
					$UDate = strtotime($log["Date"]);
		
					// Check to see if this is a new visit
					$NewVisit = FALSE;
					if (($UDate - $Visits[$log["RemoteAddress"]][$log["LocalAddress"]][$log["LocalPort"]]) > $this->VisitTimeout) {
						$NewVisit = TRUE;
					}
					$Visits[$log["RemoteAddress"]][$log["LocalAddress"]][$log["LocalPort"]] = $UDate;
						
					// Remove all of the stuff after the ? in the referer element.
					if (trim($log["Referrer"]) != "") {
						$Ref = explode("?", $log["Referrer"]);
					} else {
						$Ref[0] = "none";
					}
		
					// Analyze the user agent.
					if (stristr($log["UserAgent"], "Mozilla") != FALSE) {
						$UserAgent = explode(";", $log["UserAgent"]);
						if (stristr($log["UserAgent"], "MSIE") != FALSE) {
							$UserAgent = trim($UserAgent[1]);
						} else if (stristr($log["UserAgent"], "Gecko") != FALSE) {
							$UserAgent = trim(substr(stristr($UserAgent[4], ")"), 1));
							if (trim($UserAgent) == "") $UserAgent = $log["UserAgent"];
						} else {
							$UserAgent = $log["UserAgent"];
						}
					} else {
		
						$UserAgent = $log["UserAgent"];
					}
		
					// Get the local Name and URL
		/*
					if ((trim(strtolower($log["Secure"])) == "on") || ($log["LocalPort"] == 443)) {
						$LocalName = "https://";
					} else {
						$LocalName = "http://";
					}
		*/
					$LocalName = strtolower(trim($log["LocalName"]));
					$LocalURL = trim($log["ScriptName"]);
		
					$year = (int) date("Y", $UDate);
					$month = (int) date("m", $UDate);
					$day = (int) date("d", $UDate);
					$hour = (int) date("H", $UDate);
		
						// Count stuff
					foreach(array("Hits", "Visits") as $type) {
						if (($type != "Visits") || $NewVisit) {
							$this->Logs["Raw"][$year][$month][$day][$hour][$type]++;
							$this->Logs["REFERER"][$year][$month][strtolower($Ref[0])][$type]++;
							$this->Logs["USER_AGENT"][$year][$month][$UserAgent][$type]++;
							$this->Logs["OS"][$year][$month][$OS][$type]++;
							$this->Logs["SITE"][$year][$month][$log["RemoteName"]][$type]++;
							$this->Logs["URL"][$year][$month][$LocalURL][$type]++;
							$this->Logs["DOMAIN"][$year][$month][$domain][$type]++;
							$this->Logs["TLD"][$year][$month][$TLD][$type]++;
							$this->Logs["TOTAL"][$year][$month][0][$type]++;
							$this->Logs["HOURLY"][$year][$month][$hour][$type]++;
							$this->Logs["DAILY"][$year][$month][$day][$type]++;
							$this->Logs["TOTAL"][$year][$month][0]["Sites"][$log["RemoteName"]] = 0;
							$this->Logs["HOURLY"][$year][$month][$hour]["Sites"][$log["RemoteName"]] = 0;
							$this->Logs["DAILY"][$year][$month][$day]["Sites"][$log["RemoteName"]] = 0;
						}
					}
					if (!isset($this->Logs["DNS"][$log["RemoteAddress"]]) || ($this->Logs["DNS"][$log["RemoteAddress"]] == "")) {
						$this->Logs["DNS"][$log["RemoteAddress"]] = trim(strtolower($log["RemoteName"]));
					}
				}
			}


		}
		function GetHourlyStats ($year, $month) {
	
			$year = (int) $year;
			$month = (int) $month;
	
			$temp = array();
			if (is_array($this->Logs["Hits"]["Raw"][$year][$month])) {
				$temp = array_keys($this->Logs["Hits"]["Raw"][$year][$month]);
			}
			$days = $temp[count($temp)-1] - $temp[0];
			if ($days == 0) $days = 1;
			$return = "<table style=\"border-spacing: 4px; text-align: center;\">\n";
			$return .= "	<tr class=\"header\">\n";
			$return .= "		<td rowspan=2>Hour</td>\n";
			$return .= "		<td colspan=3 style=\"background: lightblue;\">Hits</td>\n";
			$return .= "		<td colspan=3 style=\"background: yellow;\">Visits</td>\n";
			$return .= "	</tr>\n";
			$return .= "	<tr class=\"header\">\n";
			$return .= "		<td colspan=1 style=\"background: lightblue;\">Avg</td>\n";
			$return .= "		<td colspan=2 style=\"background: lightblue;\">Total</td>\n";
			$return .= "		<td colspan=1 style=\"background: yellow;\">Avg</td>\n";
			$return .= "		<td colspan=2 style=\"background: yellow;\">Total</td>\n";
			$return .= "	</tr>\n";
	
			for ($d = 0; $d <= 23; $d++) {
				if ($rclass == "row1") {
					$rclass = "row2";
				} else {
					$rclass = "row1";
				}
				$hits = $this->Logs["Hits"]["Total"]["Hour"][$d]+0;
				if (($thits = $this->Logs["Hits"]["Total"][$year][$month]["Hits"]) == 0) $thits = 1;
				$hitperc = number_format(($hits/$thits)*100, 2);
				$hitsavg = number_format($hits/$days);
				$visits = $this->Logs["Visits"]["Total"]["Hour"][$d]+0;
				if (($tvisits = $this->Logs["Visits"]["Total"][$year][$month]["Hits"]) == 0)$tvisits = 1;
				$visitperc = number_format(($visits/$tvisits)*100, 2);
				$visitsavg = number_format($visits/$days);
				$return .= "	<tr class=\"".$rclass."\">\n";
				$return .= "		<td>".$d."</td>\n";
				$return .= "		<td>".$hitsavg."</td>\n";
				$return .= "		<td>".$hits."</td>\n";
				$return .= "		<td>".$hitperc."%</td>\n";
				$return .= "		<td>".$visitsavg."</td>\n";
				$return .= "		<td>".$visits."</td>\n";
				$return .= "		<td>".$visitperc."%</td>\n";
	
				$return .= "	</tr>\n";
			}
	
			$return .= "</table>\n";
			return($return);
		}
		
	
		function GetDailyStats ($year, $month) {
	
			$year = (int) $year;
			$month = (int) $month;
	
			$days = (int) date("d", mktime(12, 0, 0, ($month+1), 0, $year));
			$return = "<table style=\"border-spacing: 4px; text-align: center;\">\n";
			$return .= "	<tr class=\"header\">\n";
			$return .= "		<td>Day</td>\n";
			$return .= "		<td colspan=2 style=\"background: lightblue;\">Hits</td>\n";
			$return .= "		<td colspan=2 style=\"background: yellow;\">Visits</td>\n";
			$return .= "		<td colspan=2 style=\"background: orange;\">Sites</td>\n";
			$return .= "	</tr>\n";
	
			for ($d = 1; $d <= $days; $d++) {
				if (isset($this->Logs["Hits"]["Total"][$year][$month][$d]["Hits"])) {
					if ($rclass == "row1") {
						$rclass = "row2";
					} else {
						$rclass = "row1";
					}
					$hits = $this->Logs["Hits"]["Total"][$year][$month][$d]["Hits"]+0;
					$hitperc = number_format(($hits/$this->Logs["Hits"]["Total"][$year][$month]["Hits"])*100, 2);
					$visits = $this->Logs["Visits"]["Total"][$year][$month][$d]["Hits"]+0;
					$visitperc = number_format(($visits/$this->Logs["Visits"]["Total"][$year][$month]["Hits"])*100, 2);
					$sites = count($this->Logs["Hits"]["Total"][$year][$month][$d]["Sites"]);
					$siteperc = number_format(($sites /count($this->Logs["Hits"]["ByIP"]))*100, 2);
					
					$return .= "	<tr class=\"".$rclass."\">\n";
					$return .= "		<td>".$d."</td>\n";
					$return .= "		<td>".$hits."</td>\n";
					$return .= "		<td>".$hitperc."%</td>\n";
					$return .= "		<td>".$visits."</td>\n";
					$return .= "		<td>".$visitperc."%</td>\n";
					$return .= "		<td>".$sites."</td>\n";
					$return .= "		<td>".$siteperc."%</td>\n";
		
					$return .= "	</tr>\n";
				}
			}
	
			$return .= "</table>\n";
			return($return);
		}
	
	/**
		@brief Prints referers from analyzed log information
	*/
		function GetReferers ($show) {
			$htotal = $this->Logs["Hits"]["ByReferrer"]["Total"];
			$vtotal = $this->Logs["Hits"]["ByReferrer"]["Total"];
		
			$return = $this->GetGeneric($show, "ByReferrer", "Referrer", $htotal, $vtotal);
			return($return);
		}
	/**
		@brief Prints referers from analyzed log information
	*/
		function GetGeneric ($show, $type, $name, $htotal=0, $vtotal=0) {
			if (!is_array($this->Logs["Hits"][$type])) return("");
		
			if ($htotal == 0) $htotal = $this->Logs["Hits"]["Total"]["Hits"];
			if ($htotal == 0) $htotal = 1;
			if ($vtotal == 0) $vtotal = $this->Logs["Visits"]["Total"]["Hits"];
			if ($vtotal == 0) $vtotal = 1;
			
			$total = count($this->Logs["Hits"][$type]);
			if ($total > $show) {
				$showing = $show;
			} else {
				$showing = $total;
			}
	
			$return = "<table style=\"border-spacing: 4px; text-align: center;\">\n";
			$return .= "	<tr class=\"header\">\n";
			$return .= "		<td colspan=5>Showing ".$showing." of ".$total."</td>\n";
			$return .= "	</tr>\n";
			$return .= "	<tr class=\"header\">\n";
			$return .= "		<td colspan=2 style=\"background: lightblue;\">Hits</td>\n";
			$return .= "		<td colspan=2 style=\"background: yellow;\">Visits</td>\n";
			$return .= "		<td>".$name."</td>\n";
			$return .= "	</tr>\n";
			$work = $this->Logs["Hits"][$type];
			asort($work, SORT_NUMERIC);
			$work = array_reverse($work);
			$count = 0;
	
			foreach($work as $name => $hit) {
				if (strtolower(trim($name)) != "total") {
					if ($rclass == "row1") {
						$rclass = "row2";
					} else {
						$rclass = "row1";
					}
					$hits = $this->Logs["Hits"][$type][$name]+0;
					$hitperc = number_format(($hits/$htotal)*100, 2);
					$visits = $this->Logs["Visits"][$type][$name]+0;
					$visitperc = number_format(($visits/$vtotal)*100, 2);
					
					$return .=	"	<tr class=\"".$rclass."\">\n";
					$return .= "		<td>".$hits."</td>\n";
					$return .= "		<td>".$hitperc."%</td>\n";
					$return .= "		<td>".$visits."</td>\n";
					$return .= "		<td>".$visitperc."%</td>\n";
					$return .= "		<td style=\"text-align: left;\">".$name."</td>\n";
					$return .= "	</tr>\n";
					$count++;
					if ($count >= $show) break;
				}
			} 
			$return .= "</table>\n";
			return($return);
		}
	/**
		@brief Prints referers from analyzed log information
	*/
		function GetUserAgents ($show) {
			$return = $this->GetGeneric($show, "ByUserAgent", "User Agent");
			return($return);
		}
			
		/**
			@brief Constructor
			@param $servers The servers to use.  Set to "" to use the default servers	
			@param $db String The database to use
			@param $options the database options to use.
		*/
		function log($servers, $db, $options=array()) 
		{
			$options['dbWrite'] = TRUE;
			parent::__construct($servers, $db, $options);
		}
	}
}	
//add_debug_output("******** End of file ".__FILE__." *********<BR>\n<BR>\n");
?>
