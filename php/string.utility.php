<?php
//***** TODO ***
// have all this stuff centrally!!!
//*************

//================================= 
// Functions
//=================================  

function tokenReplace($str, $valueArray)
{
  // just like good old String.Format ... but with % instead of {}
  for($i=0; $i < sizeof($valueArray); $i++){
    $str = str_replace("%".$i."%",$valueArray[$i],$str);
  }

  return $str;
}


function tokenReplace2($str, $kvArray)
{
  // this one expects an array of kev value pairs
  foreach($kvArray as $name => $value){
    $str = str_replace("%".$name."%",$value,$str);
  }

  return $str;
}


?>