<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('application/libraries/Twilio/autoload.php');
use Twilio\Rest\Client;

class Send_sms extends CI_Model {
	
	public function send($phones,$msg){
		
		//$sid = 'AC24767d645c70789b874167de28217f5b';
		//$token = 'b95880f5c088a90d42779456a0a7615d'; 
		
		$sid = 'ACb589aae669b55e3e3f00351af0c2e8f4';
		$token = '515b5df46a1c96f1861b804534dc7b9a'; 
		
		$client = new Client($sid, $token);
		
		$status = true;
		//echo $phones;
		$encoded = rawurlencode("$phones");
		
		try {
			$run = $client->messages->create(
				$phones,
				array(
					'from' => '+14693363784',
					//'from' => '+15136571633',
					'body' => $msg
				)
			);
    } catch (Twilio\Exceptions\RestException $e) {
			//echo '<pre>'; print_r($phones); echo '</pre>';
			//echo '<pre>'; print_r($e); echo '</pre>';
			$status = false;
			
    }
		
		return $status;
	}
	
}