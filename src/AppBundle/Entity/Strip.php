<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Strip
 *
 * @ORM\Table(name="strip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StripRepository")
 */
class Strip
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="strip", type="string", length=255)
     */
    private $strip;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStrip(){
        return $this->strip;
    }

    /**
     * @param string $strip
     */
    public function setStrip($strip){
        $this->strip = $strip;
    }
}
