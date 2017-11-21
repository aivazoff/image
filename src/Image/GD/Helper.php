<?php

namespace ArmdPro\Image\GD;

/**
 * Class Helper
 * @package Image
 */
abstract class Helper extends \ArmdPro\Image\Helper
{
    /**
     * @param int $width
     * @param int $height
     * @param array|null $color
     * @return resource
     */
    public static function createCanvas($width, $height, array $color = null)
    {
        $color  = $color ? array_pad($color, 4, 0) : [255,255,255,0];
        $source = imagecreatetruecolor($width, $height);

        imagealphablending($source, false);
        imagesavealpha($source, true);
        $color = imagecolorexactalpha($source, $color[0], $color[1], $color[2], $color[3]);
        imagecolortransparent($source, $color);
        imagefill($source, 0, 0, $color);

        return $source;
    }
}