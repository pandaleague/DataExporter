<?php namespace PandaLeague\DataExporter\Migrator;

/**
 * @author george
 */
class SimpleFieldReplacement implements SensitiveField
{
    const TYPES = [
        self::AT_EXPORT,
        self::AFTER_EXPORT,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $replacement;

    /**
     * @var string
     */
    private $type;

    /**
     * SimpleFieldReplacement constructor.
     * @param string $name
     * @param string $replacement
     * @param string $type
     */
    public function __construct($name, $replacement, $type = self::AT_EXPORT)
    {
        if ( ! in_array($type, self::TYPES)) {
            throw new \RuntimeException('Type not available: ' . $type);
        }

        $this->name = $name;
        $this->replacement = $replacement;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}