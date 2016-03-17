<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table(name="sns_article")
 * @ORM\Entity
 */
class Article
{
  
    const RECENTARTICLES = 'recent_articles';
    const PASTYEARARTICLESDATA = 'last_year_articles';
  
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="excerpt", type="string", length=255)
     */
    private $excerpt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetimetz")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update") 
     * @ORM\Column(name="updatedAt", type="datetimetz")
     */
    private $updatedAt;


    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255)
     */    
    private $slug;
    
    /**
     * @ORM\ManyToMany(targetEntity="ArticleTag", inversedBy="articles")
     * @ORM\JoinTable(name="sms_article_tag_relation",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="article_tag_id", referencedColumnName="id")}
     *      )
     **/    
    private $tags;
    
    /**
     * @ORM\ManyToMany(targetEntity="ArticleCategory", inversedBy="articles")
     * @ORM\JoinTable(name="sms_article_category_relation",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="article_category_id", referencedColumnName="id")}
     *      )
     **/    
    private $categories;
    
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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Article
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
     * Set active
     *
     * @param boolean $active
     * @return Article
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Article
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Article
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getFullClassName()
    {
      return get_class($this);
    }
    
    public function retrieveAlbums()
    {
      return array('principal');
    }
    
    public function showSpanishDate()
    {
      // date('d \\d\\e F, Y')
      $string = $this->getCreatedAt()->format('d'). ' de ';
      
      $string .= self::retrieveMonthNameFromNumber((int)$this->getCreatedAt()->format('n'));
      $string .= ', '.$this->getCreatedAt()->format('Y');
      return $string;
    }
    
    public static function retrieveMonthNameFromNumber($month)
    {
      $verm = array(
        1 => "Enero",
        2 => "Febrero",
        3 => "Marzo",
        4 => "Abril",
        5 => "Mayo",
        6 => "Junio",
        7 => "Julio",
        8 => "Agosto",
        9 => "Septiembre",
        10 => "Octubre",
        11 => "Noviembre", 
        12 => "Diciembre"
      );
      return $verm[$month];
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\ArticleTag $tags
     * @return Article
     */
    public function addTag(\AppBundle\Entity\ArticleTag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\ArticleTag $tags
     */
    public function removeTag(\AppBundle\Entity\ArticleTag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add categories
     *
     * @param \AppBundle\Entity\ArticleCategory $categories
     * @return Article
     */
    public function addCategory(\AppBundle\Entity\ArticleCategory $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \AppBundle\Entity\ArticleCategory $categories
     */
    public function removeCategory(\AppBundle\Entity\ArticleCategory $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set excerpt
     *
     * @param string $excerpt
     * @return Article
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * Get excerpt
     *
     * @return string 
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }
}
