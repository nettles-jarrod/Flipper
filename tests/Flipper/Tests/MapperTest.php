<?php

namespace Flipper\Tests;

use Flipper\Mapper;

/**
 * @group Mapper
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Flipper\Mapper
     */
    protected $mapper;

    protected $singleSet = [
        'post_id'   => 1,
        'title'     => 'title1',
        'body'      => 'This is a very short post.'
    ];

    protected $multipleSet = [
        [
            'post_id'   => 1,
            'title'     => 'title1',
            'body'      => 'This is a very short post.'
        ],
        [
            'post_id'   => 2,
            'title'     => 'title2',
            'body'      => 'This another very short post.'
        ],
    ];

    protected function setUp()
    {
        $this->mapper = new Mapper([
            'entityStore' => 'Flipper\\Tests\\Entity'
        ]);
    }

    public function testStaticConstruct()
    {
        $mapper = Mapper::_();
        $this->assertInstanceOf('\Flipper\Mapper', $mapper);
    }

    public function testMapOne()
    {
        $post = $this->mapper->mapOne('Post', $this->singleSet);

        $this->assertSame(1, $post->getPostId());
        $this->assertSame('title1', $post->getTitle());
        $this->assertSame('This is a very short post.', $post->getBody());
    }

    public function testMap()
    {
        $post = $this->mapper->map('Post', $this->singleSet);

        $this->assertSame(1, $post[0]->getPostId());
        $this->assertSame('title1', $post[0]->getTitle());
        $this->assertSame('This is a very short post.', $post[0]->getBody());
    }

    public function testMultiRowMap()
    {
        $posts = $this->mapper->map('Post', $this->multipleSet);

        $this->assertSame(1, $posts[0]->getPostId());
        $this->assertSame('title1', $posts[0]->getTitle());
        $this->assertSame('This is a very short post.', $posts[0]->getBody());

        $this->assertSame(2, $posts[1]->getPostId());
        $this->assertSame('title2', $posts[1]->getTitle());
        $this->assertSame('This another very short post.', $posts[1]->getBody());
    }

    public function testSimpleMultipleObjectMap()
    {
        $set = [
            'author_id' => 1,
            'name'      => 'Jack London',
            'birth'     => '1/12/1876',
            'post_id'   => 3,
            'title'     => 'White Fang',
            'body'      => 'Dark spruce forest frowned on either side the frozen waterway.'
        ];

        $results = $this->mapper->mapOne(['Author', 'Post'], $set, $splitMapper = ['post_id']);

        $this->assertSame(1,             $results['author']->author_id);
        $this->assertSame('Jack London', $results['author']->name);
        $this->assertSame('1/12/1876',   $results['author']->birth);

        $this->assertSame(3,             $results['post']->getPostId());
        $this->assertSame('White Fang',  $results['post']->getTitle());
        $this->assertStringStartsWith('Dark spruce forest', $results['post']->getBody());
    }

    public function testMultipleObjectMap()
    {
        $set = [
            [
                'author_id' => 1,
                'name'      => 'Jack London',
                'birth'     => '1/12/1876',
                'post_id'   => 3,
                'title'     => 'White Fang',
                'body'      => 'Dark spruce forest frowned on either side the frozen waterway.'
            ],
            [
                'author_id' => 2,
                'name'      => 'Herman Melville',
                'birth'     => '8/1/1819',
                'post_id'   => 4,
                'title'     => 'Moby-Dick',
                'body'      => 'Call me Ishmael.'
            ]
        ];


        $results = $this->mapper->map(['Author', 'Post'], $set, $splitMapper = ['post_id']);

        $this->assertCount(2, $results);

        $this->assertSame('White Fang', $results[0]['post']->getTitle());
        $this->assertSame('Moby-Dick',  $results[1]['post']->getTitle());
    }
}
