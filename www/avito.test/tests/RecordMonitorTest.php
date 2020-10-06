<?php


use PHPUnit\Framework\TestCase;

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
    public function testSqlStr()
    {
        $class = new ReflectionClass('RecordMonitor');
        $method = $class->getMethod('_sqlStr');
        $method->setAccessible(true);

        $recordMonitor = new RecordMonitor();
        $recordMonitor->createDBConnection();

        $result = $method->invoke($recordMonitor, '""? http://myurl.com');
        $this->assertEquals('"\"\"? http://myurl.com"', $result);

    }


}
