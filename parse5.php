<?php
 

   require_once 'HTTP\Request2.php'; //uses PEAR
require_once 'globalVariables.php'; 
$jobs = unserialize($_POST['jobs']);

$msg = ""; //A string to contain all the feedback

//print "from Parse <br /><br />";
foreach( $jobs as $id ) 
{

$xmlDoc = new DOMDocument(); 
$xmlDoc->load( $localFolder . $id . '.xlf' ); 

   
// CHECK if there is an external resource for the LMC

$countNodes = $xmlDoc->getElementsByTagName("external-file"); 
if ($countNodes->length==0) { 
  // print "THERE IS NO ATTACHED RESOURCE in job $id <br />";
   $LMCresource = "no";
} Else {
	$LMCresource = "yes";
} 
//END OF CHECK if there is an external resource for the LMC

//CHECK IF THERE IS A WORDCOUNT
/*
$countNodes = $xmlDoc->getElementsByTagName("count"); 
if ($countNodes->length==0) { 
   //print "THERE IS NO COUNT in job $id <br />";
   $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
	$url = $request->getUrl();        
	$url->setQueryVariable('id', $id);         // set job id here
	$url->setQueryVariable('msg', 'There was no count element of any kind ');         // set status id here
    $request->send();
    print "Job $id failed. Check feedback for more information. <br />";
    die;
} 

//END OF CHECK IF THERE IS A WORDCOUNT


//GET THE VALUE OF WORDCOUNT


foreach ( $countNodes as $countNode )  {
    switch ($countNode->getAttribute('unit')) {
	case 'word': 
	$words = $countNode->nodeValue;
	//print "<br />The word count is: $words<br />";
	break 2;
	
	case 'segment': 
	print "<br />In project $id, there is a \"segment\" count. This system only uses word counts<br />";
	 $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'There is a \"segment\" count. This system only uses word counts');         // set status id here
    
$request->send();
	
	break;
	
	case 'character': 
	//print "<br />In project $id, there is a \"character\" count. This system only uses word counts<br />";
	 $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'There is a \"character\" count. This system only uses word counts');         // set status id here
    
$request->send();
	break;
	
	default: 
	//print "In project $id, there is no word count element";
		 $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'There is no word count element');         // set status id here
    $request->send();
$request->send();
	break;
	
}
}
   
*/   
   
//END GET THE VALUE OF WORDCOUNT



//CHECK IF THE FILE HAS GONE THROUGH THE LKR

//




//CHECK IF THERE IS PMUI DATA
$searchNodes = $xmlDoc->getElementsByTagName( "pmui-data" ); 

//If there is no PMUI DATA send feedback using feedbacker
if ($searchNodes->length==0) { 
   //print "THERE IS NO PMUI DATA in job $id <br /> ";
    $request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/feedbacker.php', HTTP_Request2::METHOD_GET);
$url = $request->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here
$url->setQueryVariable('msg', 'THERE IS NO PMUI DATA in job');         // set status id here
$request->send();
print "Job $id failed. Check feedback for more information. <br />";
//print "<br /> THe code to send the feedback HAS BEEN EXECUTED";  

}else{ 
//END CHECK IF THERE IS PMUI DATA


//STORE PMUI DATA IN AN ARRAY
foreach( $searchNodes as $searchNode ) 
{ 
	
	$p_name = $searchNode->getAttribute('pname'); 
	$p_descrip = $searchNode->getAttribute('pdescription'); 
    $p_startdate = $searchNode->getAttribute('startdate'); 
    $p_deadline = $searchNode->getAttribute('deadline'); 
    $p_budget = $searchNode->getAttribute('budget'); 
    $p_qrequirement = $searchNode->getAttribute('qrequirement'); 
	$p_client = $searchNode->getAttribute('client');
    $p_useRating = $searchNode->getAttribute('use-rating'); 
	$p_useMt = $searchNode->getAttribute('use-mt'); 
    
} 




$request2 = new HTTP_Request2($locconnect.'get_resource.php?id='.$id, HTTP_Request2::METHOD_POST);
$url = $request2->getUrl();        
$url->setQueryVariable('id', $id);         // set job id here

$melons2=$request2->send()->getBody();

//print $melons2;
if ((substr($melons2, 1, 5)) == "error"){
	$LMCresource = "NO";
}else{
	$LMCresource = "YES";
}
//END CHECK FOR RESOURCE LMC


// CATEGORY STUFF
if ((strtolower($p_client)) == "symantec"){


$searchNodesCat = $xmlDoc->getElementsByTagName( "file" );

//Store category
foreach( $searchNodesCat as $searchNodeCat ) 
{ 
$p_cat = $searchNodeCat->getAttribute('category');
print "The cathegory is $p_cat <br /> ";
}
}else{}
// END CATEGORY STUFF


$countNodes = $xmlDoc->getElementsByTagName("phase"); 
if ($countNodes->length==0) { 
//IF THIS HAPPENS IT IS BECAUSE THERE IS NO PHASE ELEMENT YET 
//SEND TO ADDER2 THAT ADDS THE WORKFLOW INFO FOR THE PREPARATION PHASE AND STOP THIS SCRIPT
$pmui = array("p_client"=>$p_client);
$pmui = serialize($pmui);
require_once 'HTTP\Request2.php'; // uses Pear
$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/adder2.php');
$request->setMethod(HTTP_Request2::METHOD_POST)
    ->addPostParameter('pmui', $pmui)
    ->addPostParameter('id', $id);
$melons=$request->send()->getBody();
print "<br /><br />  parser done <br /><br /> ";  
print $melons;
print "THE FILE WAS SENT FOR PREPARATION<br /> ";
exit();
//END SEND TO ADDER2 THAT ADDS THE WORKFLOW INFO FOR THE PREPARATION STAGE

} Else {} 
//IF THIS HAPPENS IT IS BECAUSE THERE WERE PHASE ELEMENTS BEFORE, SO THE FILE IS ALREADY 
//PREPARED AND WE NEED TO SEND IT FOR TRANSFORMATION PHASE





//CHECK FOR RESOURCE LMC STUFF


// IF WE GET HERE IT IS BECAUSE THE FILE HAS BEEN PREPARED.

$pmui = array("LMCresource"=>$LMCresource, "p_client"=>$p_client, "p_cat"=>$p_cat, "p_name"=>$p_name, "p_descrip"=>$p_descrip, "p_startdate"=>$p_startdate,"p_deadline"=> $p_deadline, "p_budget"=>$p_budget, "p_qrequirement"=>$p_qrequirement, "p_useRating"=>$p_useRating, "p_useMt"=>$p_useMt);
//  with word count $pmui = array("LMCresource"=>$LMCresource, "p_cat"=>$p_cat, "p_name"=>$p_name, "p_descrip"=>$p_descrip, "words"=>$words, "p_startdate"=>$p_startdate,"p_deadline"=> $p_deadline, "p_budget"=>$p_budget, "p_qrequirement"=>$p_qrequirement, "p_useRating"=>$p_useRating, "p_useMt"=>$p_useMt);
// Original Line $pmui = array("LMCresource"=>$LMCresource, "p_name"=>$p_name, "p_descrip"=>$p_descrip, "words"=>$words, "p_startdate"=>$p_startdate,"p_deadline"=> $p_deadline, "p_budget"=>$p_budget, "p_qrequirement"=>$p_qrequirement, "p_useRating"=>$p_useRating, "p_useMt"=>$p_useMt);
//END STORE PMUI DATA IN AN ARRAY



//send to adder to Add tasks according to metadata

$pmui = serialize($pmui);
require_once 'HTTP\Request2.php'; // uses Pear
$request = new HTTP_Request2('http://'.$_SERVER['HTTP_HOST'].'/adder.php');
$request->setMethod(HTTP_Request2::METHOD_POST)
    ->addPostParameter('pmui', $pmui)
    ->addPostParameter('id', $id);
$melons=$request->send()->getBody();
//print "<br /><br />  parser done <br /><br /> ";  
print $melons;

//if substr($melons, 0 = "There is no resource corresponding to Resource ID:"
  
}
}




?>