<?php

namespace Plugin\SimpleBlog42\Entity;

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="dtb_sb_category")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SimpleBlog42\Repository\CategoryRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Category extends AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
     */
    protected $sort_no;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\SimpleBlog42\Entity\BlogCategory", mappedBy="Tag")
     */
    protected $BlogCategory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlogCategory = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @return $this
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
     * Set sort_no.
     *
     * @param int $sort_no
     *
     * @return $this
     */
    public function setSortNo($sort_no)
    {
        $this->sort_no = $sort_no;

        return $this;
    }

    /**
     * Get sort_no.
     *
     * @return int
     */
    public function getSortNo()
    {
        return $this->sort_no;
    }

    /**
     * Add BlogCategory.
     *
     * @param \Plugin\SimpleBlog42\Entity\BlogCategory $blogCategory
     *
     * @return BlogCategory
     */
    public function addBlogCategory(BlogCategory $blogCategory)
    {
        $this->BlogCategory[] = $blogCategory;

        return $this;
    }

    /**
     * Remove blogCategory.
     *
     * @param \Plugin\SimpleBlog42\Entity\BlogCategory $blogCategory
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlogCategory(BlogCategory $blogCategory)
    {
        return $this->BlogCategory->removeElement($blogCategory);
    }

    /**
     * Get blogCategory.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlogCategory()
    {
        return $this->BlogCategory;
    }
}
