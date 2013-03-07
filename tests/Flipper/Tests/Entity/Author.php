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
     * @ORM\Column(name="name")
     */
    public $name;

    /**
     * @ORM\Column(name="birth_date")
     */
    public $birth;
}
