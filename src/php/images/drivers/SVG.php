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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\images\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our interface */
require_once dirname(__FILE__)."/DriverInterface.php";

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
 * @version    Release: 0.14.8
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SVG extends \HUGnet\images\Driver 
    implements \HUGnet\images\drivers\DriverInterface
{
    /** This is our parameters */
    protected $params = array(
        "mimetype" => "image/svg+xml",
    );
    /** @var This is the indent to use */
    private $_ind = "    ";
    /** @var This is the line end to use */
    private $_end = "\n";
    /** @var This image */
    private $_image = "";
    /** @var This definitions */
    private $_defs = "";
    /** @var This definitions indent */
    private $_defindent = "        ";
    
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function encode()
    {
        $this->_image = "";
        $this->_defs  = "";
        $output .= $this->_xmlHeader().$this->_end;
        $output .= $this->_svgHeader().$this->_end;
        $output .= $this->_ind.$this->_description().$this->_end;
        $output .= $this->_ind.$this->_rect(
            0, 
            0, 
            $this->image()->get("width"), 
            $this->image()->get("height"), 
            "#FFFFFF", 
            "none"
        ).$this->_end;
        $this->_backgroundImage($this->_ind, $this->_end);
        $points = json_decode($this->image()->get("points"), true);
        foreach ($points as $point) {
            $this->_point(
                $point, $this->_ind, $this->_end
            );
        }
        $output .= "    <defs>".$this->_end.$this->_defs."    </defs>".$this->_end;
        $output .= $this->_image.$this->_end;
        $output .= $this->_svgFooter().$this->_end;
        return $output;
    }

    /**
    * Returns the object as a string
    *
    * @return string
    */
    private function _xmlHeader()
    {
        return '<?xml version="1.0" standalone="no"?>';
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    private function _svgHeader()
    {
        return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg"'
            .' xmlns:xlink="http://www.w3.org/1999/xlink"'
            .' width="'.$this->image()->get("width").'"'
            .' height="'.$this->image()->get("height").'">';
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    private function _description()
    {
        return '<desc>'.strip_tags((string)$this->image()->get("desc")).'</desc>';
    }
    /**
    * Returns a rectangle object
    * 
    * @param int    $x      The x-coordinate of the upper left corner
    * @param int    $y      The y-coordinate of the upper left corner
    * @param int    $width  The width
    * @param int    $height The height
    * @param string $fill   The fill color (be sure to add "#")
    * @param string $stroke The line color (be sure to add "#")
    * @param string $id     The id of the shape
    * @param string $extra  Extra stuff to add to the tag
    *
    * @return string
    */
    private function _rect(
        $x, $y, $width, $height, $fill, $stroke, $id = "", $extra = ""
    ) {
        return '<rect id="'.$id.'" x="'.(int)$x.'" y="'.(int)$y.'"'
            .' width="'.(int)$width.'px" '.'height="'.(int)$height.'px"'
            .' fill="'.$fill.'" stroke="'.$stroke.'" '.$extra.'/>';
    }
    /**
    * Returns a rectangle object
    *
    * @param string $text     The text to print
    * @param int    $x        The x-coordinate of the upper left corner
    * @param int    $y        The y-coordinate of the upper left corner
    * @param string $fill     The fill color (be sure to add "#")
    * @param string $stroke   The line color (be sure to add "#")
    * @param string $fontsize The size of font to use
    * @param string $id       The id of the shape
    * @param string $extra    Extra stuff to add to the tag
    *
    * @return string
    */
    private function _text(
        $text, $x, $y, $fill, $stroke, $fontsize = "9pt", $id = "",
        $extra = ""
    ) {
        return '<text id="'.$id.'" x="'.(int)$x.'" y="'.(int)$y.'"'
            .'  font-size="'.$fontsize.'" fill="'.$fill.'" stroke="'.$stroke.'"'
            .' '.$extra.'>'.strip_tags($text).'</text>';
    }
    /**
    * Returns a rectangle object
    *
    * @param string $link   The link to use
    * @param string $title  The title for the link
    * @param string $text   The text to link
    * @param string $target Target for the link
    * @param string $id     The id of the shape
    * @param string $extra  Extra stuff to add to the tag
    * @param string $indent The indent to use
    * @param string $end    The line end to use
    *
    * @return string
    */
    private function _xlinkHref(
        $link, $title, $text, $target = "_top", $id = "", $extra = "",
        $indent = "", $end = ""
    ) {
        $text = str_replace($end.$indent, $end.$indent.$indent, $text);
        return $indent.'<a xlink:href="'.$link.'" target="'.$target.'"'
        .' xlink:title="'.$title.'" >'.$end.$indent.$text.$end.$indent.'</a>'.$end;
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    private function _svgFooter()
    {
        return '</svg>'.$this->_end;
    }
    /**
    * This calculates the size of the background image if its size does not match
    * the size of the image we are building
    *
    * @param string $indent The indent to use
    * @param string $end    The end to use
    * 
    * @return string
    */
    private function _backgroundImage($indent = "", $end = "")
    {
        $this->_image  = $indent.'<image';
        $this->_image .= ' id=""';
        $this->_image .= ' height="'.$this->image()->get("height").'"';
        $this->_image .= ' width="'.$this->image()->get("width").'"';
        $this->_image .= ' xlink:href="data:'.$this->image()->get("imagetype");
        $this->_image .= ';base64,   ';
        $this->_image .= $this->image()->get("image");
        $this->_image .= '" />'.$this->_end;
    }

    /**
    * Returns the object as a string
    *
    * @param array  $point  The point to use
    * @param string $indent The indent to use for the image
    *
    * @return string
    */
    private function _point($point, $indent = "    ")
    {
        $pretext    = html_entity_decode((string)$point["pretext"]);
        $posttext   = html_entity_decode((string)$point["posttext"]);
        $fontsize   = ((int)$point["fontsize"] > 0) ? (int)$point["fontsize"] : 12;
        $value      = $this->_reading["points"][$point["id"]];
        $text       = $pretext.$value.$posttext;
        $background = $this->autobackground($value, $point);
        $color      = $this->autocolor($background, $point);
        if (strlen($text) <= 0) {
            return;
        }
        $index = $point["id"];
        $this->_defs .= $this->_defindent.'<filter x="0" y="0" width="1" height="1"' 
                       .' id="background'.$index.'">'
                       .'<feFlood flood-color="'.$background.'"/>'
                       .'</filter>'.$this->_end;
        $this->_defs .= $this->_defindent.'<text style="fill:'.$color.';'
                       .' font-size:'.$fontsize.'pt;" x="0" y="0"'
                       .' transform="translate('.$point["x"].', '.$point["y"].')"'
                       .' id="point'.$index.'">'.$text.'</text>'.$this->_end;
        $this->_image .= $indent.'<use xlink:href="#point'.$index.'" '
                        .'filter="url(#background'.$index.')"/>'.$this->_end;
        $this->_image .= $indent.'<use xlink:href="#point'.$index.'" />'.$this->_end;
    }

}
?>
