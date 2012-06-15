<?php

function call_api(){
	$wsdl = 'https://webservice.exacttarget.com/etframework.wsdl';
	
	/* Create the Soap Client */
	$client = new ExactTargetSoapClient($wsdl, array('trace'=>1));

	/* Set username and password here */
	$client->username = $_POST['api_user_name'];
	$client->password = $_POST['api_password'];
	
	return $client;
}

function create_de ($name, $key, $description, $folderID) {
	$client = call_api();
	
	$dataextension = new ExactTarget_DataExtension();
	$dataextension->Name = $name; // name of the data extension
	$dataextension->CustomerKey = $key; // unique identifier for the data extension
	$dataextension->Description = $description;
	$dataextension->CategoryID = $folderID;
	$dataextension->CategoryID = $folderID;
	return $dataextension;
	//$object = new SoapVar($dataextension, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");
	//return $object;
}

function get_folder ($object_type, $key) {
	$client = call_api();
	
	/* Create the retrieve request */
    $request = new ExactTarget_RetrieveRequest();
    $objectType= "DataFolder"; /* We will be retrieving data extensions */
    $request->ObjectType= $objectType;
    $et_objdefClass = new ObjectDefinitionClass();
    
    $request->Properties=$et_objdefClass->getDefintionofObject($objectType)  ;   //Get all properties of object  
    
	/* Create the filter to filter out the data extension we want */
    $filter1 = new ExactTarget_SimpleFilterPart() ;
    $filter1->Property= "Name";
    $filter1->SimpleOperator = ExactTarget_SimpleOperators::equals;
    $filter1->Value = $key;   //key for the name of the folder

	/* Attach the filter to the request */
	$request->Filter = new SoapVar($filter1, SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI"); 

	/* Retrieve */
    $requestMsg = new ExactTarget_RetrieveRequestMsg();
    $requestMsg->RetrieveRequest=$request; 
    $results = $client->Retrieve($requestMsg);   
	
	/* Print out the results */
	if($results->Results!=null){
        $resultObjs=$results->Results;
        return $resultObjs->ID;
    }
}

function create_folder ($parent_id, $content_type, $key, $name) {
	$client = call_api();
	
	$folder = new ExactTarget_DataFolder();
	$folder->Name = $name; // name of the data extension
	$folder->CustomerKey = $key; // unique identifier for the data extension
	$folder->ContentType = $content_type;
	$folder->ParentFolder->ID = $parent_id;
	$folder->Description = $name;
	$folder->IsActive = 1;
	$folder->IsEditable = 1;
	
	$object = new SoapVar($folder, SOAP_ENC_OBJECT, 'DataFolder', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the folder */
	$request = new ExactTarget_UpdateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	return $results->Results;
}


function create_query ($name, $key, $description, $sql, $updatetype, $targetDE, $targetDEKey) {
	$client = call_api();
	
	$query = new ExactTarget_QueryDefinition();
	$query->Name = $name; // name of the data extension
	$query->CustomerKey = $key; // unique identifier for the data extension
	$query->Description = $description;
	$query->TargetUpdateType = $updatetype;
	$query->QueryText = $sql;
	$query->DataExtensionTarget = new ExactTarget_DataExtension();
	$query->DataExtensionTarget->Name = $targetDE;
	$query->DataExtensionTarget->CustomerKey = $targetDEKey;
	
	$object = new SoapVar($folder, SOAP_ENC_OBJECT, 'QueryDefinition', "http://exacttarget.com/wsdl/partnerAPI");
	
	/* create the data extension */
	$request = new ExactTarget_UpdateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	return $results->Results;
}

?>