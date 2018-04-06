<?php namespace PandaLeague\DataExporter\Migrator;

/**
 * @author george
 */
interface Table
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return SensitiveField[]
     */
    public function getSensitiveFields($type);
}