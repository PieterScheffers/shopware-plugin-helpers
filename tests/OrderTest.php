<?php

use pisc\Shopware\Order;

class OrderTest extends PHPUnit_Framework_TestCase 
{
    public function setUp() 
    {
        echo __CLASS__ . "/" . $this->getName() . "\n";
    }

    public function testPaymentStatusName()
    {
    	$this->assertEquals( Order::paymentStatusName(21), "Review necessary" );
		$this->assertEquals( Order::paymentStatusName(6), null );
    }

    public function testPaymentStatusNumber()
    {
    	$this->assertEquals( Order::paymentStatusNumber("The credit has been preliminarily accepted"), 31 );
		$this->assertEquals( Order::paymentStatusNumber("This is a dummy string"), null );
    }

    public function testOrderStatusName()
    {
    	$this->assertEquals( Order::orderStatusName(6), "Partially delivered" );
		$this->assertEquals( Order::orderStatusName(10), null );
    }

    public function testOrderStatusNumber()
    {
    	$this->assertEquals( Order::orderStatusNumber("Ready for delivery"), 5 );
		$this->assertEquals( Order::orderStatusNumber("This is a dummy string"), null );
    }
}