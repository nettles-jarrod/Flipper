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
        'entityStore'       => '\\'
    ];

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param array $options
     */
    public function __construct(DBALConnection $connection = null, array $options = [])
    {
        $this->connection = $connection;
        $this->setOptions($options);
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
     * Set the DBAL connection to use.
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function setConnection(DBALConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Execute a query and map it to your requested types. Returns an array of your rows.
     * @param string|array $requestedTypes
     * @param string|object|Statement $sql
     * @param array $params
     * @param string|array $split
     * @throws \InvalidArgumentException
     * @return array
     */
    public function query($requestedTypes, $sql, $params = [], $split = [])
    {
        if(is_null($requestedTypes) || empty($requestedTypes)) {
            throw new \InvalidArgumentException('You must specify at least one type to map your results against.');
        }

        if(!$sql instanceof Statement) {
            $sql = $this->connection->prepare($sql);
        }

        $sql->execute($params);
        $results = $sql->fetchAll();

        return Mapper::_($this->options)->map($requestedTypes, $results, $split);
    }

    /**
     * Execute a query and map it to your requested types. Returns a single object OR an array of
     * objects representing the first row in your result set.
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
