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
/**
	$Id$
	@brief A class for using templates.

	@par References	
	This is a very simple template class that I wrote after reading:
	http://www.massassi.com/php/articles/template_engines/ .  It is an
	excellent article on template engines.   The article has the 
	following copyright on it 
	'© Copyright 2003 Brian Lozier (brian@massassi.net)'.

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

	$Log: template.inc.php,v $
	Revision 1.1  2005/11/10 04:19:55  prices
	periodic
	
	Revision 1.4  2005/07/06 20:59:21  prices
	Fixed a lot and blogging works now.
	
	Revision 1.3  2005/07/01 20:48:47  prices
	fixed many things.
	
	Revision 1.2  2005/06/07 01:32:49  prices
	It now creates a reference to the variables, instead of copying them.
	
	Revision 1.1.1.1  2005/05/28 02:01:12  prices
	Another restart
	
	
	Revision 1.6  2005/05/23 15:14:27  prices
	Changed the license to BSD.  All future releases will be under that license.
	
	Revision 1.5  2005/05/18 21:59:54  prices
	Lots of changes and added files.
	
	Revision 1.2  2005/05/18 20:22:34  prices
	Fixes.
	
	Revision 1.1.1.1  2005/05/06 22:10:03  prices
	Rewrite
	
	Revision 1.3  2005/05/06 18:48:15  prices
	Added some functions and fixed some bugs.
	
	Revision 1.2  2005/05/04 18:25:48  prices
	Some fixes
	
	Revision 1.1  2005/05/02 20:06:09  prices
	Inception
	
	Revision 1.2  2005/04/25 22:27:12  prices
	Documentation changes.
	
	
*/

/**
	@brief This is for making templates
	
	
	@par References
	This is a very simple template class that I wrote after reading:
	http://www.massassi.com/php/articles/template_engines/ .  It is an
	excellent article on template engines.   The article has the 
	following copyright on it 
	'© Copyright 2003 Brian Lozier (brian@massassi.net)'.

	
 */
class template {

	var $file = NULL;
	/**
		@private
		@brief This stores the variables that will be allowed in the template.

		This is an indexed array of "variable" => "Value" pairs.  Value can be anything
	*/
	var $useVars = array();
	/**
		@private
		@brief The names associated with the files.
	*/
	var $name = array();
	
	/**
		@brief PHP4 Constructor
		@param $file string The file to use.  Must be a path relative to the PHP include_path.	
		@param $defFile string The file if template::file doesn't exist.  Must be a path relative to the PHP include_path.	
	*/	
	function template($file = NULL, $defFile = NULL)
	{
		$this->__construct($file, $defFile);
	}

	/**
		@brief PHP5 Constructor
		@param $file string The file to use.  Must be a path relative to the PHP include_path.	
		@param $defFile string The file if template::file doesn't exist.  Must be a path relative to the PHP include_path.	
	
	*/	
	function __construct($file = NULL, $defFile = NULL)
	{
		if (is_string($defFile)) $this->file($defFile, 'default');
		if (is_array($file)) {
			foreach($file as $k => $f) {
				if (is_string($f)) {
					$this->file($f, $k);
				}
			}
		} else if (is_string($file)) {
			$this->file($file);
		}
	}

	/**
		@private
		@brief Sets the file
		@param $file string The name of the file
		@note If $name is an array, $value is not used.
	*/
	function reset()
	{
		$this->useVars = array();
	}

	/**
		@private
		@brief Sets the file
		@param $file string The name of the file
		@note If $name is an array, $value is not used.
	*/
	function file($file, $name='file')
	{
		$name = trim(strtolower($name));
		if ($name != 'default') $this->name[] = $name;
		if (is_file($file)) {
			$this->file[$name] = $file;
			$return = TRUE;
		} else {
			$return = FALSE;
		}
		return($return);
	}


	/**
		@brief Sets variables
		@param $name Mixed The name of the variable to set, or an indexed array of variables to set
		@param $value Mixed Can be anything.  Normally a string or array.
		@param $overWrite Bool if set to false it won't overwrite values that are already present.
		@note If $name is an array, $value is not used.
	*/
	function set($name, $value = FALSE, $overWrite = TRUE)
	{
		if (is_array($name)) 
		{
			foreach($name as $key => $val) 
			{
				$this->set($key, $val, $overWrite);
			}
		} else if (is_string($name)) 
		{
			if ($overWrite || !isset($this->useVars[$name]))
			{
				$this->useVars[$name] =& $value;
			}
		}
	}

	/**
		@brief displays the template.
		@param $file string The file to use.  Must be a path relative to the PHP include_path.	
		@param $useVars array Indexed array of "variable" => "value" pairs to have available to the template
		@param $defFile string The file if template::file doesn't exist.  Must be a path relative to the PHP include_path.	
		@return The results of including the template.
	*/
	function get($file = NULL, $useVars = FALSE, $defFile = NULL)
	{
		extract($this->useVars);
		if (is_array($useVars)) extract($useVars);
		if ($file !== NULL) $this->file($file);
		if ($defFile !== NULL) $this->file($defFile, 'default');
		ob_start();
		$found = FALSE;
		$useFiles = $this->name;
		$useFiles[] = 'default';
		foreach($useFiles as $ftype) {
			if (is_file($this->file[$ftype])) {
				include($this->file[$ftype]);
				$found = TRUE;
				break;
			}
		}
		if (!$found) $contents = "Template Not Found";
		$contents = ob_get_contents();
		ob_end_clean();
		return($contents);
		
	}
	
	function getTemplates($path) {
		$return = array();
		$lDir = dir($path);
		if (is_object($lDir)) {
			while(false !== ($temp = $lDir->read())) {
				$temp = trim(strtolower($temp));
				if ((substr($temp, 0, 3) != 'cvs') && (substr($temp, 0, 1) != ".") && is_dir($path."/".$temp)) {
					$return[] = $temp;
				}
			}
		}
		return($return);
	}
}

?>