<?php namespace PandaLeague\DataExporter\Migrator;

/**
 * @author george
 */
class TableStructure
{
    /**
     * @var Table[]
     */
    private $tables;

    /**
     * TableStructure constructor.
     * @param array $tables
     */
    public function __construct(array $tables)
    {
        foreach ($tables as $table) {
            $this->addTable($table);
        }
    }

    /**
     * @param Table $table
     */
    private function addTable(Table $table)
    {
        $this->tables[] = $table;
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}