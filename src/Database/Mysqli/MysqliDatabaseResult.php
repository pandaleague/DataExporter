<?php namespace PandaLeague\DataExporter\Database\Mysqli;

use PandaLeague\DataExporter\Database\DatabaseResult;

/**
 * @author george
 */
class MysqliDatabaseResult implements DatabaseResult
{
    /**
     * @var \mysqli_result
     */
    private $result;

    /**
     * MysqliDatabaseResult constructor.
     * @param \mysqli_result $result
     */
    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        // comment
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

}