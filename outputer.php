<?php

require_once 'globalVariables.php'; 
$id = $_GET['id'];
$filename = $localFolder . $id . '.xlf';
$data = file_get_contents($filename);

header ("Content-Type:text/xml");
require_once 'HTTP/Request2.php'; //uses PEAR

$request = new HTTP_Request2($locconnect.'send_output.php');

$request->setMethod(HTTP_Request2::METHOD_POST)
    ->addPostParameter('id', $id)
    ->addPostParameter('com', 'WFR')
	->addPostParameter('data', $data);
		
try {
    $response = $request->send();
    if (200 == $response->getStatus()) {
        //echo $response->getBody();
    } else {
        echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
             $response->getReasonPhrase();
    }
} catch (HTTP_Request2_Exception $e) {
    echo 'Error: ' . $e->getMessage();
}	

   
$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/statusUpdater.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'complete');         // set status id here
    
$request->send();
print "Job $id successfully processed. <br />";
unlink($filename);
?>

