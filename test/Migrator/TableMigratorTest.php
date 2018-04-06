<?php namespace PandaLeague\DataExporter\Migrator;

use PandaLeague\DataExporter\Database\DatabaseConnection;
use PandaLeague\DataExporter\Database\DatabaseResult;
use PHPUnit\Framework\TestCase;

/**
 * @author george
 */
class TableMigratorTest extends TestCase
{
    /**
     * @return array
     */
    public function actionProvider()
    {
        $source = 'source';
        $destination = 'destination';
        $sensitiveFieldAtExport = new SimpleFieldReplacement('one', "'won'");
        $sensitiveFieldAfterExport = new SimpleFieldReplacement('two', "'too'", SensitiveField::AFTER_EXPORT);
        $table_one = new SimpleTable('table_one', [$sensitiveFieldAtExport]);
        $table_two = new SimpleTable('table_two', [$sensitiveFieldAfterExport]);
        $table_three = new SimpleTable('table_three', [$sensitiveFieldAtExport, $sensitiveFieldAfterExport]);
        $columns = [['Field' => 'one'], ['Field' => 'two'], ['Field' => 'three']];
        return [
            [$source, $destination, [$table_two], $columns, "*", ['two', "'too'"]],
            [$source, $destination, [$table_three], $columns, "'won', two, three", ['two', "'too'"]],
            [$source, $destination, [$table_one], $columns, "'won', two, three", []],
        ];
    }

    /**
     * @param $source
     * @param $destination
     * @param $tables
     * @param $columns
     * @param $replaced_columns
     * @param $after_export
     * @throws \ReflectionException
     * @dataProvider actionProvider
     */
    public function testAction($source, $destination, $tables, $columns, $replaced_columns, $after_export)
    {
        $consecutiveCalls = $this->getConsecutiveCalls($source, $destination, $tables, $replaced_columns,
            $after_export);

        $tableMigrator = new TableMigrator($this->databaseConnectionMock($consecutiveCalls, $columns), $source,
            $destination);
        $tableStructure = new TableStructure($tables);
        $tableMigrator->action($tableStructure);
    }

    /**
     * @param string $sourceDatabase
     * @param string $destinationDatabase
     * @param array $tables
     * @param string $fields
     * @param array $after_export
     * @return array
     */
    private function getConsecutiveCalls(
        string $sourceDatabase,
        string $destinationDatabase,
        array $tables,
        string $fields = '*',
        array $after_export = []
    ) {
        return array_reduce($tables,
            function ($existing, Table $table) use ($sourceDatabase, $destinationDatabase, $fields, $after_export) {
                $existing[] = [
                    'CREATE TABLE IF NOT EXISTS `%s`.`%s` LIKE `%s`.`%s`',
                    [$destinationDatabase, $table->getName(), $sourceDatabase, $table->getName()],
                ];
                if (count($table->getSensitiveFields(SensitiveField::AT_EXPORT))) {
                    $existing[] = [
                        'SHOW COLUMNS FROM `%s`.`%s`',
                        [$destinationDatabase, $table->getName()],
                    ];
                }

                $existing[] = [
                    'INSERT IGNORE INTO `%s`.`%s` (SELECT %s FROM `%s`.`%s`)',
                    [$destinationDatabase, $table->getName(), $fields, $sourceDatabase, $table->getName()],
                ];

                if (count($table->getSensitiveFields(SensitiveField::AFTER_EXPORT))) {
                    $existing[] = [
                        'UPDATE `%s`.`%s` SET `%s` = %s',
                        array_merge([$destinationDatabase, $table->getName()], $after_export),
                    ];
                }

                return $existing;
            },
            [['CREATE DATABASE IF NOT EXISTS `%s`', [$destinationDatabase]]]
        );
    }

    /**
     * @param $consecutiveCalls
     * @param $columns
     * @return DatabaseConnection
     */
    private function databaseConnectionMock(
        $consecutiveCalls,
        $columns
    ) // $sourceDatabase, $destinationDatabase, $tables = [])
    {
        $databaseConnection = $this->createMock(DatabaseConnection::class);

        $databaseConnection->expects(self::exactly(count($consecutiveCalls)))
            ->method('query')
            ->willReturnCallback(function ($query, $args) use ($columns) {
                if (strpos($query, 'SHOW COLUMNS') !== false) {
                    return $this->resultMock($columns);
                }
            })
            ->withConsecutive(...$consecutiveCalls);

        return $databaseConnection;
    }

    /**
     * @param array $toArray
     * @return DatabaseResult
     */
    private function resultMock(array $toArray)
    {
        $result = $this->createMock(DatabaseResult::class);
        $result->expects(self::any())
            ->method('toArray')
            ->willReturn($toArray);
        return $result;
    }
}