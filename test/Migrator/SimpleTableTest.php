<?php namespace PandaLeague\Migrator;

use PandaLeague\DataExporter\Migrator\SensitiveField;
use PandaLeague\DataExporter\Migrator\SimpleTable;
use PHPUnit\Framework\TestCase;

/**
 * @author george
 */
class SimpleTableTest extends TestCase
{
    /**
     * @return array
     */
    public function constructProvider()
    {
        $sensitiveField = $this->createMock(SensitiveField::class);
        return [
            ['test'],
            ['test', []],
            ['test', [$sensitiveField], [$sensitiveField]],
            [new \stdClass, [], [], \RuntimeException::class],
            ['test', [new \stdClass], [], \RuntimeException::class],
        ];
    }

    /**
     * @param $name
     * @param bool $sensitiveFields
     * @param array $expectedSensitiveFields
     * @param null $expectedException
     * @dataProvider constructProvider
     */
    public function testConstruct(
        $name,
        $sensitiveFields = false,
        $expectedSensitiveFields = [],
        $expectedException = null
    ) {
        if ($expectedException) {
            $this->expectException($expectedException);
        }
        if ($sensitiveFields) {
            $table = new SimpleTable($name, $sensitiveFields);
        } else {
            $table = new SimpleTable($name);
        }
        self::assertSame($table->getName(), 'test');
        self::assertSame($table->getSensitiveFields(), $expectedSensitiveFields);
    }
}