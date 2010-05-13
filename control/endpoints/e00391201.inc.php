<?php
/**
 * This is the control code for the 0039-12 endpoints with the FET daughter board.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @deprecated since version 0.9.0
 *
 */

require dirname(__FILE__).'/../group.inc.php';

$form = new HTML_QuickForm('fetControl');
$form->addElement('hidden', 'module', $_REQUEST['module']);
$form->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
$form->addElement('hidden', 'DeviceKey');
$form->addElement('hidden', 'noFetchSetup', true);
for ($fet = 0; $fet < 4; $fet++) {
    $location = (isset($devInfo['Location'][$fet])) ? $devInfo['Location'][$fet] : "FET ".$fet;
    $form->addElement('header', null, $location." : ".$devInfo['FET'.$fet.'pMode']." Mode");
    $options = array(0 => 'Digital', 1 => 'Analog - Hi Z', 2 => 'Analog - Voltage', 3 => 'Analog - Current');
    $form->addElement('select', 'FET'.$fet.'Mode', "Mode:", $options);
    switch($devInfo['FET'.$fet.'Mode'])
    {
        case 0:
            $radio   = array();
            $radio[] = $form->createElement('radio', 'FET'.$fet, null, 'Off', 0);
            $radio[] = $form->createElement('radio', 'FET'.$fet, null, 'On', 128);
            $form->addGroup($radio, null, "Power:");
            break;
        case 1:
            break;
        default:
            $form->addElement('text', 'FET'.$fet, "Set Point:", array('size' => 3, 'maxlength' => 3));
            $form->addRule('FET'.$fet, "Set Point must be numeric", 'numeric', null, 'client');
            $form->addRule('FET'.$fet, "Set Point can not be empty", 'required', null, 'client');
            $form->addElement('select', 'FET'.$fet.'Mult', "Multiplier:", array(0,1,2,3));
            break;
    }
}
$form->setConstants($devInfo);
$form->addElement('submit', 'postSetup', 'Update');
if (isset($_REQUEST['postSetup']) && $form->validate()) {
    $pktData = array();
    $mode = 0;
    for ($i = 0; $i < 4; $i++) {
        $data   = (int) $_REQUEST['FET'.$i.'Mode'] & 0x03;
        $data <<= (2*$i);
        $mode  |= $data;
    }
    $pktData[] = $mode;
    for ($i = 0; $i < 4; $i++) {
        switch($_REQUEST['FET'.$fet.'Mode'])
        {
            case 0:
                if ($_REQUEST['FET'.$i] == 0) {
                    $pktData[] = 0;
                } else {
                    $pktData[] = 0x80;
                }
                break;
            case 1:
                $pktData[] = 0;
                break;
            default:
                $pktData[] = (int) $_REQUEST['FET'.$i];
                break;
        }
    }

    for ($i = 0; $i < 4; $i++) {
        $pktData[] = (int) $_REQUEST['FET'.$i.'Mult'];
    }
    $return = $endpoint->setConfig($devInfo, 4, $pktData);

    $url = getMyURL(array("DeviceKey"))."?module=".$_REQUEST['module']."&hugnetengr_op=".$_REQUEST['hugnetengr_op']."&DeviceKey=".$devInfo['DeviceKey']."&mode=control";
    if ($return) header("Location: ".$url);

}
print $text.$form->toHTML();

?>