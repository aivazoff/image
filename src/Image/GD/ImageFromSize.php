<?php
namespace ArmdPro\Image\GD;

use ArmdPro\Image\GD\Helper as GDHelper;
use ArmdPro\Image\GD AS ImageGD;

/**
 * Class Size
 * @package Image\From
 */
class ImageFromSize extends ImageFromResource
{
    public function __construct($width, $height, array $color = null)
    {
        parent::__construct(GDHelper::createCanvas($width, $height, $color ?: $this->_fillColor));
    }
}