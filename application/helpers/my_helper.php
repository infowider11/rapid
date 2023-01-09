<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('getDistance')) {
	function getDistance($lat1, $lng1, $lat2, $lng2) {
		
		
		// Google API key
    //$apiKey = 'AIzaSyB4GTdudcf_UQnKPmPW4QKt82kel3Fhd6c';
    $apiKey = GOOGLE_API;
    
    // Change address format
	
		$link = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$lng1."&destinations=".$lat2.",".$lng2."&sensor=false&key=".$apiKey."&mode=driving&departure_time=now&units=imperial"; 
		
		$api = file_get_contents($link);
		
		$data = json_decode($api);
		//$result['all'] = $data;
		$status = $data->status;
		
		if($status=='OK'){
			
			$result['status'] = 1;
			
			$distance = $data->rows[0]->elements[0]->distance->value;
			
			$distance = $distance/1000;
			
			$distance = number_format($distance,2);
			
			//$result['data'] = $data;
			$result['distance'] = $distance;
			//$result['duration_in_traffic'] = $data->rows[0]->elements[0]->duration_in_traffic;
			//$result['duration'] = $data->rows[0]->elements[0]->duration;
		
		} else {
			$result['status'] = 0;	
		}
		
		return $result;
	}
}

if (!function_exists('short_number')) {
	function short_number($n) {
		if ($n < 1000) {
			$n_format = number_format($n);
		} else if ($n < 1000000) {
			$n_format = number_format($n / 1000, 1) . 'K';
			// Anything less than a million
		} else if ($n < 1000000000) {
			// Anything less than a billion
			$n_format = number_format($n / 1000000, 1) . 'M';
		} else {
			// At least a billion
			$n_format = number_format($n / 1000000000, 1) . 'B';
		}
		
		return $n_format;
	}
}

if (!function_exists('time_ago')) {
	function time_ago($timestamp){  
		$time_ago = strtotime($timestamp);  
		$current_time = time();  
		$time_difference = $current_time - $time_ago;  
		$seconds = $time_difference;  
		$minutes = round($seconds / 60) ;// value 60 is seconds
		$hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
		$days    = round($seconds / 86400); //86400 = 24 * 60 * 60;
		$weeks   = round($seconds / 604800);// 7*24*60*60;
		$months  = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
		$years   = round($seconds / 31553280);//(365+365+365+365+366)/5 * 24 * 60 * 60  
		if($seconds <= 60) { 
		
			return "Just Now";
			
		} else if($minutes <=60) { 
		
			if($minutes==1) {  
				return "one minute ago";  
			} else {  
				return "$minutes minutes ago";  
			}  
			
		} else if($hours <=24) {  
			
			if($hours==1) {  
				return "an hour ago";  
			} else  {  
				return "$hours hrs ago";  
			}  
			
		} else if($days <= 7) {  
			if($days==1) {  
				return "yesterday";  
			} else  {  
				return "$days days ago";  
			}  
		} else if($weeks <= 4.3) {  
			
			if($weeks==1) {  
				return "a week ago";  
			}  else  {  
				return "$weeks weeks ago";  
			}  
		}  else if($months <=12) { 
		
			if($months==1) {  
				return "a month ago";  
			} else {  
				return "$months months ago";  
			}  
		}  else {  
			if($years==1) {  
				return "one year ago";  
			} else  {  
				return "$years years ago";  
			}  
		}  
	}
}
