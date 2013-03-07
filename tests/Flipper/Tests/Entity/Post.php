<?php

namespace Flipper\Tests\Entity;

use Doctrine\ORM\Mapping AS ORM;

class Post
{
    /**
     * @ORM\Column(name="post_id")
     */
    protected $id;

    /**
     * @ORM\Column(name="post_title")
     */
    protected $title;

    /**
     * @ORM\Column(name="post_body")
     */
    protected $body;

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setID($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
