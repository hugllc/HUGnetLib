<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\images;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/LoadableDriver.php";
/** This is our interface */
require_once dirname(__FILE__)."/drivers/DriverInterface.php";
/** This is our base class */
require_once dirname(__FILE__)."/../contrib/Color.php";
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class Driver
{
    /** @var This is the image object */
    private $_image = null;
    /** @var This is the image output file descriptor  */
    protected $img = null;
    /** This is where we store the GD color references */
    protected $colors = array();
    /** This is where we store the RGB values for the colors */
    protected $RGB = array();
    /** This is the defaults */
    protected $default = array(
        "mimetype" => "application/unknown",
    );
    /** This is our parameters */
    protected $params = array();
    /** This is our reading */
    protected $reading = array();
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$image the Image record we are attached to
    *
    * @return null
    */
    protected function __construct(&$image)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($image)
        );
        $this->_image = &$image;
        $this->_setFont();
    }
    /**
    * This function sets the font.
    *
    * @param string $font The font file to use
    *
    * @return null
    */
    private function _setFont($font = null)
    {
        $base = realpath(dirname(__FILE__)."/../contrib/fonts");;
        if (!is_null($font) && (file_exists($base."/".$font))) {
            $this->_fontFile = real_path($base."/".$font);
        } else {
            $this->_fontFile = realpath($base."/bitstream-vera/Vera.ttf");
        }
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_image);
        if (!is_null($this->img)) {
            imagedestroy($this->img);
        }
    }
    /**
    * Checks to see if a piece of data exists
    *
    * @param string $name The name of the property to check
    *
    * @return true if the property exists, false otherwise
    */
    public function present($name)
    {
        return !is_null($this->get($name));
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = null;
        if (isset($this->params[$name])) {
            $ret = $this->params[$name];
        } else if (isset($this->default[$name])) {
            $ret = $this->default[$name];
        }
        return $ret;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    */
    public function toArray()
    {
        $return = array();
        $keys = array_merge(array_keys($this->default), array_keys($this->params));
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$device The device record we are attached to
    *
    * @return null
    */
    public static function &factory($driver, &$device)
    {
        $class = \HUGnet\Util::findClass(
            $driver, "images/drivers", true, "\\HUGnet\\images\\drivers"
        );
        $interface = "\\HUGnet\\images\\drivers\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($device);
        }
        include_once dirname(__FILE__)."/drivers/PNG.php";
        return new \HUGnet\images\drivers\PNG($device);
    }
    /**
    * This returns the image class
    *
    * @return null
    */
    protected function &image()
    {
        return $this->_image;
    }
    /**
    * This returns the image class
    *
    * @return null
    */
    public function &reading($reading = null)
    {
        if (is_array($reading)) {
            $this->_reading = $reading;
        }
        return $this->_reading;
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    protected function gdBuildImage()
    {
        $this->gdStartImage();
        $points = json_decode($this->image()->get("points"), true);
        foreach ($points as $point) {
            $this->gdImagePoint($point);
        }
    }

    /**
     * Gets a remote image and sets $this->_image to the resource
     *
     * @return none
     */
    protected function gdStartImage()
    {
        $this->img  = imagecreatefromstring(
            base64_decode($this->image()->get("image"))
        );
        if ($this->img === false) {
            $this->img  = imagecreatetruecolor(
                $this->image()->get("width"), $this->image()->get("height")
            );
            $color     = $this->gdAllocateColor("#FFFFFF");
            imagefill($this->img, 0, 0, $color);
        }
    }

    /**
    * Gets a remote image and sets $this->_image to the resource
    *
    * @param array $point The point to add
    *
    * @return none
    */
    protected function gdImagePoint($point)
    {
        $color    = $this->gdAllocateColor($point["color"]);
        $pretext  = html_entity_decode((string)$point["pretext"]);
        $posttext = html_entity_decode((string)$point["posttext"]);
        $fontsize = ((int)$point["fontsize"] > 0) ? (int)$point["fontsize"] : 12;
        $value    = $this->_reading["points"][$point["id"]];
        $text     = $pretext.$value.$posttext;
        $box      = imagettfbbox($fontsize, 0, $this->_fontFile, $text);

        if (!is_null($point["background"])
            && (strtolower($point["background"]) !== "none")
            && (strtolower($point["background"]) !== "transparent")
        ) {

            $background = $this->autobackground($value, $point);
            $bcolor = $this->gdAllocateColor($background);
            imagefilledrectangle(
                $this->img,
                $point["x"] + $box[6] - 3,
                $point["y"] + $box[7] - 3,
                $point["x"] + $box[2] + 3,
                $point["y"] + $box[3] + 3,
                $bcolor
            );
            $color = $this->gdAllocateColor($this->autocolor($background, $point));
        }
        $ret = imagettftext(
            $this->img,
            $fontsize,
            0,
            (int)$point["x"],
            (int)$point["y"],
            (int)$color,
            $this->_fontFile,
            $text
        );

    }

    /**
     * Converts an HTML color into RGB
     *
     * @param string $color The color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    protected function gdAllocateColor($color)
    {

        if (empty($this->colors[$color])) {
            $c = $this->hexToRGB($color);
            $this->colors[$color] = imagecolorallocate(
                $this->img, $c["R"], $c["G"], $c["B"]
            );
        }
        return $this->colors[$color];
    }
    /**
     * Converts an HTML color into RGB
     *
     * @param string $color The color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    protected function hexToRGB($color)
    {
        $color = empty($color) ? "#000000" : trim(strtoupper($color));
        if (empty($this->RGB[$color])) {
            $color = str_replace("#", "", $color);
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));

            $this->RGB[$color]    = array("R" => $r, "G" => $g, "B" => $b);
        }
        return $this->RGB[$color];
    }
    /**
     * Returns the brightness of the color
     *
     * @param string $color The color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    protected function brightness($color)
    {
        $c = $this->hexToRGB($color);
        $brightness  =  sqrt(
            (0.241 * pow($c["R"], 2)) 
            + (0.691 * pow($c["G"], 2)) 
            + (0.068 * pow($c["B"], 2))
        );
        return $brightness;
    }
    /**
     * Returns whether a color combination will be readable
     *
     * @param string $color1 The first color in HTML format (#RRGGBB)
     * @param string $color2 The second color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    protected function readable($color1, $color2)
    {
        $brightness = abs($this->brightness($color1) - $this->brightness($color2));
        return $brightness > 130;
    }
    /**
     * Converts an HTML color into RGB
     *
     * @param string $color  The color in HTML format (#RRGGBB)
     * @param array  $colors Array of colors in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    protected function mostReadable($color, $colors)
    {
        $max    = null;
        $maxkey = null;
        foreach ((array)$colors as $key => $c) {
            if (is_string($c) && (strlen($c) == 7)) {
                $brightness = abs($this->brightness($color) - $this->brightness($c));
                if (is_null($max) || ($brightness > $max)) {
                    $max    = $brightness;
                    $maxkey = $key;
                }
            }
        }
        if (is_null($maxkey)) {
            return null;
        }
        return $colors[$maxkey];
    }
    /**
     * Method to automatically pick a color
     *
     * @param string $background The background color to use
     * @param array  $point      The point to use
     *
     * @return string
     */
    protected function autocolor($background, $point)
    {
        if ($background != $point["background"]) {
            $color = $this->mostReadable(
                $background,
                array($point["color"], $point["color1"], "#000000", "#FFFFFF")
            );
        } else {
            $color = $point["color"];
        }
        if (empty($color)) {
            // Black if nothing else is specified
            $color = "#000000";
        }
        return $color;
    }
    /**
     * Method to automatically pick a color
     *
     * @param mixed $value The value to use
     * @param array $point The point to use
     *
     * @return string
     */
    protected function autobackground($value, $point)
    {
        $value = (float)$value;
        if (empty($point['backgroundmax']) 
            || ($point["valmin"] >= $point["valmax"]) 
            || ($value <= $point["valmin"])
        ) {
            return $point["background"];
        }
        $denom = ($point["valmax"] - $point["valmin"]);
        if (($value >= $point["valmax"]) || ($denom <= 0)) {
            return $point["backgroundmax"];
        }
        $diff = (float)($value - (float)$point["valmin"]);
        $diff = $diff / $denom;
        $min = $this->_color2HSV($point["background"]);
        $max = $this->_color2HSV($point["backgroundmax"]);

        $hue = $min['h'] + (($max['h'] - $min['h']) * $diff);
        $sat = $min['s'] + (($max['s'] - $min['s']) * $diff);
        $val = $min['v'] + (($max['v'] - $min['v']) * $diff);
        return $this->_hsv2Color($hue, $sat, $val);
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
        return \HUGnet\contrib\Color::hex2hsv($color);
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
        return "#".\HUGnet\contrib\Color::hsv2hex($hue, $sat, $val);
    }
}
?>
