<?php

namespace Flipper;

use Flipper\Container;

class Mapper
{
    /**
     * Default options to use with the mapper.
     * @var array
     */
    protected $options = [
        'defaultSplitter'   => 'id',
        'entityStore'       => '\\'
    ];

    /**
     * This provides a fast lookup mechanism for creating new objects properly.
     * @var array
     */
    protected static $typeStore = [];

    /**
     * The Mapper is a standalone, data-agnostic class that maps arrays of data to objects.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Set an array of options for the mapper to use.
     * @param array $options
     * @return Mapper
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Statically create an instance of the Mapper class.
     * @param array $options
     * @return Mapper
     */
    public static function _(array $options = [])
    {
        return new static($options);
    }

    /**
     * Map multiple rows of data to the requested types. It will return an array of objects.
     * @param $requestedTypes
     * @param $data
     * @param array $split
     * @return array
     */
    public function map($requestedTypes, $data, $split = [])
    {
        $source = new Container($data);

        if(!is_array($requestedTypes)) {
            $requestedTypes = [$requestedTypes];
        }

        $splitMapper = self::setupSplitMapper($split);

        return $this->mapData($requestedTypes, $source, $splitMapper);
    }

    /**
     * Map data to requested types. It will return a single object, or - if you request a split - an array of the types
     * you requested. The resulting array will be the size of the number of types that you request.
     *
     * @param string|array $requestedTypes The types that you wish your data to be mapped against.
     * @param $data mixed A single row of data to map.
     * @param array $split The property name to split the results on. This lets the mapper know to move to the next
     * requested type in its mapping. You can specify multiple splitters in an array or a single one in a string.
     * @return array|object|null
     */
    public function mapOne($requestedTypes, $data, $split = [])
    {
        $result = $this->map($requestedTypes, $data, $split);

        if($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }

    /**
     * Iterates over the rows of data in the container and sends each row to the row mapper.
     * @param array $requestedTypes
     * @param Container $data
     * @param array $splitMapper
     * @return array
     */
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

    /**
     * Iterates over the columns of data in the row and sends each requested object/key-value pair
     * to the property mapper. If multiple objects are requested, an array of those objects will be
     * returned. If a single object is requested, only that object will be returned.
     * @param array|object $row
     * @param array $objects
     * @param array $splitMapper
     * @return array|object
     */
    protected function mapRow($row, array $objects, array $splitMapper)
    {
        $currentObject = reset($objects);

        foreach($row as $key => $value) {

            if(reset($splitMapper) === $key) {
                array_shift($splitMapper);
                $currentObject = next($objects);
            }

            $this->mapProperty($currentObject, $key, $value);
        }

        if(1 === count($objects)) {
            return reset($objects);
        }

        return $objects;
    }

    /**
     * Attempt to map the value to an existing object property. This method will attempt to call any existing setter for the property
     * based on standard conventions for setters.
     * @param object $targetObject
     * @param string $propertyName
     * @param string $value
     */
    protected function mapProperty($targetObject, $propertyName, $value)
    {
        $setterMethod = 'set' . str_replace('_', '', $propertyName);

        if(is_numeric($value)) {
            $value += 0; //trick to convert numeric values
        }

        if(method_exists($targetObject, $setterMethod)) {
            $targetObject->$setterMethod($value);
        } else {
            $targetObject->$propertyName = $value;
        }
    }

    /**
     * Create the requested types needed for the next row of data.
     * @param array $requestedTypes
     * @return array
     */
    protected function createTypes(array $requestedTypes)
    {
        $objects = [];

        foreach($requestedTypes as $type) {
            $entityReference = $this->getTypeName($type);
            $objects[$entityReference] = $this->loadType($type);
        }

        return $objects;
    }

    /**
     * Get the name of the class without its namespace for our return array, since we don't want
     * to have access it through its fully qualified namespace. I.e. Use $result['post'] instead
     * of $result['App\Entities\Post'].
     * @param string$namespacedType
     * @return string
     */
    private function getTypeName($namespacedType)
    {
        $namespaces = explode('\\', $namespacedType);
        return strtolower(end($namespaces));
    }

    /**
     * Created the requested type, either by providing its fully qualified namespace or by using
     * the entity store set in the options.
     * @param string $type
     * @return object
     */
    protected function loadType($type)
    {
        if(isset($this->options['entityStore'])) {
            $type = $this->options['entityStore'] . '\\' . $type;
        }

        return new $type();
    }

    /**
     * Turns requested split maps into an array.
     * @param $splitMapper
     * @return array
     */
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
