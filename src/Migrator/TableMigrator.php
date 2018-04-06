<?php namespace PandaLeague\DataExporter\Migrator;

use PandaLeague\DataExporter\Database\DatabaseConnection;

/**
 * @author george
 */
class TableMigrator
{
    /**
     * @var DatabaseConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $sourceDatabase;

    /**
     * @var string
     */
    private $destinationDatabase;

    /**
     * TableMigrator constructor.
     * @param DatabaseConnection $connection
     * @param string $sourceDatabase
     * @param string $destinationDatabase
     */
    public function __construct(DatabaseConnection $connection, string $sourceDatabase, string $destinationDatabase)
    {
        $this->connection = $connection;
        $this->sourceDatabase = $sourceDatabase;
        $this->destinationDatabase = $destinationDatabase;
    }

    /**
     * @param TableStructure $structure
     */
    public function action(TableStructure $structure)
    {
        $this->createDatabase();

        foreach ($structure->getTables() as $table) {
            $this->migrateTable($table);
            $this->protectData($table);
        }
    }

    /**
     *
     */
    private function createDatabase()
    {
        $this->connection->query('CREATE DATABASE IF NOT EXISTS `%s`', [$this->destinationDatabase]);
    }

    /**
     * @param Table $table
     */
    private function migrateTable(Table $table)
    {
        $this->connection->query("CREATE TABLE IF NOT EXISTS `%s`.`%s` LIKE `%s`.`%s`", [
            $this->destinationDatabase,
            $table->getName(),
            $this->sourceDatabase,
            $table->getName(),
        ]);

        $columns = $this->getColumns($table);

        $this->connection->query("INSERT IGNORE INTO `%s`.`%s` (SELECT %s FROM `%s`.`%s`)", [
            $this->destinationDatabase,
            $table->getName(),
            $columns,
            $this->sourceDatabase,
            $table->getName(),
        ]);
    }

    /**
     * @param Table $table
     * @return string
     */
    private function getColumns(Table $table)
    {
        if ( ! count($table->getSensitiveFields(SensitiveField::AT_EXPORT))) {
            return '*';
        }

        $result = $this->connection->query("SHOW COLUMNS FROM `%s`.`%s`", [
            $this->destinationDatabase,
            $table->getName(),
        ]);

        $sensitiveFields = array_reduce($table->getSensitiveFields(SensitiveField::AT_EXPORT),
            function ($input, SensitiveField $sensitiveField) {
                if ($sensitiveField->getType() == SensitiveField::AT_EXPORT) {
                    $input[$sensitiveField->getName()] = $sensitiveField->getReplacement();
                }
                return $input;
            }, []);

        return implode(array_reduce($result->toArray(), function ($input, $row) use ($sensitiveFields) {
            $input[] = isset($sensitiveFields[$row['Field']]) ? $sensitiveFields[$row['Field']] : $row['Field'];
            return $input;
        }, []), ', ');
    }

    /**
     * @param Table $table
     */
    private function protectData(Table $table)
    {
        foreach ($table->getSensitiveFields(SensitiveField::AFTER_EXPORT) as $sensitiveField) {
            $this->connection->query("UPDATE `%s`.`%s` SET `%s` = %s", [
                $this->destinationDatabase,
                $table->getName(),
                $sensitiveField->getName(),
                $sensitiveField->getReplacement(),
            ]);
        }
    }
}