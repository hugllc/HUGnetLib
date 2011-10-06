<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/OutputContainer.php";
require_once dirname(__FILE__)."/ConfigContainer.php";
require_once dirname(__FILE__).'/../contrib/Color.php';

/**
 * This is a generic, extensible container class
 *
 * Classes can be added in so that their methods and properties can be used
 * by this class and the reverse of that.  There can be a whole linked list
 * of containers that extend eachother.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ImagePointContainer extends HUGnetContainer
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "text" => "",
        "color" => "#000000",
        "fill"  => "transparent",
        "x" => 0,
        "y" => 0,
        "fontsize" => "9pt",
        "colorValueMax" => 100,
        "colorValueMin" => 0,
        "colorMax" => "FF0001",
        "colorMin" => "0000FF",
        "outline" => null,
        "link" => "",
        "linkTitle" => "",
        "id" => 0,
    );

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        // Setup our configuration
        $this->myConfig = &ConfigContainer::singleton();
        parent::__construct($data);
    }
    /**
     * Method to display the view
     *
     * @param mixed $value    The value to use
     * @param mixed $valueMin The max value to use
     * @param mixed $valueMax The min value to use
     *
     * @return string
     */
    private function _autocolor($value, $valueMin = null, $valueMax = null)
    {
        if (!is_null($valueMin)) {
            $this->colorValueMin = $valueMin;
        }
        if (!is_null($valueMax)) {
            $this->colorValueMax = $valueMax;
        }
        if ($value < $this->colorValueMin) {
            return str_replace("#", "", $this->colorMin);
        }
        if ($value > $this->colorValueMax) {
            return str_replace("#", "", $this->colorMax);
        }
        $diff = ($value - $this->colorValueMin);
        $denom = ($this->colorValueMax - $this->colorValueMin);
        if ($denom <= 0) {
            return str_replace("#", "", $this->colorMax);
        }
        $diff = $diff/$denom;
        $min = $this->_color2HSV($this->colorMin);
        $max = $this->_color2HSV($this->colorMax);
        $hue = $min['h'] + (($max['h'] - $min['h']) * $diff);
        $sat = $min['s'] + (($max['s'] - $min['s']) * $diff);
        $val = $min['v'] + (($max['v'] - $min['v']) * $diff);
        return $this->_hsv2Color($hue, $sat, $val);
    }
    /**
     * Method to display the view
     *
     * @param mixed $value    The value to use
     * @param mixed $valueMin The max value to use
     * @param mixed $valueMax The min value to use
     *
     * @return string
     */
    public function autoFill($value, $valueMin = null, $valueMax = null)
    {
        $this->fill = "#".$this->_autoColor($value, $valueMin, $valueMax);
        // Now check the foreground color
        $bgndF = (bool)($this->_colorBrightness($this->fill) > 80);
        $bgndC = (bool)($this->_colorBrightness($this->color) > 80);
        // if $bF and $bC are the same then invert the color
        if ($bgndF === $bgndC) {
            $this->color = "#".$this->_colorInvert($this->color);
        }
    }
    /**
     * Method to display the view
     *
     * @param mixed $value    The value to use
     * @param mixed $valueMin The max value to use
     * @param mixed $valueMax The min value to use
     *
     * @return string
     */
    public function autoColor($value, $valueMin = null, $valueMax = null)
    {
        $this->color = "#".$this->_autoColor($value, $valueMin, $valueMax);

        // Now check the background color
        $bgndF = (bool)($this->_colorBrightness($this->fill) > 90);
        $bgndC = (bool)($this->_colorBrightness($this->color) > 90);
        if ($bgndF === $bgndC) {
            $this->fill = "#".$this->_colorInvert($this->fill);
        }
    }
    /**
     * Method to display the view
     *
     * @param string $color The color value to use.  Should be RRGGBB
     *
     * @return string
     */
    private function _color2RGB($color)
    {
        $color = str_replace("#", "", $color);
        $ret['r'] = hexdec(substr($color, 0, 2));
        $ret['g'] = hexdec(substr($color, 2, 2));
        $ret['b'] = hexdec(substr($color, 4, 2));
        return $ret;
    }

    /**
     * Method to display the view
     *
     * @param string $color The color value to use.  Should be RRGGBB
     *
     * @return string
     */
    private function _color2HSV($color)
    {
        $color = str_replace("#", "", $color);
        return Color::hex2hsv($color);
    }
    /**
    * Method to display the view
    *
    * @param int $hue The Hue
    * @param int $sat The Saturation
    * @param int $val The Value
    *
    * @return string
    */
    private function _hsv2Color($hue, $sat, $val)
    {
        return Color::hsv2hex($hue, $sat, $val);
    }


    /**
    * Get the brightness of a color
    *
    * The algorithm was found at:
    * http://particletree.com/notebook/calculating-color-contrast-for-legible-text/
    *
    * @param string $color The color value to use.  Should be RRGGBB
    *
    * @return int Range: 0 - 255
    */
    private function _colorBrightness($color)
    {
        $col = $this->_color2RGB($color);
        $bright = (($col['r'] * 299) + ($col['g'] * 587) + ($col['b'] * 114)) / 1000;
        return (int)$bright;
    }

    /**
    * Get the brightness of a color
    *
    * @param string $color The color value to use.  Should be RRGGBB
    *
    * @return int Range: 0 - 255
    */
    private function _colorInvert($color)
    {
        $col = $this->_color2RGB($color);
        $red = 255 - $col['r'];
        $green = 255 - $col['g'];
        $blue = 255 - $col['b'];
        return sprintf("%02X%02X%02X", $red, $green, $blue);
    }
}
?>
