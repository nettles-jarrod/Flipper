<?php

namespace Flipper;

use \Doctrine\DBAL\Connection as DBALConnection,
    \Doctrine\DBAL\Statement;

use \Flipper\Mapper;

class Flipper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $options = [
        'defaultSplitter'   => 'id',
        'entityStore'       => '\\'
    ];

    public function __construct(DBALConnection $connection = null, array $options = [])
    {
        $this->connection = $connection;
        $this->setOptions($options);
    }

    /**
     * Statically create an instance of Flipper.
     * @param array $options
     * @return Flipper
     */
    public static function _(array $options = [])
    {
        return new static($options);
    }

    /**
     * Set an array of options for Flipper to use in its operations.
     * @param array $options
     * @return Flipper
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @param string|array $requestedTypes
     * @param string|object|Statement $sql
     * @param array $params
     * @param string|array $split
     * @return array
     */
    public function query($requestedTypes, $sql, $params = [], $split = [])
    {
        if(!$sql instanceof Statement) {
            $sql = $this->connection->prepare($sql);
        }

        $sql->execute($params);
        $results = $sql->fetchAll();

        return Mapper::_($this->options)->map($requestedTypes, $results, $split);
    }

    /**
     * @param string|array $requestedTypes $requestedTypes
     * @param string|object|Statement $sql
     * @param array $params
     * @param string|array $split
     * @return object|array|null
     */
    public function queryOne($requestedTypes, $sql, $params = [], $split = [])
    {
        $result = $this->query($requestedTypes, $sql, $params, $split);

        if($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}
