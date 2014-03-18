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
namespace HUGnet\images\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

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
class SVG extends \HUGnet\images\Driver
{
    /** This is our parameters */
    protected $params = array(
        "mimetype" => "image/svg+xml",
    );
    /** @var This is the indent to use */
    private $_ind = "    ";
    /** @var This is the line end to use */
    private $_end = "\n";
    
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function encode()
    {
        $output .= $this->_xmlHeader().$this->_end;
        $output .= $this->_doctypeHeader().$this->_end;
        $output .= $this->_svgHeader().$this->_end;
        $output .= $this->_ind.$this->_description().$this->_end;
        $output .= $this->_ind.$this->_rect(
            0, 0, $this->image()->get("width"), $this->image()->get("height"), "#FFFFFF", "none"
        ).$this->_end;
        $output .= $this->_backgroundImage($this->_ind, $this->_end);
        for ($i = 0; $i < $this->image()->pointCount; $i++) {
            $output .= $this->_point(
                $this->image()->point($i), $this->_ind, $this->_end
            );
        }
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
    private function _doctypeHeader()
    {
        return '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" '
            .'"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
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
            .' width="'.$this->image()->get("width").'px"'
            .' height="'.$this->image()->get("height").'px">';
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    private function _description()
    {
        return '<desc>'.strip_tags((string)$this->image()->description).'</desc>';
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
        $ret  = '<image';
        $ret .= ' id=""';
        $ret .= ' height="'.$this->image()->get("height").'"';
        $ret .= ' width="'.$this->image()->get("width").'"';
        $ret .= ' xlink:href="data:'.$this->image()->get("imagetype").';base64,  ';
        $ret .= base64_encode($this->image()->get("image"));
        $ret .= '" />';
        return $ret;

    }

    /**
    * Returns the object as a string
    *
    * @param ImagePointContainer &$point The point to use
    * @param string              $indent The indent to use
    * @param string              $end    The line end to use
    *
    * @return string
    */
    private function _point(ImagePointContainer &$point, $indent = "", $end = "")
    {
        if (strlen($point["text"]) <= 0) {
            return;
        }

        $pointId = "point".$point["id"];

        $ret  = $indent.$this->_rect(
            $point["x"], $point["y"], 0, 0, "transparent", "none", $pointId."box"
        ).$end;
        $ret .= $indent.$this->_text(
            $point["text"], $point["x"], $point["y"], $point["color"], "none",
            $point["fontsize"], $pointId."text", $extra
        ).$end;
        if (strlen($point["link"]) > 0) {
            $ret = $this->_xlinkHref(
                $point["link"], $point["linkTitle"], $ret, "_top", "", "", $indent, $end
            )
        }
        $ret .= $indent.'<script>'.$end;
        $ret .= $indent.$indent.'var Text=document.getElementById("'
            .$pointId."text".'").getBBox();'.$end;
        $ret .= $indent.$indent.'var Box =document.getElementById("'
            .$pointId."box".'");'.$end;
        $ret .= $indent.$indent.'Box.setAttributeNS(null, "x", Text.x-3);'.$end;
        $ret .= $indent.$indent.'Box.setAttributeNS(null, "y", Text.y-3);'.$end;
        $ret .= $indent.$indent.'Box.setAttributeNS(null, "width", Text.width+6);'
            .$end;
        $ret .= $indent.$indent.'Box.setAttributeNS(null, "height", Text.height+6);'
            .$end;
        $ret .= $indent.$indent.'Box.setAttributeNS(null, "fill", "'
            .$point["background"].'");'.$end;
        $ret .= $indent.$indent.'//document.documentElement.appendChild(Box);'.$end;
        $ret .= $indent.'</script>'.$end;
        return $ret;
    }

}
?>
