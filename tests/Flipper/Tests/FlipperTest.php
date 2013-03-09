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

    public function testMultipleRowSelect()
    {

    }
}
