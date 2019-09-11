<?php
namespace App\Controller;

use App\Services\DeliveryEstimator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryEstimationController
{
    public function index()
    {
    	return $this->render('deliveryEstimation.html.twig');
	}
	
    public function getEstimatedDeliveryDate(Request $request)
    {
    	$datetime = $request->query->get('datetime', date('Y-m-d H:i:s'));
    	$location = $request->query->get('location', 'UK');
    	$format = $request->query->get('format', 'html');
    	
    	$supplier = 'App\Services\DeliverySuppliers\RoyalMail';
    	
        $estimator = new DeliveryEstimator(new $supplier());
        
        $orderDate = date('l, jS F, Y H:i:s', strtotime($datetime));
        $deliveryDate = $estimator->getDeliveryDate($datetime, $location);
        
        if ($format == 'json') {
        	return $this->json(compact('orderDate', 'deliveryDate'));
        }
        
        return new Response(
        	"Order Date: $orderDate<br>" .
        	"Delivery Date: $deliveryDate<br>"
        );
    }
}
