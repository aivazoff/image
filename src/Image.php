<?php

namespace ArmdPro;

abstract class Image
{
    const RESIZE_MODE_NORMALIZE = 1;
    const RESIZE_MODE_CROOP     = 2;
    const RESIZE_MODE_FILL      = 3;

    const SIZE_ICON     = 'icon';
    const SIZE_SMALL    = 'small';
    const SIZE_THUMB    = 'thumb';
    const SIZE_MEDIUM   = 'medium';
    const SIZE_NORMAL   = 'normal';
    const SIZE_LARG     = 'large';
    const SIZE_POSTER   = 'poster';
    const SIZE_GALLERY  = 'gallery';

    protected $_fillColor = [255, 255, 255, 127];

    protected static $_types = [
        'jpeg' => IMAGETYPE_JPEG,
        'gif'  => IMAGETYPE_GIF,
        'png'  => IMAGETYPE_PNG,
    ];
}
