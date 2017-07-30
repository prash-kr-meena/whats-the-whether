<?php

require "simple_html_dom.php";
include "./php/string.utility.php";
$error = "";

function showCity()
{
  if($_GET){
    echo $_GET['city']; 
  }
}

function echoAlertDiv($type,$info)
{
  $returnVal = '<div class="alert alert-'.$type.' role="alert">'.$info.'</div>';  
  echo $returnVal;
}

function urlExists($url)
{
  $file_headers = @get_headers($url,1); //needs the ,1!!!

  $exists = true;

  if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
      $exists = false;
  }
  else {
      $exists = true;  
  }


  return $exists;
}

function getCompass($deg){
  //http://stackoverflow.com/questions/2131195/cardinal-direction-algorithm-in-java
  $directions = array("N", "NE", "E", "SE", "S", "SW", "W", "NW", "N");
  $pointIndex = round( ($deg % 360) / 45);
  return $directions[$pointIndex];   
}

function getWeatherFromObject($resultObject){

  // we will always take just the first of > 1 city of that name 
  // is returned, e.g. if we put "london" instead of "london,uk"
  
  if(isset($resultObject->list)){
    $cityWeather = $resultObject->list[0];
  } else {
    $cityWeather = $resultObject;
  }

  $returnValue = "%0%. Temperature %1%&deg;C (max: %2%&deg;C, min: %3%&deg;C). Wind %4% %5%m/s.";

  $params = array(
    ucfirst($cityWeather->weather[0]->description),
    round($cityWeather->main->temp),
    round($cityWeather->main->temp_min),
    round($cityWeather->main->temp_max),
    getCompass($cityWeather->wind->deg),
    $cityWeather->wind->speed
  );

  $returnValue = tokenReplace($returnValue,$params);
  return $returnValue;
}

function getWeatherFromAPI($city){
  //http://openweathermap.org/getWeatherFromAPI
 $returnValue = "";
 global $error;  

  $url ='http://api.openweathermap.org/data/2.5/weather?q=%0%&appid=%1%&units=metric';
  $apikey = '0e3f99a1c99f0a522499b7dc9758ff36';
  
  $url = tokenReplace($url, array(urlencode($city),$apikey));

  //http://stackoverflow.com/questions/15617512/get-json-object-from-url
  ini_set("allow_url_fopen", 1);
  $response = @file_get_contents($url);
  if($response)
  {
    $resultObject = json_decode($response);
    
    if ($resultObject->cod != "200"){
      // set  error
      $error = "This city is not in the index.";
    } else {

      $returnValue = getWeatherFromObject($resultObject);
    }
  }
  else{
    // set  error
    $error = "This city is not in the index.";
  }

  return $returnValue;

}

function getWeather($city){
  $returnValue = "";

  $url = "http://www.weather-forecast.com/locations/".$city."/forecasts/latest";

 $exists = urlExists($url);

  if($exists){
    $html = file_get_html($url);
    if($html)
    {
      $returnValue = $html->find(".forecast-content.summary>.phrase")[0];
    }
  }
  else{
    // set  error
    global $error;  
    $error = "This city is not in the index.";
  }

  return $returnValue;
}

function weatherOrError()
{
  if(isset($_GET['city']))
  {
    $city = $_GET['city'];
    $weather = getWeatherFromAPI($city);
    global $error;  

    if ($weather != ""){
      echoAlertDiv("success", $weather);
    } else //if ($error != "")
    {
      
      echoAlertDiv("danger", $error);
    }
  }
}

?>