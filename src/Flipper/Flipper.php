<?php

namespace Flipper;

use \Doctrine\DBAL\Driver\Connection as DBALConnection,
    \Doctrine\DBAL\Statement;

use \Flipper\Mapper;

class Flipper
{
    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $options = [
        'entityStore'       => '\\'
    ];

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @param \Doctrine\DBAL\Driver\Connection $connection
     * @param array $options
     */
    public function __construct(DBALConnection $connection = null, array $options = [])
    {
        $this->connection = $connection;
        $this->setOptions($options);
        $this->mapper = new Mapper($this->options);
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

        return $this->mapper->map($requestedTypes, $results, $split);
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
     * @param \Doctrine\DBAL\Driver\Connection $connection
     */
    public function setConnection(DBALConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Mapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}
