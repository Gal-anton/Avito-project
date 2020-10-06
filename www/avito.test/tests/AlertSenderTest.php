<?php

require_once __DIR__ . "/../config/db_config.php";

use PHPUnit\Framework\TestCase;

/**
 * Class RecordMonitorTest
 * @uses AlertSender
 */
class AlertSenderTest extends TestCase
{
    /**
     * @covers AlertSender::checkName
     */
    public function testCheckNameOnValidName()
    {

        $nameSample = "Anton";
        $expected   = ", Anton";
        $class = new ReflectionClass('AlertSender');
        $method = $class->getMethod('checkName');
        $method->setAccessible(true);

        $result = $method->invoke(new AlertSender(), $nameSample);

        $this->assertSame($expected, $result);
    }
    /**
     * @covers AlertSender::checkName
     */
    public function testCheckNameOnNullName()
    {

        $nameSample = null;
        $expected   = "";
        $class = new ReflectionClass('AlertSender');
        $method = $class->getMethod('checkName');
        $method->setAccessible(true);

        $result = $method->invoke(new AlertSender(), $nameSample);

        $this->assertSame($expected, $result);
    }

    /**
     * @covers AlertSender::checkPrice
     * @throws ReflectionException
     */
    public function testCheckPriceOnValidName()
    {

        $priceSample = 1234.543;
        $expected   = $priceSample;
        $class = new ReflectionClass('AlertSender');
        $method = $class->getMethod('checkPrice');
        $method->setAccessible(true);

        $result = $method->invoke(new AlertSender(), $priceSample);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers AlertSender::checkName
     * @throws ReflectionException
     */
    public function testCheckPriceOnNull()
    {

        $priceSample = null;
        $expected   = "Цена не указана";
        $class = new ReflectionClass('AlertSender');
        $method = $class->getMethod('checkPrice');
        $method->setAccessible(true);

        $result = $method->invoke(new AlertSender(), $priceSample);

        $this->assertSame($expected, $result);
    }
}
