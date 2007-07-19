<?php
/**

	@internal
	$Id$
	@file include/popups.inc.php

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


	$Log: popups.inc.php,v $
	Revision 1.1  2005/11/10 04:19:55  prices
	periodic
	
	Revision 1.1.1.1  2005/05/28 02:01:12  prices
	Another restart
	
	
	Revision 1.4  2005/05/23 15:14:27  prices
	Changed the license to BSD.  All future releases will be under that license.
	
	Revision 1.3  2005/05/18 21:59:54  prices
	Lots of changes and added files.
	
	Revision 1.2  2005/05/18 20:22:34  prices
	Fixes.
	
	Revision 1.1.1.1  2005/05/06 22:10:03  prices
	Rewrite
	
	Revision 1.1  2005/05/06 18:48:15  prices
	Added some functions and fixed some bugs.
	
	Revision 1.3  2005/05/03 02:45:10  prices
	Moved over more toward completely using the template class.
	
	Revision 1.2  2005/04/30 03:14:26  prices
	I think everything works now.
	
	Revision 1.1  2005/04/28 19:49:00  prices
	This is periodic.  I am not done with the changes.  This is a massive overhaul
	to make it work with PEAR instead of dragonflyportal.
	


*/

/**
	Creates a popup window inside the browser window.
*/
class popup {
	/**
		@private
		@brief We identify the popups with this	
	*/
	var $index = 0;
	/**
		@private
		@brief The output goes here.
	*/
	var $Output = array();

	/**
		@private
		@brief Whether or not to show the popups
	*/
	var $show = TRUE;
	
	
	/**	
		@brief Constructor	
	*/
	function popup($show=TRUE) {
		$this->__construct($show);
	}

	/**	
		@brief php5 Constructor	
	*/
	function __construct($show=TRUE) {
		if ($show == 0) {
			$this->show = FALSE;
		}
	}
	
	/**
		@brief Creates a popup link
		@return onMouseOver and onMouseOut methods to cause a popup window to happen
		@param text String The text that goes inside the window that pops up.
		@sa popup.js
		
		This function sets up a popup window.  The return text is the onMouseOver and onMouseOut
		hooks for HTML tags.  Any tags that can take those two hooks can take the output of this
		function and have the window pop up when the mouse goes over that entity.
	
	*/
	function getLink($text){

	   if ((trim($text) != "") && $this->show) {
	      $return = " onMouseOver='showPopup(".$this->index.");' ";
	      $return .= " onMouseOut='hidePopup(".$this->index.");' ";
	
	      $this->Output[$this->index] = '<DIV CLASS="PopUp" NAME="'.$this->index.'" ID="'.$this->index.'" STYLE="position:absolute; top: 0; left: 0; z-index: 10; visibility: hidden;">';
			$this->Output[$this->index] .= "\n".$text."\n</DIV>\n";
	      $this->index++;
	   } else {
	      $return = "";
	   }
	   return($return);
	}

	/**
		@brief Creates a popup link
		@return onMouseOver and onMouseOut methods to cause a popup window to happen
		
		This function sets up a popup window.  The return text is the onMouseOver and onMouseOut
		hooks for HTML tags.  Any tags that can take those two hooks can take the output of this
		function and have the window pop up when the mouse goes over that entity.
	
	*/
	function getText() {
		if ($this->show) {
			$ret = implode("\n", $this->Output);
		} else {
			$ret = "";
		}
		return($ret);
	}
	
}

?>