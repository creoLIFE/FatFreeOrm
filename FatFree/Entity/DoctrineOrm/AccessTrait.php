<?php

namespace FatFree\Entity\DoctrineOrm;

use Doctrine\ORM\Mapping as ORM;

trait AccessTrait
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $_enabled = 1;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
    }

}
