<?php
require_once 'globalVariables.php'; 
require_once 'HTTP\Request2.php'; //uses PEAR
$pmui = unserialize($_POST['pmui']);
$id = $_POST['id'];

//var_dump($pmui);

//print "<br /><br />";

$atname = "tool-id";
$tname = "translation";

$atorder = "order";
$torder = "1";
$placeInArr = "1";



//PREPARATION STAGE, check client, if client Symantec, use DDC, otherwise move on. If use 
// DDC call LKR and then WRF again.

// Do we include the DDC task?

// IF client is not symantec, do nothing, else add DDC to the array.
if (strtolower($pmui['p_client']) != "symantec"){
		unset($pmui['p_client']);
		//print "Deleted CATEGORY for {$id} </br>"; // This removes the element from the array PROBLEM	
} else {
	$pmui['p_client'] = "DDC";
	$pmui[$placeInArr] = $pmui['p_client'];
	$placeInArr++;
	unset($pmui['p_client']);
}
//END OF Do we include the DDC task?




// We always include the LKR 

//	$pmui[$placeInArr] = "LKR";
//	$placeInArr++;


// END We always include the LKR




// We always include the WFR for TRANSFORMATION 

	$pmui[$placeInArr] = "WFR";
	$placeInArr++;

// END We always include the WFR for TRANSFORMATION 


ksort($pmui);


	
//GET THE DOCUMENT LOAD IT PREPARE TO WRITE ON IT

 $doc = new DOMDocument();
 $doc->load( $localFolder .$id .'.xlf' );	
 $xpath = new DOMXPath($doc);

 
 $form = "form";
 $type = "text";
 $tasklistf = "";
 
$headers = $doc->getElementsByTagName( 'header' );	
	$text= "/n";
foreach( $headers as $header )
{


$reference = $doc->createElement("reference");
    $header->appendChild($reference);
    
    $ifile = $doc->createElement("internal-file");
    $reference->appendChild($ifile);
    $ifile->setAttribute($form, $type);
    
    $wf = $doc->createElement("workflow");
    $ifile->appendChild($wf);
    
    foreach ($pmui as $value) {
    $ltask = $doc->createElement("task");
    $wf->appendChild($ltask);
    $ifile->setAttribute($form, $type);
   
    
    $ltask->setAttribute($atname, $value);
    $ltask->setAttribute($atorder, $torder);
    //Create check to see if a comma or a full stop is needed. Coom
    // becomes . if the order number is equal to the lengh of the 
    //array pmui (count($pmui)) otherwise, it is comma
    $comm = ($torder == (count($pmui))) ? '.' : ', ';
    $torder++;
    $tasklist = $value;
    $tasklistf = $tasklistf . $tasklist . $comm;
     }
    

}
//PHASE STUFF FROM NAOTO


$phasegroups = $doc->getElementsByTagName('phase-group');	
$toolid='WFR';
$phasename='WF-Recommendation-Preparation';
	
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

$msg = "The workflow recommender has added ". ($torder - 1) . " task(s): " . $tasklistf  ;
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


