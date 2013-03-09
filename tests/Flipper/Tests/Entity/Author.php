<?php

namespace Flipper\Tests\Entity;

use Doctrine\ORM\Mapping AS ORM;


class Author
{
    /**
     * @ORM\Column(name="author_id")
     */
    public $author_id;

    /**
     * @ORM\Column(name="first_name")
     */
    public $first_name;

    /**
     * @ORM\Column(name="last_name")
     */
    public $last_name;
}
