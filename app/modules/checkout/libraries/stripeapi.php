<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once('stripe/autoload.php');

/**
 * 
 */
class stripeapi{
	
	public function __construct($stripe_secret_key = null, $stripe_publishable_key = null, $mode = "") {
	    \Stripe\Stripe::setApiKey($stripe_secret_key);
	}

	/**
	 *
	 * Block comment
	 *
	 */
	public function customer_create($data_buyer = ""){
		if (is_array($data_buyer)) {
			$result = \Stripe\Customer::create($data_buyer);
		}
		return $result;
	}

	/**
	 *
	 * Define Payment && Create payment.
	 *
	 */
	public function create_payment($data_charge = ""){
		$result = array();
		if (is_array($data_charge)) {
			try {
			    //retrieve charge details
				$response = \Stripe\Charge::create($data_charge);
				if ($response->paid == 1 && $response->amount_refunded == 0) {
					$result = (object)array(
						"status"      => "success",
						"response"    => $response,
					);
				}else{
					$result = (object)array(
						"status" 		=> "error",
						"response"      => 'There was some wrong with your request',
					);
				}
				return $result;
			} catch(Stripe_CardError $e) {
			  	$error1 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error1,
				);
				return $result;
			} catch (Stripe_InvalidRequestError $e) {
			  	// Invalid parameters were supplied to Stripe's API
			  	$error2 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error2,
				);
				return $result;
			} catch (Stripe_AuthenticationError $e) {
			  	// Authentication with Stripe's API failed
			  	$error3 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error3,
				);
				return $result;
			} catch (Stripe_ApiConnectionError $e) {
			  	// Network communication with Stripe failed
			  	$error4 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error4,
				);
				return $result;
			} catch (Stripe_Error $e) {
			  	// Display a very generic error to the user, and maybe send
			  	// yourself an email
			  	$error5 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error5,
				);
				return $result;
			} catch (Exception $e) {
			  	// Something else happened, completely unrelated to Stripe
			  	$error6 = $e->getMessage();
			  	$result = (object)array(
					"status"      => "error",
					"message"    => $error6,
				);
				return $result;
			}
		}else{
			redirect(cn("add_funds"));
		}
	}


	public function PaymentIntent($data_charge = array(), $data_payment_method = array()){
		$result = array();
		$payment_method = $this->PaymentMethod($data_payment_method);
		if (is_array($data_charge)) {
			try {
				$payment_intent = \Stripe\PaymentIntent::create($data_charge);
		 	}catch (Twocheckout_Error $e) {
				$result = (object)array(
					"status" => "error",
					"message" => "Transaction has been failed",
				);
			}

			try {
				$payment_retrieve = \Stripe\PaymentIntent::retrieve($payment_intent->id);
			}catch (Twocheckout_Error $e) {
				$result = (object)array(
					"status" => "error",
					"message" => "Transaction has been failed",
				);
			}
			try {
				$result = $payment_retrieve->confirm([
				  'payment_method' => $payment_method->id,
				]);
			}catch (Twocheckout_Error $e) {
				$result = (object)array(
					"status" => "error",
					"message" => "Transaction has been failed",
				);
			}
		}else{
			$result = (object)array(
				"status" => "error",
				"message" => "Transaction has been failed",
			);
		}	
		return $result;
	}

	private function PaymentMethod($data_payment_method = ''){
		try {
			$result = \Stripe\PaymentMethod::create($data_payment_method);
		}catch (Twocheckout_Error $e) {
			$result = (object)array(
				"status" => "error",
				"message" => "Transaction has been failed",
			);
		}
		return $result;
	}
}


