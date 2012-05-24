<?php
require_once 'globalVariables.php'; 
require_once 'HTTP/Request2.php'; // uses Pear
$request = new HTTP_Request2($locconnect.'send_feedback.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();
$url->setQueryVariable('com', 'WFR');         // set your component name here
$url->setQueryVariable('id', $_GET['id']);         // set job id here
$url->setQueryVariable('msg', $_GET['msg']);         // set your component’s feedback here
$response=$request->send()->getBody();

?>
