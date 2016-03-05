<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Integrante
 *
 * @ORM\Table(name="sms_integrante")
 * @ORM\Entity
 */
class Integrante
{
  
    const LISTINTEGRANTES = 'list-integrantes';
    
    /**
     * @var integer
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
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="googleaccount", type="string", length=255, nullable=true)
     */
    private $googleaccount;

    /**
     * @var string
     *
     * @ORM\Column(name="twitteraccount", type="string", length=255, nullable=true)
     */
    private $twitteraccount;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookaccount", type="string", length=255, nullable=true)
     */
    private $facebookaccount;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="posicionVisual", type="integer")
     */
    private $posicionVisual = 0;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Integrante
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Integrante
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Integrante
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set googleaccount
     *
     * @param string $googleaccount
     * @return Integrante
     */
    public function setGoogleaccount($googleaccount)
    {
        $this->googleaccount = $googleaccount;

        return $this;
    }

    /**
     * Get googleaccount
     *
     * @return string 
     */
    public function getGoogleaccount()
    {
        return $this->googleaccount;
    }

    /**
     * Set twitteraccount
     *
     * @param string $twitteraccount
     * @return Integrante
     */
    public function setTwitteraccount($twitteraccount)
    {
        $this->twitteraccount = $twitteraccount;

        return $this;
    }

    /**
     * Get twitteraccount
     *
     * @return string 
     */
    public function getTwitteraccount()
    {
        return $this->twitteraccount;
    }

    /**
     * Set facebookaccount
     *
     * @param string $facebookaccount
     * @return Integrante
     */
    public function setFacebookaccount($facebookaccount)
    {
        $this->facebookaccount = $facebookaccount;

        return $this;
    }

    /**
     * Get facebookaccount
     *
     * @return string 
     */
    public function getFacebookaccount()
    {
        return $this->facebookaccount;
    }

    /**
     * Set posicionVisual
     *
     * @param integer $posicionVisual
     * @return Integrante
     */
    public function setPosicionVisual($posicionVisual)
    {
        $this->posicionVisual = $posicionVisual;

        return $this;
    }

    /**
     * Get posicionVisual
     *
     * @return integer 
     */
    public function getPosicionVisual()
    {
        return $this->posicionVisual;
    }
    
    public function getFullClassName()
    {
      return get_class($this);
    }
    
    public function retrieveAlbums()
    {
      return array('imagen');
    }
}
