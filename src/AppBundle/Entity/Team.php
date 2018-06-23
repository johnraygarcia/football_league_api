<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Strip", fetch="EAGER")
     * @ORM\JoinColumn(name="strip_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\File(mimeTypes={ "image/jpeg"})
     **/
    private $strip;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\League", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $league;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getStrip(){
        return $this->strip;
    }

    /**
     * @param mixed $strip
     */
    public function setStrip($strip){
        $this->strip = $strip;
    }

    /**
     * @return mixed
     */
    public function getLeague(){
        return $this->league;
    }

    /**
     * @param mixed $league
     */
    public function setLeague($league){
        $this->league = $league;
    }
}
