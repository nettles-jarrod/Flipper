<?php

namespace Flipper\Tests;

use Flipper\Flipper;

class FlipperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Flipper\Flipper
     */
    protected $flipper;

    protected $singleSet = [
        'id'    => 1,
        'title' => 'title1',
        'body'  => 'This is a very short post.'
    ];

    protected $multipleSet = [
        [
            'id'    => 1,
            'title' => 'title1',
            'body'  => 'This is a very short post.'
        ],
        [
            'id'    => 2,
            'title' => 'title2',
            'body'  => 'This another very short post.'
        ],
    ];

    protected function setUp()
    {
        $this->flipper = new Flipper([
            'entityStore' => 'Flipper\\Tests\\Entity\\'
        ]);
    }

    public function testEntityLoader()
    {
        $entity = $this->flipper->loadEntity('Post');

        $this->assertInstanceOf('Flipper\Tests\Entity\Post', $entity);
    }

    public function testMapOne()
    {
        $post = $this->flipper->mapOne('Post', $this->singleSet);

        $this->assertSame(1, $post->getId());
        $this->assertSame('title1', $post->getTitle());
        $this->assertSame('This is a very short post.', $post->getBody());
    }

    public function testMap()
    {
        $post = $this->flipper->map('Post', $this->singleSet);

        $this->assertSame(1, $post[0]->getId());
        $this->assertSame('title1', $post[0]->getTitle());
        $this->assertSame('This is a very short post.', $post[0]->getBody());
    }

    public function testMultiRowMap()
    {
        $posts = $this->flipper->map('Post', $this->multipleSet);

        $this->assertSame(1, $posts[0]->getId());
        $this->assertSame('title1', $posts[0]->getTitle());
        $this->assertSame('This is a very short post.', $posts[0]->getBody());

        $this->assertSame(2, $posts[1]->getId());
        $this->assertSame('title2', $posts[1]->getTitle());
        $this->assertSame('This another very short post.', $posts[1]->getBody());
    }

    public function testSimpleMultipleObjectMap()
    {
        $set = [
            'author_id' => 1,
            'name'      => 'Jack London',
            'birth'     => '1/12/1876',
            'id'        => 3,
            'title'     => 'White Fang',
            'body'      => 'Dark spruce forest frowned on either side the frozen waterway.'
        ];

        $results = $this->flipper->mapOne(['Author', 'Post'], $set, $splitMapper = ['id']);

        $this->assertSame(1,             $results['author']->author_id);
        $this->assertSame('Jack London', $results['author']->name);
        $this->assertSame('1/12/1876',   $results['author']->birth);

        $this->assertSame(3,             $results['post']->getId());
        $this->assertSame('White Fang',  $results['post']->getTitle());
        $this->assertStringStartsWith('Dark spruce forest', $results['post']->getBody());
    }
}
