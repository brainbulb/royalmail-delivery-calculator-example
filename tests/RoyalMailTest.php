<?php

namespace App\Tests\Services\DeliverySuppliers;

use App\Services\DeliverySuppliers\RoyalMail;
use PHPUnit\Framework\TestCase;

class RoyalMailTest extends TestCase
{
	private $service = NULL;
	private $days = [
		'mon' => '2019-08-12',
		'tue' => '2019-08-13',
		'wed' => '2019-08-14',
		'thu' => '2019-08-15',
		'fri' => '2019-08-16',
		'sat' => '2019-08-17',
		'sun' => '2019-08-18',
	];
	
	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		
		$this->service = new RoyalMail();
	}
	
	
	/**
	 * Check the delivery days
	 */
	public function testDeliveryDays()
	{
		$this->assertEquals(1, $this->service->getAmountOfDeliveryDays('UK'));
		$this->assertEquals(3, $this->service->getAmountOfDeliveryDays('Europe'));
		$this->assertEquals(8, $this->service->getAmountOfDeliveryDays('Rest of the World'));
	}
	
	
	/**
	 * Check the shipping days logic returns the correct amount of days it takes to ship an order
	 */
	public function testShippingDays()
	{
		// orders made Friday after 4pm ship 3 days later on Monday
		$this->assertEquals(3, $this->service->getAmountOfShippingDays('fri', '17:00:00'));
		
		// orders made Saturday ship 2 days later on Monday
		$this->assertEquals(2, $this->service->getAmountOfShippingDays('sat', '00:00:00'));
		$this->assertEquals(2, $this->service->getAmountOfShippingDays('sat', '17:00:00'));
		
		// orders made Sunday ship 1 day later on Monday
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('sun', '00:00:00'));
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('sun', '17:00:00'));
		
		// orders made Mon-Thu after 4pm ship the next day
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('mon', '17:00:00'));
		
		// orders made Mon-Fri before 4pm ship the same day
		$this->assertEquals(0, $this->service->getAmountOfShippingDays('mon', '00:00:00'));
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('mon', '17:00:00'));
		$this->assertEquals(0, $this->service->getAmountOfShippingDays('tue', '00:00:00'));
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('tue', '17:00:00'));
		$this->assertEquals(0, $this->service->getAmountOfShippingDays('wed', '00:00:00'));
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('wed', '17:00:00'));
		$this->assertEquals(0, $this->service->getAmountOfShippingDays('thu', '00:00:00'));
		$this->assertEquals(1, $this->service->getAmountOfShippingDays('thu', '17:00:00'));
		$this->assertEquals(0, $this->service->getAmountOfShippingDays('fri', '00:00:00'));
	}
}
