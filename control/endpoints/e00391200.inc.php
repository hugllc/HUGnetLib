<?php
/**
 * This is the control code for the 0039-12 endpoints with the temperature daughter board
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
 */

require dirname(__FILE__).'/../group.inc.php';

//print get_stuff($devInfo);
$form = new HTML_QuickForm('fetControl');
$form->addElement('hidden', 'DeviceKey');
$form->addElement('hidden', 'noFetchSetup', true);
$form->addElement('header', null, 'Device Specific Options');
$form->addElement('text', 'TimeConstant', 'Time Constant:', array('size' => 3, 'maxlength' => 3));
foreach ($devInfo['Types'] as $sensor => $type) {

    $options = $endpoint->drivers[$devInfo['Driver']]->sensorTypes;
    $form->addElement('select', 'Types['.$sensor.']', "Sensor ".$sensor." Type:", $options);
}
$form->addRule('TimeConstant', 'Time Constant can not be empty', 'required', null, 'client');
$form->addRule('TimeConstant', 'Time Constant must be numeric', 'numeric', null, 'client');
$form->setDefaults($devInfo);
$form->addElement('submit', 'postSetup', 'Update');
if (isset($_REQUEST['postSetup']) && $form->validate()) {

    $pktData     = (is_array($_REQUEST['Types'])) ? $_REQUEST['Types'] : array();
    $pktData[-1] = (int) $_REQUEST['TimeConstant'];
    ksort($pktData);
    foreach ($pktData as $key => $val) {
        $pktData[$key] = (int) $val;
    }
    $return = $endpoint->setConfig($devInfo, 4, $pktData);

    if ($return) header("Location: ".$_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey']);

}
print $text.$form->toHTML();


?>