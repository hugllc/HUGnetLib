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

	@internal
	$Id$
	@file lib/forms.inc.php

*/
require_once 'HTML/QuickForm.php';

/**
	Creates a popup window inside the browser window.
*/
class dfQuickForm extends HTML_QuickForm
{
	var $_dfRenderer = NULL;   //!< The renderer that we are going to use;
	var $_dfElements = array(
		'default' => "\n\t<tr>\n\t\t<td class=\"formLabel\"><!-- BEGIN required --><span class=\"formRequired\">*</span><!-- END required -->{label}</td>\n\t\t<td class=\"formInput\"><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>",
		'fullWidth' => "\n\t<tr>\n\t\t<td class=\"formLabel\"><!-- BEGIN required --><span class=\"formRequired\">*</span><!-- END required -->{label}</td>\n\t</tr>\n\t<tr>\n\t\t<td colspan=\"2\" class=\"formInput\"><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>",
	);
	

	/**
		@brief Creates the table.
		@param $attrib array Attribute array with $colName => $attributes pairs.
		@param $class1 string the class to use for every other row
		@param $class2 string the class to use for the other rows.	
	*/
	function dfQuickForm($formName = '', $method = 'post', $action = '', $target = '_self', $attributes = NULL, $trackSubmit = FALSE, $renderer=NULL) 
	{
		$this->HTML_QuickForm($formName, $method, $action, $target, $attributes, $trackSubmit);
		if (!is_object($renderer)) {
			$this->_dfRenderer =& $this->defaultRenderer();
		} else {
			$this->_dfRenderer =& $renderer;
		}
		$this->_dfRenderer->setElementTemplate($this->_dfElements['default']);
	}

	function setElementTemplate($template, $element) {
		if (method_exists($this->_dfRenderer, 'setElementTemplate')) {
			$this->_dfRenderer->setElementTemplate($this->_dfElements[$template], $element);
		}
		
	}

	/**
		@brief Creates the table.
		@param $attrib array Attribute array with $colName => $attributes pairs.
		@param $class1 string the class to use for every other row
		@param $class2 string the class to use for the other rows.	
	*/
	function getRenderer() {
		return $this->_dfRenderer;
	}

	/**
		@brief Creates the table.
		@param $attrib array Attribute array with $colName => $attributes pairs.
		@param $class1 string the class to use for every other row
		@param $class2 string the class to use for the other rows.	
	*/

	function toHTML($text=NULL)
	{
		$this->accept($this->_dfRenderer);
		return $this->_dfRenderer->toHTML($text);
	}

}

?>
