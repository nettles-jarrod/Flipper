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
     * @param $requestedTypes
     * @param $sql
     * @param array $params
     * @param array $splitMapper
     * @return array
     */
    public function query($requestedTypes, $sql, $params = [], $splitMapper = [])
    {
        if(!$sql instanceof Statement) {
            $sql = $this->connection->prepare($sql);
        }

        $sql->execute($params);
        $results = $sql->fetchAll();

        return Mapper::_($this->options)->map($requestedTypes, $results, $splitMapper);
    }

    /**
     * @param $requestedTypes
     * @param $sql
     * @param array $params
     * @param array $splitMapper
     * @return object|array|null
     */
    public function queryOne($requestedTypes, $sql, $params = [], $splitMapper = [])
    {
        $result = $this->query($requestedTypes, $sql, $params, $splitMapper);

        if($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}
