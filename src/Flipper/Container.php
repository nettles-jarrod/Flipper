<?php

namespace Flipper;

class Container
{
    /**
     * @var array|\Iterator|\IteratorAggregate
     */
    protected $source;

    /**
     * This is a container for data that can be mapped by Flipper. You can pass in a PHP array or any object that
     * implements the Iterator or IteratorAggregate interfaces.
     * @param $source
     * @throws DataException
     */
    public function __construct($source)
    {
        if(!$this->isMappable($source)) {
            throw new DataException('Whoops, the data you passed to Flipper isn\'t traversable. Did you
            forget to pass in an array or something that implements Iteratator or IteratorAggregate?');
        }

        $this->source = $source;
    }

    /**
     * Check to see if this source is a single row of if it has multiple rows of data. Returns true when there are multiple rows.
     * @return bool
     */
    public function hasMultipleResults()
    {
        if(count($this->source) == count($this->source, COUNT_RECURSIVE)) {
            return false;
        }

        return true;
    }

    /**
     * Calling this will always return a datatype that can be iterated over with foreach.
     * @return array|\Iterator|\IteratorAggregate
     */
    public function getData()
    {
        return $this->source;
    }

    /**
     * Analyze a provided data source and determine if it can be used for mapping. Basically,
     * check to see if its an array or an implementation of Traversable.
     * @param array|\Traversable $source
     * @return bool
     */
    protected function isMappable($source)
    {
        if(is_array($source)) {
            return true;
        }

        if(is_object($source) && ($source instanceof \Iterator || $source instanceof \IteratorAggregate )) {
            return true;
        }

        return false;
    }
}
