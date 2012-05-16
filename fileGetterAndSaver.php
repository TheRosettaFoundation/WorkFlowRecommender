<?php

require_once 'HTTP\Request2.php'; // uses Pear
//get the array of job ids and unserialize it
$jobs = unserialize($_POST['jobs']);
require_once 'globalVariables.php'; 

foreach($jobs as $id) {
    //create a file for each element in the array jobs
	$filename = $localFolder . $id . '.xlf';
    //Open file for writing
	$fp = fopen( $filename,"w");
	//Get the file
    $filecontent =  $locconnect."get_job.php?id=".$id."&com=WFR";
    //and save it as a string "filestring
    $filestring = file_get_contents($filecontent);
    //Get rid of the opening and closing "content" tags
    $filestring = str_replace("<content>", "", $filestring);
    $filestring = str_replace("</content>", "", $filestring);
    // and save it in the file "fp" 
    fwrite( $fp, $filestring); // write in the new value
    fclose( $fp); // close it
	
    
//UPDATE THE STATUS USING statusUpdater
    
    
    
$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/statusUpdater.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'processing');         // set status id here
    
$request->send();

print "Processing job $id. <br />";
    
}

//Call parser

$jobs= serialize($jobs);

$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/parse5.php');
$request->setMethod(HTTP_Request2::METHOD_POST)
    ->addPostParameter('jobs', $jobs);
$melon=$request->send()->getBody();



print $melon;

  
 
    
    
    
?>