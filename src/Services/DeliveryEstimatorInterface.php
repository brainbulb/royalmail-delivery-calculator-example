<?php

namespace App\Services;

interface DeliveryEstimatorInterface
{
	public function getDeliveryDate($orderDate, $location);
}
