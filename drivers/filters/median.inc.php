<?php
/**
	$Id: filters.inc.php 264 2007-09-22 03:42:00Z prices $
	@file filters/median.inc.php
	@brief Class for median filters.
	
	
*/

if (!class_exists('medianFilter') && class_exists("filter_base")) {
    $this->add_generic(array("Name" => "medianFilter", "Type" => "filter", "Class" => "medianFilter"));
    
    class medianFilter extends filter_base
    {
        var $filters = array(
            "medianFilter" => array(
                "longName" => "Median Filter",
                "minimumRows" => 3,
            ),
        );
    
    }
}

?>