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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
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
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
        $text     = $pretext;
        $text    .= $this->_reading["points"][$point["id"]];
        $text    .= $posttext;
        $box      = imagettfbbox($fontsize, 0, $this->_fontFile, $text);
        if (!is_null($point["background"])
            && (strtolower($point["background"]) !== "none")
            && (strtolower($point["background"]) !== "transparent")
        ) {

            $bcolor = $this->gdAllocateColor($point["background"]);
            imagefilledrectangle(
                $this->img,
                $point["x"] + $box[6] - 3,
                $point["y"] + $box[7] - 3,
                $point["x"] + $box[2] + 3,
                $point["y"] + $box[3] + 3,
                $bcolor
            );
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

        $color = empty($color) ? "#000000" : trim(strtoupper($color));
        if (empty($this->colors[$color])) {
            $r = hexdec(substr($color, 1, 2));
            $g = hexdec(substr($color, 3, 2));
            $b = hexdec(substr($color, 5, 2));

            $this->colors[$color] = imagecolorallocate($this->img, $r, $g, $b);
            $this->RGB[$color]    = array("R" => $r, "G" => $g, "B" => $b);
        }
        return $this->colors[$color];
    }
}
?>
