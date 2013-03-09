<?php

namespace Flipper\Tests\Entity;

use Doctrine\ORM\Mapping AS ORM;

class Post
{
    /**
     * @ORM\Column(name="post_id")
     */
    protected $post_id;

    /**
     * @ORM\Column(name="author_id")
     */
    protected $author_id;

    /**
     * @ORM\Column(name="created_date")
     */
    protected $created_date;

    /**
     * @ORM\Column(name="title")
     */
    protected $title;

    /**
     * @ORM\Column(name="body")
     */
    protected $body;

    public function setPostId($post_id)
    {
        $this->post_id = $post_id;
    }

    public function getPostId()
    {
        return $this->post_id;
    }

    public function setAuthorId($author_id)
    {
        $this->author_id = $author_id;
    }

    public function getAuthorId()
    {
        return $this->author_id;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }
}
