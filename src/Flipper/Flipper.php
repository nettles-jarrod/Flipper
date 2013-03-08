<?php

namespace Flipper;

use \Doctrine\DBAL\Connection as DBALConnection,
    \Doctrine\DBAL\Statement;

use \Flipper\Mapper\Mapper;

class Flipper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    protected $options = [
        'defaultSplitter'   => 'id',
        'entityStore'       => '\\'
    ];

    public function __construct(DBALConnection $connection = null, array $options = [])
    {
        $this->connection = $connection;
        $this->setOptions($options);
    }

    public static function _(array $options = [])
    {
        return new static($options);
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function query($requestedTypes, $sql, $params = [], $splitMapper = [])
    {
        if(!$sql instanceof Statement) {
            $sql = $this->connection->prepare($sql);
        }

        $sql->execute($params);
        $results = $sql->fetchAll();

        return Mapper::_($this->options)->map($requestedTypes, $results, $splitMapper);
    }

    public function queryOne($requestedTypes, $sql, $params = [], $splitMapper = [])
    {
        $result = $this->query($requestedTypes, $sql, $params, $splitMapper);

        if($result && isset($result[0])) {
            return $result;
        }

        return null;
    }

    protected function bindParameters(Statement $statement, array $params)
    {
        foreach($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
    }
}
