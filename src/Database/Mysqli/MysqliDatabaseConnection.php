<?php namespace PandaLeague\DataExporter\Database\Mysqli;

use PandaLeague\DataExporter\Database\DatabaseConnection;

/**
 * @author george
 */
class MysqliDatabaseConnection implements DatabaseConnection
{
    /**
     * @var \mysqli connection
     */
    private $connection;

    /**
     * MysqliDatabaseConnection constructor.
     * @param $host
     * @param $username
     * @param $password
     */
    public function __construct($host, $username, $password)
    {
        $this->connection = new \mysqli($host, $username, $password);
    }

    /**
     * @inheritdoc
     */
    public function query(string $query, array $params)
    {
        var_dump(vsprintf($query, $params));
        $result = $this->connection->query(vsprintf($query, $params));
        return is_bool($result) ? $result : new MysqliDatabaseResult($result);
    }

}