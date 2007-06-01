<?php
/**

	@internal
	$Id: forms.inc.php 52 2006-05-14 20:51:23Z prices $
	@file lib/forms.inc.php

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


	$Log: forms.inc.php,v $
	Revision 1.1  2005/11/10 04:19:55  prices
	periodic
	
	Revision 1.2  2005/07/06 00:28:21  prices
	Fixes for a lot of different things.
	
	Revision 1.1  2005/06/28 22:19:35  prices
	Ported everything to the new LiveUser.  I also updated it so that everything
	is xhtml compliant, and made the forms so that they can be controlled.
	
	

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
