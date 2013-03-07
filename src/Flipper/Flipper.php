<?php

namespace Flipper;

class Flipper
{
    protected $options = [
        'defaultSplitter'   => 'id',
        'entityStore'       => '\\'
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public static function _(array $options = [])
    {
        return new static($options);
    }

    public function map($requestedObjects, array $dataSource, $splitMapper = [])
    {
        if(!is_array($requestedObjects)) {
            $requestedObjects = [$requestedObjects];
        }

        $splitMapper = $this->setupSplitMapper($splitMapper);

        $result = $this->mapDataSource($requestedObjects, $dataSource, $splitMapper);

        return $result;
    }

    public function mapOne($requestedObjects, array $dataSource, $splitMapper = [])
    {
        $result = $this->map($requestedObjects, $dataSource, $splitMapper);

        if($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }

    public function loadEntity($entityName)
    {
        if(isset($this->options['entityStore'])) {
            $entityName = $this->options['entityStore'] . $entityName;
        }

        return new $entityName();
    }

    private function setupSplitMapper($splitMapper)
    {
        if(is_array($splitMapper)) {
            return $splitMapper;
        }

        if(is_null($splitMapper) || empty($splitMapper)) {
            return [$this->options['defaultSplitter']];
        }

        if(is_string($splitMapper)) {
            return explode(',', $splitMapper);
        }

        throw new \Exception('Split mapper does not follow a proper format.');
    }

    private function mapDataSource(array $requestedObjects, array $dataSource, array $splitMapper)
    {
        $results = [];

        if($this->isMultipleResults($dataSource)) {

            foreach($dataSource as $row) { //loop through each row in the resultset
                $objects = $this->createEntities($requestedObjects);
                $results[] = $this->mapRow($row, $objects, $splitMapper);
            }

        } else {
            $objects = $this->createEntities($requestedObjects);
            $results[] = $this->mapRow($dataSource, $objects, $splitMapper);
        }

        return $results;
    }

    private function mapRow($row, $objects, $splitMapper)
    {
        $currentObject = reset($objects);

        foreach($row as $key => $value) {

            if(reset($splitMapper) === $key) {
                array_shift($splitMapper);
                $currentObject = next($objects);
            }

            $setterMethod = 'set' . $key;

            if(method_exists($currentObject, $setterMethod)) {
                $currentObject->$setterMethod($value);
            } else {
                $currentObject->$key = $value;
            }
        }

        if(1 === count($objects)) {
            return reset($objects);
        }

        return $objects;
    }

    private function createEntities(array $requestedObjects)
    {
        $objects = [];

        foreach($requestedObjects as $entityName) {
            $entityReference = $this->getEntityReferenceName($entityName);
            $objects[$entityReference] = $this->loadEntity($entityName);
        }

        return $objects;
    }

    private function isMultipleResults(array $dataSource)
    {
        if(count($dataSource) == count($dataSource, COUNT_RECURSIVE)) {
            return false;
        }

        return true;
    }

    private function getEntityReferenceName($entityNamespacedClass)
    {
        $chunks = explode('\\', $entityNamespacedClass);
        return strtolower(end($chunks));
    }
}
