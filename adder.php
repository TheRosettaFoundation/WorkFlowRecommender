<?php
require_once 'globalVariables.php'; 
require_once 'HTTP/Request2.php'; //uses PEAR
$pmui = unserialize($_POST['pmui']);
$id = $_POST['id'];

//var_dump($pmui);

$atname = "tool-id";
$tname = "translation";

$atorder = "order";
$torder = "1";
$placeInArr = "1";

unset($pmui['p_name']);
unset($pmui['p_descrip']);

//PREPARATION STAGE, check client, if client Symantec, use DDC, otherwise move on. If use 
// DDC call WRF again, this time cat info should be in place

// Do we include the DST task?

// IF client is not symantec, do nothing, else add DDC to the array.
if (strtolower($pmui['p_client']) != "symantec"){
		unset($pmui['p_client']);
		unset($pmui['p_cat']);
		//print "Deleted LMC for {$id} </br>"; // This removes the element from the array	
} else {
	unset($pmui['p_client']);
	//We know client is symantec, check if cat is secur. of avail., if it is not, delete cat,
	//else, save cat to add DST later on
	if ((strtolower($pmui['p_cat']) != "security") && (strtolower($pmui['p_cat']) != "availability"))  {
		unset($pmui['p_cat']);
		print "Deleted DST for {$id} </br>"; // This removes the element from the array	
//unset($pmui['p_client']);
	} else{
		$pmui['p_cat'] = "DST";
		//unset($pmui['p_client']);
}
}
//END OF Do we include the DST task?








/*


// Do we include the DST task?

//If p_cat is not security or availabitity, do not include DST
if ((strtolower($pmui['p_cat']) != "security") && (strtolower($pmui['p_cat']) != "availability"))  {
		unset($pmui['p_cat']);
		print "Deleted DST for {$id} </br>"; // This removes the element from the array	
// If we get here it means p_cat is security or availability, check if the client is symantec, if it is, add DST
} elseif (strtolower($pmui['p_client']) == "symantec") {
	$pmui['p_cat'] = "DST";
	$pmui[$placeInArr] = $pmui['p_cat'];
	$placeInArr++;
	unset($pmui['p_client']);
	print "ADDED DST for {$id} </br>";
unset($pmui['p_cat']);
//If we get here in means p_cat is security or availability but the client is not symantec, just delete p_cat from array
} else {
unset($pmui['p_cat']);
unset($pmui['p_client']);
}

//END OF Do we include the DST task?

*/





// Do we include the LMC task?
if (strtolower($pmui['LMCresource']) == "no"){
		unset($pmui['LMCresource']);
		//print "Deleted LMC for {$id} </br>"; // This removes the element from the array	
} else {
	$pmui['LMCresource'] = "LMC";
	$pmui[$placeInArr] = $pmui['LMCresource'];
	$placeInArr++;
unset($pmui['LMCresource']);
}
//END OF Do we include the LMC task?



// Do we include the DST task?
if (array_key_exists('p_cat', $pmui)){
$pmui[$placeInArr] = $pmui['p_cat'];
	$placeInArr++;
	unset($pmui['p_cat']);
	
	}

//END OF Do we include the DST task?




//Do we include MT
if (strtolower($pmui['p_useMt']) == "no"){
		unset($pmui['p_useMt']);
		//print "Deleted MACHINE TRANSLATION for {$id} </br>"; // This removes the element from the array	
} else {
	$pmui['p_useMt'] = "MT";
	$pmui[$placeInArr] = $pmui['p_useMt'];
	$placeInArr++;
unset($pmui['p_useMt']);
}

//END OF Do we include MT



//Do we include the RT task?
if (strtolower($pmui['p_useRating']) == "no"){
		unset($pmui['p_useRating']); // This removes the element from the array	
		//print "Deleted RATING for {$id} </br>";
}else{
	$pmui['p_useRating'] = "RT";
		$pmui[$placeInArr] = $pmui['p_useRating'];
		$placeInArr++;
unset($pmui['p_useRating']);
}
//END OF Do we include the RT task?


//Do we include the GS task?
if (strtolower($pmui['p_qrequirement']) !== "MELON"){
		unset($pmui['p_qrequirement']);
		//print "Deleted POST EDITING for {$id} </br>"; // This removes the element from the array	
}else{
	$pmui['p_qrequirement'] = "GS";
			$pmui[$placeInArr] = $pmui['p_qrequirement'];
			$placeInArr++;
unset($pmui['p_qrequirement']);
}
//END OF Do we include the GS task?	


//we always include the  CMP task?

		$pmui[$placeInArr] = "CMP";
		$placeInArr++;
	
//END OF we always include the CMP task	



//unset($pmui['words']);
unset($pmui['p_startdate']);
unset($pmui['p_deadline']);
unset($pmui['p_budget']);

ksort($pmui);

//print "<br /><br />";

	
//GET THE DOCUMENT LOAD IT PREPARE TO WRITE ON IT

 $doc = new DOMDocument();
 $doc->load( $localFolder .$id .'.xlf' );	
 $xpath = new DOMXPath($doc);

 

 $melons = $doc->getElementsByTagName( 'task' );	
	$text= "/n";
	$torder= $melons->length;
	$torder++;
	//$torder++;
	print "THE TASK COUNT IS {$torder}";
// DELETE DELETE OLD WF 

$domNodeList = $doc->getElementsByTagname('workflow'); 
foreach ( $domNodeList as $domElement ) { 
  //  ...do stuff with $domElement... 
  $domElement->parentNode->removeChild($domElement); 
  }
  
 // END OF DELETE OLD WF 
 
 
 
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


   print "JUST BEFORE THE PHASE GROUP CHECK";
// CHECK if there is a phase-group if not, add it

$countNodes = $doc->getElementsByTagName('phase-group'); 
if ($countNodes->length==0) { 
  // add a phase group inside the header
  print "No phase group";
  $heads= $doc->getElementsByTagName('header');	
   $root_child=$doc->createElement('phase-group');
	$heads ->appendChild($root_child);
} Else {
	//There is a phase-group and nothing hapens
	print "YAY phase group";
}










//Add check, if there is no phase group, must add it.

$phasegroups = $doc->getElementsByTagName('phase-group');	
$toolid='WF';
$phasename='WF-Recommendation';
	
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
 $request = new HTTP_Request2($this_url.'feedbacker.php', HTTP_Request2::METHOD_GET);
 $url = $request->getUrl();        
 $url->setQueryVariable('id', $id);         // set job id here
 $url->setQueryVariable('msg', $msg);         // set status id here
 $request->send();



$request = new HTTP_Request2($this_url.'outputer.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here    
$melons=$request->send()->getBody();

//print "<br /><br />  parser done <br /><br /> ";  
print $melons;

?>


