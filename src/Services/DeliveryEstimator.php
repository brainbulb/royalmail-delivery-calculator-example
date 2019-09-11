<?php

namespace App\Services;

class DeliveryEstimator
{
	/**
	 * @var DeliveryEstimatorInterface
	 */
	private $service;
	
	/**
	 * @param DeliveryEstimatorInterface $service
	 */
	public function __construct(DeliveryEstimatorInterface $service)
	{
		$this->service = $service;
	}
	
	/**
	 * Get an estimate delivery date for the given date and location
	 *
	 * @param string $orderDate
	 * @param string $location
	 * @return string
	 */
	public function getDeliveryDate($orderDate, $location)
	{
		return $this->service->getDeliveryDate($orderDate, $location);
	}
}
