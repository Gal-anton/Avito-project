<?php


use PHPUnit\Framework\TestCase;
require_once __DIR__ . "/../config/db_config.php";
/**
 * Class RecordMonitorTest
 * @uses RecordMonitor
 */
class RecordMonitorTest extends TestCase
{
    /**
     * @covers RecordMonitor::_sqlStr
     * @throws ReflectionException
     */
    public function testSqlStrOnInvalid()
    {
        $class = new ReflectionClass('RecordMonitor');
        $method = $class->getMethod('_sqlStr');
        $method->setAccessible(true);

        $recordMonitor = new RecordMonitor();

        $recordMonitor->createDBConnection();

        $result = $method->invoke($recordMonitor, '""? http://myurl.com');
        $this->assertEquals('"\"\"? http://myurl.com"', $result);
    }
/**
     * @covers RecordMonitor::_sqlStr
     * @throws ReflectionException
     */
    public function testSqlStrOnNull()
    {
        $class = new ReflectionClass('RecordMonitor');
        $method = $class->getMethod('_sqlStr');
        $method->setAccessible(true);

        $recordMonitor = new RecordMonitor();
        $recordMonitor->createDBConnection();

        $result = $method->invoke($recordMonitor, null);
        $this->assertNull($result);
    }

}
