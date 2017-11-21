<?php

namespace ArmdPro\Image;

use ArmdPro\Image\Exception as ImageException;
use ArmdPro\Image\GD\ImageFromSize;
use ArmdPro\Image\GD\ImageFromFile;
use ArmdPro\Image\GD as Image;

/**
 * Class Image
 *
 * @property int $width
 * @property int $height
 * @property array $size
 *
 * @method Image clone()
 */
abstract class GD extends \ArmdPro\Image
{
    private $_width;
    private $_height;

    /**
     * @return string
     */
    abstract public function getMimeType();

    /**
     * @return int
     */
    abstract public function getType();

    /**
     * @return resource
     */
    abstract public function getResource();

    /**
     * @param resource $resource
     * @return $this
     */
    abstract protected function _setResource($resource);

    /**
     * @return void
     */
    abstract protected function _destroy();

    public function __construct()
    {
        $src = $this->getResource();
        $this->_width  = imagesx($src);
        $this->_height = imagesy($src);
        imagesavealpha($src, true);
    }

    function __get($name)
    {
        switch($name)
        {
            case('height'): return $this->_height;
            case('width'):  return $this->_width;
        }
    }

    function __set($name, $value)
    {
        switch($name)
        {
            case('width'):
                $this->resizeByWidth($value);
                break;

            case('height'):
                $this->resizeByHeight($value);
                break;
        }

        return $this;
    }

    public function __toString()
    {
        header("Content-Type: " . $this->getMimeType());
        return $this->render();
    }

    public function __call($name, $args)
    {
        if($name == 'clone') {
            return clone $this;
        }

        $msg = sprintf("Method not exists: %s::%s()", get_class($this), $name);
        throw new ImageException($msg);
    }

    public function __clone() {
        $clone = new ImageFromSize($this->_width, $this->_height);
        $this->replace($clone);
    }

    /**
     * @param array $fillColor
     */
    public function setFillColor(array $fillColor)
    {
        $this->_fillColor = $fillColor;
    }

    /**
     * @param Image $image
     * @return $this
     */
    public function replace(Image $image)
    {
        return $this->_setResource($image->getResource());
    }

    /**
     * @param int $width
     * @param bool $normalize
     * @return $this
     */
    public function resizeByWidth($width, $normalize = true)
    {
        $height = $width / $this->_width * $this->_height;
        return $this->_resize($width, $height, $normalize);
    }

    /**
     * @param int $height
     * @param bool $normalize
     * @return $this
     */
    public function resizeByHeight($height, $normalize = true)
    {
        $width = $height / $this->_height * $this->_width;
        return $this->_resize($width, $height, $normalize);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int|bool $mode
     * @return $this
     */
    public function resize($width, $height, $mode = false)
    {
        if(!$width && !$width) {
            return $this;
        }

        if(!$mode) {
            return $this->_resize($width, $height, false);
        }

        if(!$width) {
            $width = $height / $this->_height * $this->_width;
        } else if(!$height) {
            $height = $width / $this->_width * $this->_height;
        }

        $newWidth = $height / $this->_height * $this->_width;
        $newHeight2 = $width / $this->_width * $this->_height;
        $newWidth2 = $newHeight2 / $this->_height * $this->_width;

        if(($mode != self::RESIZE_MODE_FILL && ($newWidth > $width || $newWidth2 > $width))
            OR ($mode == self::RESIZE_MODE_FILL && ($newWidth < $width || $newWidth2 < $width))
        ){
            $this->resizeByWidth($width, $mode);
        } else {
            $this->resizeByHeight($height, $mode);
        }

        if($mode == self::RESIZE_MODE_NORMALIZE) {
            return $this;
        }

        $canvas = new ImageFromSize($width, $height, $this->_fillColor);
        $canvas->copy($this, ($width - $this->_width) / 2, ($height - $this->_height) / 2);
        return $this->replace($canvas);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int|null $x
     * @param int|null $y
     * @return $this
     */
    public function croop($width, $height, $x = null, $y = null)
    {
        $newWidth = $height / $this->_height * $this->_width;
        $newHeight = $width / $this->_width * $this->_height;

        if($newWidth > $newHeight){
            $this->resizeByHeight($height);
        } else {
            $this->resizeByWidth($width);
        }

        $canvas = new ImageFromSize($width, $height, $this->_fillColor);

        $x = $x ?: ($width - $this->_width) / 2;
        $y = $y ?: ($height - $this->_height) / 2;

        return $this->replace($canvas->copy($this, $x, $y));
    }

    /**
     * @param bool $gzCompress
     * @return string
     */
    public function render($gzCompress = false)
    {
        ob_start($gzCompress ? 'ob_gzhandler' : null);
        imageinterlace($this->getResource(), true);
        imagepng($this->getResource());
        return ob_get_clean();
    }

    /**
     * @param Image $image
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function copy(Image $image, $x = 0, $y = 0)
    {
        imagealphablending($this->getResource(), true);
        imagecopy($this->getResource(), $image->getResource(), $x, $y, 0, 0, $image->_width, $image->_height);
        imagealphablending($this->getResource(), false);

        return $this;
    }

    /**
     * @param string|null $filePath
     * @param int|null $quality
     * @return bool
     */
    public function save($filePath, $quality = null)
    {
        $ext = ImageFromFile::formatExtension($filePath);
        return call_user_func_array("image{$ext}", func_get_args());
    }

    public function output()
    {
        header("Content-Type: {$this->getMimeType()}");
        $ext = image_type_to_extension($this->getType());
        call_user_func("image{$ext}", $this->getResource());
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool $normalize
     * @return $this
     */
    protected function _resize($width, $height, $normalize = false)
    {
        if($normalize && ($width >= $this->width || $height >= $this->height)) {
            return $this;
        }

        $canvas = new ImageFromSize($width, $height, [255,255,255,0]);
        $dst = $canvas->getResource();
        $src = $this->getResource();

        // imagealphablending($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        // imagealphablending($dst, false);
        $this->_setResource($dst);

        $this->_width  = $width;
        $this->_height = $height;

        return $this;
    }

    /**
     * @return void
     */
    function __destruct()
    {
        /*imagedestroy($this->_getResource());
        $this->_destroy();*/
    }
}