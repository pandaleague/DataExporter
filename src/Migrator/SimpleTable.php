<?php namespace PandaLeague\DataExporter\Migrator;

/**
 * @author george
 */
/**
 * Class SimpleTable
 * @package PandaLeague\DataExporter\Migrator
 */
class SimpleTable implements Table
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var SensitiveField[]
     */
    private $sensitiveFields = [];

    /**
     * SimpleTable constructor.
     * @param string $name
     * @param array $sensitiveFields
     */
    public function __construct($name, array $sensitiveFields = [])
    {
        if ( ! is_string($name)) {
            throw new \RuntimeException('name must be string');
        }

        $this->name = $name;

        foreach ($sensitiveFields as $sensitiveField) {
            if ( ! ($sensitiveField instanceof SensitiveField)) {
                throw new \RuntimeException('must be instance of SensitiveField');
            }

            $this->addSensitiveField($sensitiveField);
        }
    }

    /**
     * @param SensitiveField $sensitiveField
     */
    private function addSensitiveField(SensitiveField $sensitiveField)
    {
        $this->sensitiveFields[] = $sensitiveField;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $type
     * @return SensitiveField[]
     */
    public function getSensitiveFields($type = null)
    {
        if (is_null($type)) {
            return $this->sensitiveFields;
        }

        return array_reduce($this->sensitiveFields, function (array $fields, SensitiveField $field) use ($type) {
            if ($field->getType() === $type) {
                $fields[] = $field;
            }
            return $fields;
        }, []);
    }
}