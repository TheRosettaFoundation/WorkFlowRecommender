<?php
require_once 'globalVariables.php'; 
require_once 'HTTP\Request2.php'; //uses PEAR
$id = $_POST['id'];

//var_dump($pmui);

//print "<br /><br />";

$atname = "tool-id";
$tname = "translation";

$atorder = "order";
$torder = "1";
$placeInArr = "1";

	
//GET THE DOCUMENT LOAD IT PREPARE TO WRITE ON IT

 $doc = new DOMDocument();
 $doc->load( $localFolder .$id .'.xlf' );	
 $xpath = new DOMXPath($doc);

 

//PHASE STUFF FROM NAOTO


$phasegroups = $doc->getElementsByTagName('phase-group');	
$toolid='WFR';
$phasename='WF-Custom-Workflow-Detected';
	
	foreach($phasegroups as $phasegroup)
	{
	$root_child=$doc->createElement('phase');
	$phasegroup ->appendChild($root_child);
	
	$root_attr1 = $doc->createAttribute('phase-name');
   	$root_child->appendChild($root_attr1);
	$root_text = $doc->createTextNode($phasename);
   	$root_attr1 ->appendChild($root_text); 
   	
   	$root_attr2 = $doc->createAttribute('company-name');
   	$root_child->appendChild($root_attr2);
	$root_text = $doc->createTextNode('SomeCompany');
   	$root_attr2 ->appendChild($root_text); 
   	
   	$root_attr3 = $doc->createAttribute('process-name');
   	$root_child->appendChild($root_attr3);
	$root_text = $doc->createTextNode('WF-Recommender');
   	$root_attr3 ->appendChild($root_text); 
   	
   	$root_attr4 = $doc->createAttribute('contact-name');
   	$root_child->appendChild($root_attr4);
	$root_text = $doc->createTextNode('Aram');
   	$root_attr4 ->appendChild($root_text); 
   	
   	
   	$root_attr5 = $doc->createAttribute('contact-email');
   	$root_child->appendChild($root_attr5);
	$root_text = $doc->createTextNode('aram.morera-mesa@ul.ie');
   	$root_attr5 ->appendChild($root_text); 
   	
    $root_attr6 = $doc->createAttribute('tool-id');
   	$root_child->appendChild($root_attr6);
	$root_text = $doc->createTextNode($toolid);
   	$root_attr6 ->appendChild($root_text); 
   	
   	$date=date("r", time());
	$root_attr7 = $doc->createAttribute('date');
   	$root_child->appendChild($root_attr7);
	$root_text = $doc->createTextNode($date);
   	$root_attr7 ->appendChild($root_text); 
	
   	}
   	
   	
// END OF PHASE STUFF FROM NAOTO

//var_dump($pmui);
$doc->save($localFolder . $id .'.xlf');
//}

//print "yo ";

$msg = "The workflow recommender found a custom workflow and did not add any additional workflow information: " ; 
 $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
 $url = $request->getUrl();        
 $url->setQueryVariable('id', $id);         // set job id here
 $url->setQueryVariable('msg', $msg);         // set status id here
 $request->send();





$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/outputer.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here    
$melons=$request->send()->getBody();

//print "<br /><br />  parser done <br /><br /> ";  
print $melons;

?>


