<?php

namespace ArmdPro\Image\GD;

use ArmdPro\Image\GD AS ImageGD;

/**
 * Class Resource
 * @package ArmdPro\Image\GD
 */
class ImageFromResource extends ImageGD
{
    /**
     * @var resource
     */
    private $_resource;
    private $_mimeType = 'iamge/png';
    private $_type = IMAGETYPE_PNG;

    public function __construct($resource)
    {
        $this->_resource = $resource;
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->_mimeType;
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