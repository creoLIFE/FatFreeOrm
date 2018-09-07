<?php

namespace FatFree\Entity\DoctrineOrm;

use Doctrine\ORM\Mapping as ORM;

trait AccessTrait
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    protected $_enabled = 1;

    /**
     * @return 	use AccessTrait;bool
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
