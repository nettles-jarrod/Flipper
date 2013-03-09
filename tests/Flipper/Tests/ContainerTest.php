<?php

namespace Flipper\Tests;

use Flipper\Container;

/**
 * @group Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testMappableArray()
    {
        $mappable = new Container([]);
        $this->assertSame([], $mappable->getData());
    }

    public function testMappableIterator()
    {
        $mappable = new Container($this->getMock('\Iterator'));
        $this->assertTrue($mappable->getData() instanceof \Iterator);
    }

    public function testMappableIteratorAggregate()
    {
        $mappable = new Container($this->getMock('\IteratorAggregate'));
        $this->assertTrue($mappable->getData() instanceof \IteratorAggregate);
    }
}
