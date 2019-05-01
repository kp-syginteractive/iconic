<?php

namespace SYG\Iconic;

use SYG\Iconic\ApiClient;
use SYG\Iconic\Exceptions\{InvalidRequestDataException, InvalidApiCredentialsException};

class OrderRetriever
{
	private $iconicClient;
	private $orderId;
	public function __construct(ApiClient $iconicClient)
	{
		$this->iconicClient = $iconicClient;
	}

	public function retrieve($orderId)
	{
		$this->orderId = $orderId;
		return (object) array_merge( 
			(array) $this->getOrder(), 
			(array) $this->getOrderItems()
		);
	}

	private function getOrder()
	{
		$response = json_decode( $this->iconicClient->getData('GetOrder', ['OrderId' => $this->orderId]) );
		
		if(isset($response->ErrorResponse) && $response->ErrorResponse->Head->ErrorCode == 9) {
			throw new InvalidApiCredentialsException;
		}

		if(isset($response->ErrorResponse) && $response->ErrorResponse->Head->ErrorCode == 16) {
			throw new InvalidRequestDataException;
		}

		return $response->SuccessResponse->Body->Orders->Order;	
	}

	private function getOrderItems()
	{
		return json_decode( $this->iconicClient->getData('GetOrderItems', ['OrderId' => $this->orderId]) )
			->SuccessResponse->Body;		
	}

}