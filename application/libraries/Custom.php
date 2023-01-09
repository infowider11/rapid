<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of Siteconfig
 *
 * @author https://roytuts.com
 */
class Custom {
    protected $CI;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct()
    {
            // Assign the CodeIgniter super-object
            $this->CI =& get_instance();
          $timeZone=   $this->getTimeZoneByIP();
          date_default_timezone_set($timeZone);
    }

    function timeDiffrence($date1,$date2,$formate='') {
   // Declare and define two dates 
 
$date1 = strtotime($date1);  
$date2 = strtotime($date2);  
  
// Formulate the Difference between two dates 
$diff = abs($date2 - $date1);  
  
  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
  
  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
  
  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
  
  
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
  
  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
  
  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));  

// Print the result 
//$result= $years.$months.$days.$hours.$minutes.$seconds; 
$daysstr = $days.' Days ';
if($days<=0){
$daysstr = ' ';

}

$hourstr = $hours.' Hours ';

if($hours<=0){
$hourstr = ' ';

}

$result=$daysstr.$hourstr.$minutes.' Minutes'; 
//$result=$daysstr.$hours.' Hours '.$minutes.' Minutes '; 

return $result;
}

function timeDiffrenceProfile($date1,$date2,$formate='') {
   // Declare and define two dates 
 
$date1 = strtotime($date1);  
$date2 = strtotime($date2);  
  
// Formulate the Difference between two dates 
$diff = abs($date2 - $date1);  
  
  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
  
  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
  
  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
  
  
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
  
  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
  
  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));  

// Print the result 
//$result= $years.$months.$days.$hours.$minutes.$seconds; 
$daysstr = $days.' Days ';
if($days<=0){
$daysstr = ' ';

}
$hourstr = $hours.' Hours ';

if($hours<=0){
$hourstr = ' ';

}

$result=$daysstr.$hourstr.$minutes.' Minutes ago'; 

return $result;
}


function timeDiffrenceProfile1($date1,$date2,$formate='') {
   // Declare and define two dates 
 
$date1 = strtotime($date1);  
$date2 = strtotime($date2);  
  
// Formulate the Difference between two dates 
$diff = abs($date2 - $date1);  
  
  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
  
  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
  
  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
  
  
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
  
  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
  
  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));  

// Print the result 
//$result= $years.$months.$days.$hours.$minutes.$seconds; 

$hourstr = $hours.' Hours ';

if($hours<=0){
$hourstr = ' ';
}

$result=$daysstr.$hourstr.$minutes.' Minutes ago'; 

$daysstr = $days.' Days ';
if($days<=0)
{
$daysstr = ' ';
$result=$daysstr.$hourstr.$minutes.' Minutes ago'; 
}


$daysstr = $minutes.' Minutes ';
if($minutes<=1)
{
$daysstr = ' ';
$result=$daysstr.$hourstr.$minutes.' Minute ago'; 
}



// $minutesstr = $minutes.' Minutes ';
// if($minutes == 1)
// {
// $minutesstr = ' ';
// $result=$daysstr.$hourstr.$minutesstr; 
// }


elseif($days==1){
$daysstr = $days.' Day ';
$result=$daysstr.' ago'; 
}
elseif($minutes==1)
{
  $daysstr = $minutes.' Minutes ';
  $result=$daysstr.' ago'; 
}

else{

  $result=$daysstr.' ago'; 
}

return $result;

}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))  
     //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
    //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getTimeZoneByIP()
{

 $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$this->getRealIpAddr());
 return $xml->geoplugin_timezone ;
}

function getCurrencySymbolByIP()
{

 $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$this->getRealIpAddr());
//echo $xml->geoplugin_countryName ;
return $xml->geoplugin_currencyCode ;


// echo "<pre>";
// foreach ($xml as $key => $value)
// {
//     echo $key , "= " , $value ,  " \n" ;
// }
// echo "</pre>";


}


function getCurrencyByIP(){


 $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$this->getRealIpAddr());
//echo $xml->geoplugin_countryName ;
 return $xml->geoplugin_currencyCode[0];


// echo "<pre>";
// foreach ($xml as $key => $value)
// {
//     echo $key , "= " , $value ,  " \n" ;
// }
// echo "</pre>";

// return $xml->geoplugin_currencyCode[0] ;

}


 function convertCurrency($amount, $from, $to)  
 {  
      $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";  
      $data = file_get_contents($url);  
      preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);  
      return $converted[1];  
 }  


function currencyConverter($from, $to, $amount) {
 
  $url = "https://api.exchangerate-api.com/v4/latest/".$to;
  $ch = curl_init();
  curl_setopt($ch,CURLOPT_URL,$url);
  //curl_setopt($ch,CURLOPT_GET, 1);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  $response_object =  (array) json_decode($response);
//print_r($response_object['rates']->$to);
return $response_object['rates']->$from*$amount;
} 

public function convert_time_zone($timeZone,$timezoneCurrent,$session_start,$format){

    $from = new DateTimeZone($timeZone);
  // create timeZone object , with totimeZone
    $to = new DateTimeZone($timezoneCurrent);
    // read give time into ,fromtimeZone
    $orgTime = new DateTime($session_start, $from);
 // fromte input date to ISO 8601 date (added in PHP 5). the create new date time object
    $toTime = new DateTime($orgTime->format("c"));
 // set target time zone to $toTme ojbect.
     $toTime->setTimezone($to);
 // return reuslt.
 return $toTime->format($format);
    


              
          
}

}


/* End of file Site_Config.php */
/* Location: ./application/libraries/Site_Config.php */