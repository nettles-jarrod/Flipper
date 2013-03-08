<?php

namespace Flipper\Mapper;

use Flipper\Container,
    Flipper\DataException,
    Flipper\Mappable;

class Mapper implements Mappable
{
    protected $options = [
        'defaultSplitter'   => 'id',
        'entityStore'       => '\\'
    ];

    public static function _(array $options = [])
    {
        return new static($options);
    }

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function map($requestedTypes, $data, $splitMapper = [])
    {
        $source = new Container($data);

        if(!is_array($requestedTypes)) {
            $requestedTypes = [$requestedTypes];
        }

        $splitMapper = self::setupSplitMapper($splitMapper);

        return $this->mapData($requestedTypes, $source, $splitMapper);
    }

    public function mapOne($requestedTypes, $data, $splitMapper = [])
    {
        $result = $this->map($requestedTypes, $data, $splitMapper);

        if($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }

    protected function mapData(array $requestedTypes, Container $data, array $splitMapper)
    {
        $results = [];

        if($data->hasMultipleResults()) {
            foreach($data->getData() as $row) {
                $objects = $this->createTypes($requestedTypes);
                $results[] = $this->mapRow($row, $objects, $splitMapper);
            }
        } else {
            $objects = $this->createTypes($requestedTypes);
            $results[] = $this->mapRow($data->getData(), $objects, $splitMapper);
        }

        return $results;
    }

    protected function mapRow(array $row, array $objects, array $splitMapper)
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

    protected function createTypes($requestedTypes)
    {
        $objects = [];

        foreach($requestedTypes as $type) {
            $entityReference = $this->getTypeName($type);
            $objects[$entityReference] = $this->loadType($type);
        }

        return $objects;
    }

    private function getTypeName($namespacedType)
    {
        $namespaces = explode('\\', $namespacedType);
        return strtolower(end($namespaces));
    }

    protected function loadType($type)
    {
        if(isset($this->options['entityStore'])) {
            $type = $this->options['entityStore'] . $type;
        }

        return new $type();
    }

    protected function setupSplitMapper($splitMapper)
    {
        if(is_array($splitMapper)) {
            return $splitMapper;
        }

        if(is_string($splitMapper)) {
            return explode(',', $splitMapper);
        }

        return [];
    }
}
