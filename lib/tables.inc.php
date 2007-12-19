<?php
/**
 * Class for building cute tables.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Lib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
require_once 'HTML/Table.php';
@require_once 'Spreadsheet/Excel/Writer.php';
require_once 'lib/forms.inc.php';

/**
 * Creates a popup window inside the browser window.
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Lib
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class DfTable
{
    /** @var array Alternate table formats */
    private $_altTableFormats = array('CSV');
    /** @var string Line end characters */
    private $_lineEnd = "\r\n";
    /** @var bool Whether to allow exporting */
    private $_export = true;
    /** @var bool Whether to add the filters */
    private $_filterForm = false;
    /** @var bool Whether to do the filtering */
    private $_filterRow = false;
    /** @var string Separator for CSV */
    private $_sep = ',';
    /** @var array Row Type */
    private $_rowType = array();
    /** @var array Subtotal columns */
    private $_subTotalCol = array();
    /** @var mixed Data */
    private $_firstData = null;
    /**
     * function
     *
     * @param string $name       The name of the table
     * @param array  $attributes Table attributes
     * @param int    $tabOffset  deprecated 
     *
     * @return none
     */
    function dfTable($name = 'Default', $attributes=null, $tabOffset=0)
    {

        $replace = array(' ', '"', "'");

        if (class_exists(Spreadsheet_Excel_Writer)) $this->_altTableFormats[] = "Excel";

        $this->_tableName = $name;
        $this->_tableFormatName = str_replace($replace, array(), $this->_tableName)."TableFormat";
        $type = trim($_REQUEST[$this->_tableFormatName]);
        if (array_search($type, $this->_altTableFormats) !== false) {
            $this->_tableType = $type;
        } else {
            $this->_tableType = 'HTML';
        }

    }
    /**
     * function
     *
     * @param bool $val Value to set _export to
     *
     * @return none
     */
    function setExport($val) 
    {
        $this->_export = (bool) $val;
    }
    /**
     * function
     *
     * @return none
     */
    function toHTML() 
    {
        switch($this->_tableType)
        {
        case 'CSV':
            if ($this->_export) {
                ob_end_clean();                     // End buffering and discard

                header("Content-Type: text/x-csv");
                header('Content-Disposition: attachment; filename="'.$this->_tableName.'.csv";');
                print $this->_CSVBuffer;
                die();
            }
        case 'Excel':
            if ($this->_export) {
                ob_end_clean();                     // End buffering and discard
                // sending HTTP headers
                $this->_ExcelWorkbook->send($this->_tableName.'.xls');
                // Let's send the file
                $this->_ExcelWorkbook->close();
                die();
                break;
            }
        default:
            $return = '';
            if (is_object($this->_HTML_Table)) {
                $table = $this->_HTML_Table->toHTML();
            }
            if (!empty($table)) {
                if ($this->_filterForm) {
                    $return .= $this->getFilterForm();
                }                
                if ($this->_export) {
                    $return .= "<div>Export table to: ";
                    $url = getMyURL(array($this->_tableFormatName));
                    $sep = "";
                    foreach ($this->_altTableFormats as $format) {
                        $return .= $sep;
                        $return .= '<a href="'.$url.$this->_tableFormatName.'='.urlencode($format).'">'.$format.'</a>';
                        $sep = " | ";
                    }
                    $return .= '</div>';
                }
            }
            $return .= $table;
            return $return;
            break;
        }
        
    }
    /**
     * function
     *
     * @param bool $row  Set whether to filter rows
     * @param bool $form Set whether to have the filter form
     *
     * @return none
     */
    function setFilter($row=false, $form=false) 
    {
        $this->_filterForm = (bool)$form;
        $this->_filterRow = (bool)$row;
    }
    /**
     * function
     *
     * @return none
     */
    function getFilter() 
    {

        foreach (array('Field', 'Contains', 'Filter') as $field) {
            $lfield = strtolower($field);
            if (isset($_REQUEST[$this->_tableFormatName.$field])) {
                $return[$lfield] = trim($_REQUEST[$this->_tableFormatName.$field]);
                $_SESSION['tableCache']['filter'][$this->_tableFormatName][$lfield] = $return[$lfield];
            } else if (isset($_SESSION['tableCache']['filter'][$this->_tableFormatName][$lfield])) {
                $return[$lfield] = $_SESSION['tableCache']['filter'][$this->_tableFormatName][$lfield];
            }
        }
        if (empty($return['field'])) list($return['field'], $tmp) = each($this->_listHead);
        $return['contains'] = (bool)$return['contains'];
        if (empty($return['filter'])) $this->_filterRow = false;
        return $return;
    }
    /**
     * function
     *
     * @param array $row the row to filter
     *
     * @return bool
     */
    function filter($row) 
    {
        if ($this->_filterRow) {
            return (is_string(strstr($row[$this->_filter['field']], $this->_filter['filter'])) == $this->_filter['contains']);
        } else {
            return true;
        }
    }
    /**
     * function
     *
     * @return none
     */
    function getFilterForm() 
    {
        $url = getMyUrl();
        $this->_filterForm = new dfQuickForm($this->_tableName.'Filter', 'post', $url);
        $group[] = &$this->_filterForm->createElement('select', $this->_tableFormatName.'Field', '', $this->_listHead);
        $options = array(1 => 'contains', 0 => 'does not contain');
        $group[] = &$this->_filterForm->createElement('select', $this->_tableFormatName.'Contains', '', $options);
        $group[] = &$this->_filterForm->createElement('text', $this->_tableFormatName.'Filter', '');
        $group[] = &$this->_filterForm->createElement('submit', $this->_tableFormatName.'Submit', 'Go');

        $this->_filterForm->addGroup($group, null, "Filter By:");
        
        $def = $this->getFilter();
        $def[$this->_tableFormatName.'Field'] = $def['field'];
        $def[$this->_tableFormatName.'Contains'] = $def['contains'];
        $def[$this->_tableFormatName.'Filter'] = $def['filter'];
        $this->_filterForm->setDefaults($def);
        
        return $this->_filterForm->toHTML();
    }

    /**
     * Sets up a list and builds the header.
     *
     * @param array  $header       Array of $colName => $headerText pairs.  The order these are in
     *       define the order of the columns in the list.
     * @param string $fill         String to fill into empty cells
     * @param int    $headerPeriod The number of rows until the header is printed again
     * @param bool   $addHeader    Whether the header should be included at the top
     *
     * @return none
     */
    function createList($header, $fill=null, $headerPeriod=0, $addHeader=true)
    {
        switch($this->_tableType) {
        case 'Excel':
            $this->_ExcelWorkbook = new Spreadsheet_Excel_Writer();
            $this->_autoFill = $fill;
            break;
        case 'CSV':
            $this->_CSVBuffer = "";
            break;
        default:
            $this->_HTML_Table = new HTML_Table($attributes, $tabOffset);
            $this->_HTML_Table->setAutoGrow(true);
            if ($fill !== null) {
                $this->_HTML_Table->setAutoFill($fill);
            }
            break;
        }
        $this->_listPage = 'Default';
        $this->_listRow = 0;
        $this->_headerPeriod = $headerPeriod;
        if (is_array($header)) {
            $this->_listHead = $header;
        } else {
            $this->_listHead = array();
        }
        $this->_filter = $this->getFilter();
        if ($addHeader) $this->addListHeaderRow();
    }
    /**
     * function
     *
     * @param string $subCol the name of the subtotal column
     * @param array  $cols   The array keys for this will be used as the
     *                       columns to subtotal
     *
     * @return none
     */
    function addListSubTotalCol($subCol, $cols) 
    {
        if (!is_array($cols)) return;
        $this->_subTotalCol[$subCol] = array_keys($cols);
    }
    /**
     * Returns a letter designation for the column number
     *
     * @param int $col The column number
     *
     * @return string
     */
    function _getColLetter($col) 
    {
        if ($col <= 26) {
            return chr(ord('A') + $col);
        } else {
            $ret = $this->_getColLetter($col/26);
            $ret .= $this->_getColLetter($col%26);
            return $ret;
        }
    }
    /**
     * function
     *
     * @param array $data The column information
     *
     * @return none
     */
    function _getListSubTotalCols($data) 
    {
        switch($this->_tableType) {
        case 'Excel':
            foreach ($this->_subTotalCol as $subCol => $cols) {
                if (!isset($data[$subCol])) {
                    $formula = "";
                    $sep = "";
                    $col = 0;
                    foreach ($this->_listHead as $key => $val) {
                        if (array_search($key, $cols) !== false) {
                            $formula .= $sep.$this->_getColLetter($col).($this->_listRow+1);
                            $sep = ",";
                        }
                        $col++;
                    }
                    $formula = "=SUM(".$formula.")";
                    $col = 0;
                    foreach ($this->_listHead as $key => $val) {
                        if ($key == $subCol) break;
                        $col++;
                    }
                    $format =& $this->_ExcelWorkbook->addFormat();
                    $format->setBorder(1);
                    $format->setAlign('center');
                    $this->_ExcelWorksheet->writeFormula($this->_listRow, $col, $formula, $format);
                }
            }
            break;
        default:
            foreach ($this->_subTotalCol as $subCol => $cols) {
                if (!isset($data[$subCol])) {
                    $subtotal = 0;
                    foreach ($cols as $colName) {
                        $subtotal += (float)strip_tags($data[$colName]);
                    }
                    $col = 0;
                    foreach ($this->_listHead as $key => $val) {
                        if ($key == $subCol) break;
                        $col++;
                    }
                    $this->_HTML_Table->setCellContents($this->_listRow, $col, (string) $subtotal);
                }
            }
            break;
        }
    } 

    /**
     * Adds a text divider to the list.    
     *
     * @param string $text   The text to put in the divider row
     * @param array  $attrib Attribute array for the HTML version
     * @param bool   $export Should this line be exported
     *
     * @return int The row
     */
    function addListDividerRow($text, $attrib=null, $export=true) 
    {
        switch($this->_tableType) {
        case 'CSV':
            break;
        case 'Excel':
            if ($export) {
                $this->_ExcelWorksheet =& $this->_ExcelWorkbook->addWorksheet($text);
                $this->_listRow = -1;
            }
            break;
        default:
            $this->_HTML_Table->setCellContents($this->_listRow, 0, stripslashes($text));

            $attrib['colspan'] = count($this->_listHead);
            $this->_HTML_Table->setRowAttributes($this->_listRow, $attrib);
        }
        $this->_rowType[$this->_listRow] = 'divider';
        return $this->_listRow++;
    }

    /**
     * Adds a row to the list.
     *
     * @return int The number of the row that was inserted
     */
    function addListHeaderRow() 
    {
        $col = 0;
        switch($this->_tableType) {
        case 'CSV':
            $sep = "";
            foreach ($this->_listHead as $head) {
                $this->_CSVBuffer .= $sep.strip_tags($head);
                $sep = ",";
            }
            $this->_CSVBuffer .= $this->_lineEnd;
            break;
        case 'Excel':
            if (!is_object($this->_ExcelWorksheet)) $this->addListDividerRow($this->_tableName);
            $format =& $this->_ExcelWorkbook->addFormat();
            $format->setBold();
            $format->setAlign('center');
            $format->setPattern(4);
            foreach ($this->_listHead as $head) {
                $this->_ExcelWorksheet->writeString($this->_listRow, $col++, strip_tags($head), $format);
            }
            break;
        default:
            foreach ($this->_listHead as $head) {
                $this->_HTML_Table->setHeaderContents($this->_listRow, $col++, $head);
            }
        }
        $this->_rowType[$this->_listRow] = 'header';
        return $this->_listRow++;
    }

    /**
     * Adds a row to the list.
     *
     * @param array $header array Data to use in the list.
     *
     * @return int The number of the row that was inserted
     */
    function updateListHeader($header) 
    {
        $col = 0;
        foreach ($this->_listHead as $key => $head) {
            if (isset($header[$key])) {
                $this->_listHead[$key] = $header[$key];
            }
        }
    }

    /**
     * function
     *
     * @param array $data        Array of row arrays
     * @param bool  $checkAtrrib deprecated
     *
     * @return none
     */
    function addManyListRows($data, $checkAttrib=false) 
    {
        if (is_array($data)) {
            switch($this->_tableType) {
            case 'CSV':
            case 'Excel':
                foreach ($data as $row) {
                    $listRow = $this->addListRow($row);    
                }
                break;
            default:
                foreach ($data as $row) {
                    $listRow = $this->addListRow($row, $row['attributes']);    
                }
            break;
            }
            return true;
        } else {
            return false;    
        }
    }

    /**
     * Adds a row to the list.
     *
     * @param array  $subCols The columns to total in the row
     * @param string $type    The type of rows we should total
     * @param array  $attrib  The attributes to add to the row
     * @param string $mytype  The type of this row
     *
     * @return int The number of the row that was inserted
     */
    function addListSubTotalRow($subCols, $type='data', $attrib=null, $mytype='subtotal') 
    {
        $col = 0;
        
        switch($this->_tableType) {
        case 'CSV':
            $sep = "";
            foreach (array_keys($this->_listHead) as $key) {
                $this->_CSVBuffer .= $sep.stripslashes(strip_tags($data[$key]));
                $sep = $this->_sep;
            }
            $this->_CSVBuffer .= $this->_lineEnd;
            break;
        case 'Excel':
            if (!is_object($this->_ExcelWorksheet)) $this->addListDividerRow($this->_tableName);
            $col = 0;
            foreach (array_keys($this->_listHead) as $key) {

                if ($subCols[$key] === true) {
                    $formula = '';
                    $sep = "";
                    for ($row = 0; $row < $this->_listRow; $row++) {
                        if ($this->_rowType[$row] == $type) {
                            $formula .= $sep.$this->_getColLetter($col).($row+1);
                            $sep = ",";
                        }
                    }
                    $formula = "=SUM(".$formula.")";
                    $format =& $this->_ExcelWorkbook->addFormat();
                    $format->setBorder(1);
                    $format->setAlign('center');
                    $this->_ExcelWorksheet->writeFormula($this->_listRow, $col, $formula, $format);
                } else {
                    $format =& $this->_ExcelWorkbook->addFormat();
                    $format->setBold();
                    $format->setAlign('center');
                    $format->setPattern(4);
                    $subtotal = $subCols[$key];
                    $this->_ExcelWorksheet->writeString($this->_listRow, $col, $subtotal, $format);
                }
                $col++;
            }                    
            break;
        default:
            $col = 0;
            foreach (array_keys($this->_listHead) as $key) {

                $subtotal = 0;
                if ($subCols[$key] === true) {
                    for ($row = 0; $row < $this->_listRow; $row++) {
                        if ($this->_rowType[$row] == $type) {
                            $subtotal += (float) strip_tags($this->_HTML_Table->getCellContents($row, $col));
                        }
                    }
                } else if (empty($subCols[$key])) {
                    $subtotal = '&nbsp;';
                    $this->_HTML_Table->setCellAttributes($this->_listRow, $col, array('class' => 'header'));
                } else {
                    $subtotal = $subCols[$key];
                    $this->_HTML_Table->setCellAttributes($this->_listRow, $col, array('class' => 'header'));
                }
                $this->_HTML_Table->setCellContents($this->_listRow, $col, (string)$subtotal);
                $col++;
            }                    
            break;
        }
        $this->_rowType[$this->_listRow] = $mytype;
        return $this->_listRow++;
    }

    /**
     * Adds a row to the list.
     *
     * @param $data array Data to use in the list.
     * @param array  $attrib  The attributes to add to the row
     * @param string $type    The type of rows we should add
     *
     * @return The number of the row that was inserted
     */
    function addListRow($data, $attrib=null, $type='data') 
    {
        $col = 0;
        
        if ($this->filter($data)) {
            switch($this->_tableType) {
            case 'CSV':
                $sep = "";
                foreach (array_keys($this->_listHead) as $key) {
                    $this->_CSVBuffer .= $sep.stripslashes(strip_tags($data[$key]));
                    $sep = $this->_sep;
                }
                $this->_CSVBuffer .= $this->_lineEnd;
                break;
            case 'Excel':
                if (!is_object($this->_ExcelWorksheet)) $this->addListDividerRow($this->_tableName);
                foreach (array_keys($this->_listHead) as $key) {
                    if (isset($data[$key])) {
                        $cell = stripslashes(strip_tags($data[$key]));
                    } else if ($this->_autoFill != null) {
                        $cell = stripslashes(strip_tags($this->_autoFill));
                    } else {
                        unset($cell);
                    }

                    $this->_ExcelWorksheet->write($this->_listRow, $col++, $cell);
                }
                break;
            default:
                if ((($this->_listRow % $this->_headerPeriod) == 0) && ($this->_headerPeriod != 0))
                {
                    $this->addListHeaderRow();
                }
                foreach (array_keys($this->_listHead) as $key) {
                    $this->_HTML_Table->setCellContents($this->_listRow, $col++, stripslashes($data[$key]));
                    if (is_array($attrib)) $this->_HTML_Table->setRowAttributes($this->_listRow, $attrib, true);
                }                    
                break;
            }
            $this->_rowType[$this->_listRow] = $type;
            $this->_getListSubTotalCols($data);
            if ($this->_firstData == null) $this->_firstData = $this->_listRow;
            return $this->_listRow++;
        } else {
            return false;
        }
    }

    /**
     * Finishes the list table.
     *
     * @param $attrib array Attribute array with $colName => $attributes pairs.
     * @param $class1 string the class to use for every other row
     * @param $class2 string the class to use for the other rows.    
     *
     * @return none
     */
    function finishList($attrib = array(), $class1='row1', $class2='row2') 
    {
        switch($this->_tableType) {
        case 'CSV':
            break;
        case 'Excel':
            break;
        default:
            $this->_HTML_Table->altRowAttributes($this->_firstData, array('class' => $class1), array('class' => $class2), true);
            
            $col = 0;
            foreach (array_keys($this->_listHead) as $key) {
                if (isset($attrib[$key])) {
                    $this->_HTML_Table->updateColAttributes($col, $attrib[$key]);
                }
                $col++;
            }    
            break;
        }
    }


    /**
     * Sets up a list and builds the header.
     * @param array  $header       Array of $colName => $headerText pairs.  The order these are in
     *       define the order of the columns in the list.
     * @param array  $data         Data to put into the info table
     * @param string $fill         String to fill into empty cells
     * @param int    $headerPeriod The number of rows until the header is printed again
     *
     * @return none
     */
    function createInfo($header=array(), $data=array(), $fill=null, $headerPeriod=0)
    {
        $this->setExport(false);
        $this->setFilter(false);
        $this->_HTML_Table = new HTML_Table($attributes, $tabOffset);
        $this->_HTML_Table->setAutoGrow(true);
        if ($fill !== null) {
            $this->_HTML_Table->setAutoFill($fill);
        }

        $this->_infoRow = 0;
        $this->_infoHeader = $header;
        $this->addInfoData($data, $header);
    }
    /**
     * function
     *
     * @param array  $data   Data to put into the info table
     * @param array  $header Array of $colName => $headerText pairs.  The order these are in
     *       define the order of the columns in the list.
     *
     * @return none
     */
    function addInfoData($data, $header=null) 
    {
        $header = is_array($header) ? $header : $this->_infoHeader;
        $col = 0;
        foreach ($header as $key => $head) {
            $this->_infoKeys[] = $this->_infoRow;
            $this->addInfoRow($head, $data[$key]);
        }
    
    }


    /**
     * Adds a row to the list.
     *
     * @param string $head The header to add the data to.
     * @param string $data Data to use.
     *
     * @return The number of the row that was inserted
     */
    function addInfoRow($head, $data) 
    {
        $col = 0;
        if (is_array($data)) {
            $this->_HTML_Table->setHeaderContents($this->_infoRow, $col, $head);
            $this->_HTML_Table->setCellAttributes($this->_infoRow, $col, array('colspan' => 2));
            $this->_infoRow++;

            foreach ($this->_infoHeader as $k => $h) {
                $this->addInfoRow($h, $data[$k]);
            }

        } else {
            if ($data !== null) {
                $this->_HTML_Table->setHeaderContents($this->_infoRow, $col++, $head);
                $this->_HTML_Table->setCellContents($this->_infoRow, $col++, stripslashes($data));
                $this->_infoRow++;
            }
        }
        return $this->_infoRow;
    }

    /**
     * Finishes the list table.
     *
     * @param $attrib array Attribute array with $colName => $attributes pairs.
     * @param $class1 string the class to use for every other row
     * @param $class2 string the class to use for the other rows.    
     *
     * @return none
     */
    function finishInfo($attrib = array(), $class1='row1', $class2='row2') 
    {
        $this->_HTML_Table->altRowAttributes(0, array('class' => $class1), array('class' => $class2), true);
        foreach ($this->_infoKeys as $key => $row) {
            if (isset($attrib[$row])) {
                $this->_HTML_Table->updateRowAttributes($key, $attrib[$row]);
            }
        }    
        
    }


}

?>
