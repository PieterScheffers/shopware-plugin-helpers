<?php

namespace pisc\Shopware;

use pisc\Arrr\Arrr;
use pisc\upperscore as u;

class Order
{
	public static $orderStatuses = [
		0 => "Open",
		1 => "In process",
		2 => "Completed",
		3 => "Partially completed",
		4 => "Cancelled/rejected",
		5 => "Ready for delivery",
		6 => "Partially delivered",
		7 => "Completely delivered",
		8 => "Clarification required"
	];

	public static $paymentStatuses = [
		 9 => "Partially invoiced",
		10 => "Completely invoiced",
		11 => "Partially paid",
		12 => "Completely paid",
		13 => "1st reminder",
		14 => "2nd reminder",
		15 => "3rd reminder",
		16 => "Encashment",
		17 => "Open",
		18 => "Reserved",
		19 => "Delayed",
		20 => "Re-crediting",
		21 => "Review necessary",
		30 => "No credit approved",
		31 => "The credit has been preliminarily accepted",
		32 => "The credit has been accepted",
		33 => "The payment has been ordered by Hansaetic Bank",
		34 => "A time extension has been registered",
		35 => "The process has been cancelled"
	];
 
	public static function paymentStatusName($int)
	{
		return u\def(static::$paymentStatuses, $int, null);
	}

	public static function paymentStatusNumber($name)
	{
		$name = str_replace( " ", "_", strtolower($name) );

		return Arrr::ar(static::$paymentStatuses)
			->mapIt(function($s) {
				return str_replace( " ", "_", strtolower($s) );
			})
			->filterIt(function($s) use ($name) {
				return $s == $name;
			})
			->keys()
			->first();
	}

	public static function orderStatusName($int)
	{
		return u\def(static::$orderStatuses, $int, null);
	}

	public static function orderStatusNumber($name)
	{
		$name = str_replace( " ", "_", strtolower($name) );

		return Arrr::ar(static::$orderStatuses)
			->mapIt(function($s) {
				return str_replace( " ", "_", strtolower($s) );
			})
			->filterIt(function($s) use ($name) {
				return $s == $name;
			})
			->keys()
			->first();
	}

}