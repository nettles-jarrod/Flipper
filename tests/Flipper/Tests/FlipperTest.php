<?php

namespace Flipper\Tests;

use Flipper\Flipper;
use Doctrine\DBAL\DriverManager;

/**
 * @group Flipper
 */
class FlipperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Flipper\Flipper
     */
    protected $flipper;

    protected function setUp()
    {
        $params = [
            'dbname'    => 'flippertest',
            'user'      => 'root',
            'host'      => '127.0.0.1',
            'driver'    => 'pdo_mysql'
        ];

        $connection = DriverManager::getConnection($params);

        $this->flipper = new Flipper($connection, $options = ['entityStore' => 'Flipper\\Tests\\Entity']);
    }

    public function testSimpleSelect()
    {
        $author = $this->flipper->queryOne('Author', 'select * from author where author_id = :id', ['id' => 1]);

        $this->assertSame(1, $author->author_id);
        $this->assertSame('Jack', $author->first_name);
        $this->assertSame('London', $author->last_name);
    }

    public function testSingleResultWithSplit()
    {
        $query = 'select
                    a.*,
                    p.*
                  from post p
                  left join author a on p.author_id = a.author_id
                  where p.post_id = :id';

        $result = $this->flipper->queryOne(['Author', 'Post'], $query, ['id' => 1], $splitMapper = 'post_id');

        $author = $result['author']; /** @var $author \Flipper\Tests\Entity\Author */
        $post = $result['post']; /** @var $post \Flipper\Tests\Entity\Post */

        $this->assertSame(1, $author->author_id);
        $this->assertSame('Jack', $author->first_name);
        $this->assertSame('London', $author->last_name);

        $this->assertSame('Some post.', $post->getTitle());
    }

    public function testMultipleRowSelect()
    {
        $query = 'select * from post where author_id = :id';
        $authors = $this->flipper->query('Author', $query, ['id' => 1]);

        $this->assertCount(3, $authors, 'Query did not return 3 posts.');
    }
}
