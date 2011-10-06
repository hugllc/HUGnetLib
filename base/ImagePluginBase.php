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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/ImagePluginInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class ImagePluginBase extends HUGnetClass implements ImagePluginInterface
{
    /** @var This is the image class */
    protected $image = null;
    /** @var This is the image output file descriptor  */
    protected $img = null;

    /**
    * Disconnects from the database
    *
    * @param ImageContainer &$container The image cointainer to output as an image
    * @param array          $data       The data to use
    */
    public function __construct(ImageContainer &$container, $data = array())
    {
        $this->image =& $container;
        if (!is_null($data["fontFile"])) {
            $this->_fontFile = $data["fontFile"];
        } else {
            $this->_fontFile = realpath(
                dirname(__FILE__)."/../contrib/fonts/bitstream-vera/Vera.ttf"
            );
        }
    }
    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        if (!is_null($this->img)) {
            imagedestroy($this->img);
        }
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function output()
    {
        return "Please replace this function";
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    protected function gdBuildImage()
    {
        $this->gdStartImage();
        for ($i = 0; $i < $this->image->pointCount; $i++) {
            $this->gdImagePoint($this->image->point($i));
        }
    }

    /**
     * Gets a remote image and sets $this->_image to the resource
     *
     * @return none
     */
    protected function gdStartImage()
    {

        $this->img  = imagecreatetruecolor(
            $this->image->width, $this->image->height
        );
        $color     = $this->gdAllocateColor("#FFFFFF");
        imagefill($this->img, 0, 0, $color);
        if ($this->fileExists($this->image->imageLoc)) {
            list($imageWidth, $imageHeight) = getimagesize($this->image->imageLoc);
            $contents  = file_get_contents($this->image->imageLoc);
            $img = imagecreatefromstring($contents);
            imagecopyresized(
                $this->img, $img, 0, 0, 0, 0,
                $this->image->width, $this->image->height,
                $imageWidth, $imageHeight
            );
            imagedestroy($img);
        }

    }

    /**
    * Gets a remote image and sets $this->_image to the resource
    *
    * @param ImagePointContainer &$point The point to add
    *
    * @return none
    */
    protected function gdImagePoint(ImagePointContainer &$point)
    {

        $color = $this->gdAllocateColor($point->color);
        $text  = html_entity_decode($point->text);
        $box = imagettfbbox($point->fontsize, 0, $this->_fontFile, $text);
        if (!is_null($point->outline)) {
            $ocolor = $this->gdAllocateColor($point->outline);
            imagefilledrectangle(
                $this->img,
                $point->x + $box[6] - 6,
                $point->y + $box[7] - 6,
                $point->x + $box[2] + 6,
                $point->y + $box[3] + 6,
                $ocolor
            );
        }

        if (!is_null($point->fill)
            && (strtolower($point->fill) !== "none")
            && (strtolower($point->fill) !== "transparent")
        ) {
            $bcolor = $this->gdAllocateColor($point->fill);
            imagefilledrectangle(
                $this->img,
                $point->x + $box[6] - 3,
                $point->y + $box[7] - 3,
                $point->x + $box[2] + 3,
                $point->y + $box[3] + 3,
                $bcolor
            );
        }
        imagettftext(
            $this->img,
            $point->fontsize,
            0,
            $point->x,
            $point->y,
            $color,
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

        $color = trim(strtoupper($color));
        if (empty($this->colors[$color])) {
            $red = hexdec(substr($color, 1, 2));
            $green = hexdec(substr($color, 3, 2));
            $blue = hexdec(substr($color, 5, 2));

            $this->colors[$color] = imagecolorallocate(
                $this->img, $red, $green, $blue
            );
            $this->RGB[$color] = array("R" => $red, "G" => $green, "B" => $blue);
        }
        return $this->colors[$color];
    }
    /**
    * Checks to see if a file exists and can be opened.
    *
    * @param string $filename The file to check
    *
    * @return string
    */
    protected function fileExists($filename)
    {
        $file = @fopen($filename, 'r');
        if ($file) {
            fclose($file);
            return true;
        }
        return false;
    }
}
?>
