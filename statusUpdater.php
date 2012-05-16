<?php
require_once 'HTTP\Request2.php'; //uses PEAR
require_once 'globalVariables.php'; 


$request = new HTTP_Request2($locconnect.'set_status.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();
$url->setQueryVariable('com', 'WFR');         // set your component name here
$url->setQueryVariable('id', ($_GET['id']));         // set job id here
$url->setQueryVariable('msg', ($_GET['msg']));  

/*$url->setQueryVariable('id', "1776934cc4");  
$url->setQueryVariable('msg', 'pending');*/
/*
$url->setQueryVariable('id', "3971bc5a94");  // complete
$url->setQueryVariable('msg', 'pending');    */
$request->send();




//32dc767a43
//1accd4673b
//08c4d848dd	
/*Processing job 3971bc5a94. 
Processing job 1776934cc4. 
Processing job 8b1edc57b4.*/
?>