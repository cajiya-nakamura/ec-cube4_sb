<?php

namespace Plugin\SimpleBlog42\Entity;

use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Member;
use Doctrine\ORM\Mapping as ORM;

/**
 * BlogCategory
 *
 * @ORM\Table(name="plg_sb_blog_category")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SimpleBlog42\Repository\BlogCategoryRepository")
 */
class BlogCategory extends AbstractEntity
{
    /**
     * Get Category id
     * use csv export
     *
     * @return integer
     */
    public function getCategoryId()
    {
        if (empty($this->Category)) {
            return null;
        }

        return $this->Category->getId();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \Plugin\SimpleBlog42\Entity\Blog
     *
     * @ORM\ManyToOne(targetEntity="Plugin\SimpleBlog42\Entity\Blog", inversedBy="BlogCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
     * })
     */
    private $Blog;

    /**
     * @var \Plugin\SimpleBlog42\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Plugin\SimpleBlog42\Entity\Category", inversedBy="BlogCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $Category;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;

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
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return ProductTag
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set blog.
     *
     * @param \Plugin\SimpleBlog42\Entity\Blog|null $blog
     *
     * @return BlogCategory
     */
    public function setBlog(Blog $blog = null)
    {
        $this->Blog = $blog;

        return $this;
    }

    /**
     * Get blog.
     *
     * @return \Plugin\SimpleBlog42\Entity\Blog|null
     */
    public function getBlog()
    {
        return $this->Blog;
    }

    /**
     * Set Category.
     *
     * @param \Plugin\SimpleBlog42\Entity\Category|null $category
     *
     * @return BlogCategory
     */
    public function setCategory(Category $category = null)
    {
        $this->Category = $category;

        return $this;
    }

    /**
     * Get Category.
     *
     * @return \Plugin\SimpleBlog42\Entity\Category|null
     */
    public function getCategory()
    {
        return $this->Category;
    }

    /**
     * Set creator.
     *
     * @param \Plugin\SimpleBlog42\Entity\Member|null $creator
     *
     * @return BlogCategory
     */
    public function setCreator(Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}
