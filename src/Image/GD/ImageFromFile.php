<?php

namespace ArmdPro\Image\GD;

use ArmdPro\Image\GD;

/**
 * Class ImageFromFile
 * @package ArmdPro\Image\GD
 */
class ImageFromFile extends GD
{
    /**
     * @var resource
     */
    private $_resource;

    /**
     * @var string
     */
    protected $_mime;

    /**
     * @var string
     */
    protected $_file;

    /**
     * @var string
     */
    protected $_ext;

    /**
     * @var int
     */
    protected $_type;

    public function __construct($file)
    {
        $this->_ext = self::formatExtension($file);
        $this->_resource = self::createFromExt($file);
        $this->_file = $file;

        if(isset(self::$_types[$this->_ext])) {
            $this->_mime = image_type_to_mime_type(self::$_types[$this->_ext]);
            $this->_type = self::$_types[$this->_ext];
        } else {
            $this->_mime = (new \fInfo(FILEINFO_MIME_TYPE))->file($this->_file);
        }

        parent::__construct();
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->_mime;
    }

    /**
     * @param $file
     * @return resource
     */
    public static function createFromExt($file)
    {
        $ext = self::formatExtension($file);
        return call_user_func("imagecreatefrom{$ext}", $file);
    }

    /**
     * @param $file
     * @return string
     */
    public static function formatExtension($file)
    {
        return preg_replace('/^jpg|jpe$/', 'jpeg', pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * @param null|string $filePath
     * @param null $quality
     * @return bool
     */
    public function save($filePath, $quality = null)
    {
        return parent::save($filePath ? : $this->_file, $quality);
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * @param resource $resource
     * @return $this
     */
    protected function _setResource($resource)
    {
        $this->_resource = $resource;
        return $this;
    }

    protected function _destroy()
    {
        $this->_resource = null;
    }
}