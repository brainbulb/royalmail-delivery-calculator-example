<?php

namespace App\Services\DeliverySuppliers;

use App\Services\DeliveryEstimatorInterface;

/**
 * To begin with the company will only be using Royal Mail, more suppliers could be provided in the future and delivery times may change.
 * Orders are only shipped Monday - Friday
 * Any order placed between midnight and 4pm will be shipped the same day
 * Any order placed between 4pm and midnight will be shipped the next day
 * Delivery times are as follow
 * UK - 1 business day
 * Europe - 3 business days
 * Rest of the world - 8 business days
 * Assume that the first business day is the day the order is shipped
 */
class RoyalMail implements DeliveryEstimatorInterface
{
	/**
	 * @var array
	 */
	private $deliveryTimes = [
		'UK' => 1, 'Europe' => 3, 'Rest of the World' => 8,
	];
	
	/**
	 * Get an estimated delivery date from an orders date and delivery country
	 *
	 * @param $orderDate
	 * @param $location
	 * @return string
	 * @throws \Exception
	 */
	public function getDeliveryDate($orderDate, $location)
	{
		$datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $orderDate);
		
		$timestamp = $datetime->getTimestamp();
		$time = $datetime->format('His');
		$day = $datetime->format('D');
		
		$shippingDays = $this->getAmountOfShippingDays($day, $time);
		$deliveryDays = $this->getAmountOfDeliveryDays($location);
		
		$totalDaysToDeliver = $shippingDays + $deliveryDays;
		
		return date('l, jS F, Y', strtotime("+{$totalDaysToDeliver} days", $timestamp));
	}
	
	/**
	 * Get the amount of days it will take to ship to the given location
	 *
	 * @param $location
	 * @return int
	 * @throws \Exception
	 */
	public function getAmountOfDeliveryDays($location)
	{
		if ( ! array_key_exists($location, $this->deliveryTimes) ) {
		    throw new \Exception('Invalid delivery location');
		}
		
		return $this->deliveryTimes[$location];
	}
	
	/**
	 * Get the amount of days it takes to ship an order for the given day and time
	 *
	 * @param $day
	 * @param $time
	 * @return int
	 */
	public function getAmountOfShippingDays($day, $time)
	{
		$day = strtolower($day);
		$time = str_replace(':', '', $time);
		
		// orders made Friday after 4pm ship Monday, 3 days later
		if ($this->dayIsFriAndTimeIsAfter4pm($day, $time)) {
		    return 3;
		}
		
		// orders made Saturday ship Monday, 2 days later
		if ($this->dayIsSat($day)) {
		    return 2;
		}
		
		// orders made Sunday and orders made Mon-Thu after 4pm ship the next day, 1 day later
		if ($this->dayIsSun($day) || $this->dayIsMonToThuAndTimeIsAfter4pm($day, $time)) {
		    return 1;
		}
		
		// default tyo same day shipping, as nothing else matches
		return 0;
	}
	
	/**
	 * Checks if the given day is a Sunday
	 *
	 * @param $day
	 * @return bool
	 */
	private function dayIsSun($day)
	{
		return ($day == 'sun');
	}
	
	/**
	 * Checks if the given day is a Saturday
	 *
	 * @param $day
	 * @return bool
	 */
	private function dayIsSat($day)
	{
		return ($day == 'sat');
	}
	
	/**
	 * Checks if the given day is a Friday and time is 4pm or later
	 *
	 * @param $day
	 * @param $time
	 * @return bool
	 */
	private function dayIsFriAndTimeIsAfter4pm($day, $time)
	{
		return ($day == 'fri' && $time >= 160000);
	}
	
	/**
	 * Checks if the given day is Monday-Thursday and time is 4pm or later
	 *
	 * @param $day
	 * @param $time
	 * @return bool
	 */
	private function dayIsMonToThuAndTimeIsAfter4pm($day, $time)
	{
		return (in_array($day, ['mon', 'tue', 'wed', 'thu']) && $time >= 160000);
	}
}
