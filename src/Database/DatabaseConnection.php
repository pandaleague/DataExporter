<?php namespace PandaLeague\DataExporter\Database;

/**
 * @author george
 */
interface DatabaseConnection
{
    /**
     * @param string $query
     * @param array $params
     * @return DatabaseResult
     */
    public function query($query, array $params);
}