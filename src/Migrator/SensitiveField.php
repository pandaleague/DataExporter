<?php namespace PandaLeague\DataExporter\Migrator;

/**
 * @author george
 */
interface SensitiveField
{
    const AT_EXPORT = 'at_export';
    const AFTER_EXPORT = 'after_export';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getReplacement();

    /**
     * @return string
     */
    public function getType();
}