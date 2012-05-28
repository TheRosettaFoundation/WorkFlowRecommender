<?php


header ("Content-Type:text/html; charset=utf-8");

#$locconnect = "http://10.100.13.155/locConnect/";
$locconnect = "http://193.1.97.50/locconnect/";
$this_url = "http://loc.csisdmz.ul.ie/wfr/";

require_once 'HTTP/Request2.php'; // uses Pear
require_once 'globalVariables.php'; 
$request = new HTTP_Request2($locconnect.'fetch_job.php?com=WFR');
   $request->setMethod(HTTP_Request2::METHOD_GET);
$jobs=$request->send()->getBody();



$filecontent = $locconnect."fetch_job.php?com=WFR";
$filestring = file_get_contents($filecontent);




if (strpos($filestring, "error") == true){
print "Waiting for a job...";}
else {	
$xmlDoc = new DOMDocument(); 
$xmlDoc->load($filecontent); 

$jobs = $xmlDoc->getElementsByTagName('job');
for ($i = 0; $i < $jobs->length; $i++) {
    $arr[$i]= $jobs->item($i)->nodeValue;
}

$sarr = serialize($arr);






$request = new HTTP_Request2($this_url . '/fileGetterAndSaver.php');
$request->setMethod(HTTP_Request2::METHOD_POST)
    ->addPostParameter('jobs', $sarr);

$melons=$request->send()->getBody();

print $melons;
}




?>
