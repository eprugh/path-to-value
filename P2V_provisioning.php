<?php 

require('exacttarget_soap_client.php');
require('exacttarget_object_definition.php');

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

	/* create the data extension */
	$request = new ExactTarget_UpdateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	return $results->Results;
}

$client = call_api();

// this script should be run on ANY ExactTarget account requiring implementation
if($_POST['_SubscribersFolder'] == "yes"){
	
	// create the Data Extension folder "_Subscribers"
	$parent_id = get_folder("DataExtension", "Data Extensions");
	$new_folder = create_folder($parent_id, "dataextension", "_Subscribers", "_Subscribers");
	if($new_folder->StatusCode == "OK"){ 
		echo "<p>_Subscribers folder created successfully.</p>";
	} else {
		echo "<p>There was an error creating the _Subscribers folder: " . $new_folder->StatusMessage . "</p>";
	}
	
}

if($_POST['EmailFolder'] == "yes"){
		
	// create the email folder "_Path to Value"
	$parent_id = get_folder("Email", "my emails");
	$new_folder = create_folder($parent_id, "email", "_Path to Value", "_Path to Value");
	if($new_folder->StatusCode == "OK"){ 
		echo "<p>Email folder _Path to Value created successfully.</p>";
	} else {
		echo "<p>There was an error creating the email folder _Path to Value: " . $new_folder->StatusMessage . "</p>";
	}
	
}
	
if($_POST['Common_Subscriber_View_DE'] == "yes"){
	
	/****************************************** 
	******************************************
	Create the Common_Subscriber_View DE
	******************************************
	*****************************************/	
	$parent_id = get_folder("DataExtension", "_Subscribers");
	$csv = create_de("Common_Subscriber_View", "common_subscriber_view", "DO NOT DELETE. Used for segmentation of your subscribers.", $parent_id);
	$csv->IsSendable = "True";
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Email_Address";
	$field1->FieldType = "EmailAddress";
	$field1->IsRequired = "True"; // default is false, required to be true for primary key
	$field1->IsPrimaryKey = "True";
	
	/* set it so that the data extension fields EmailAddress maps to attribute Subscriber Key */
	$csv->SendableDataExtensionField = new ExactTarget_DataExtensionField();
	$csv->SendableDataExtensionField->Name = "SubscriberKey";
	$csv->SendableSubscriberField  = new ExactTarget_Attribute();
	$csv->SendableSubscriberField ->Name = "Subscriber Key"; /* This could be Email Address or Subscriber ID */
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "SubscriberKey";
	$field2->FieldType = "Text";
	$field2->MaxLength = "100";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "Number_of_Emails_Sent";
	$field3->FieldType = "Number";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "Number_of_Emails_Opened";
	$field4->FieldType = "Number";
	
	$field5 = new ExactTarget_DataExtensionField();
	$field5->Name = "Number_of_Emails_Clicked";
	$field5->FieldType = "Number";
	
	$field6 = new ExactTarget_DataExtensionField();
	$field6->Name = "Open_to_Sent";
	$field6->FieldType = "Number";
	
	$field7 = new ExactTarget_DataExtensionField();
	$field7->Name = "Click_to_Sent";
	$field7->FieldType = "Number";
	
	$field8 = new ExactTarget_DataExtensionField();
	$field8->Name = "Last_Send_Date";
	$field8->FieldType = "Date";
	
	$field9 = new ExactTarget_DataExtensionField();
	$field9->Name = "Last_Open_Date";
	$field9->FieldType = "Date";
	
	$field10 = new ExactTarget_DataExtensionField();
	$field10->Name = "Last_Click_Date";
	$field10->FieldType = "Date";
	
	$field11 = new ExactTarget_DataExtensionField();
	$field11->Name = "Number_Sent_1wk";
	$field11->FieldType = "Number";
	
	$field12 = new ExactTarget_DataExtensionField();
	$field12->Name = "Number_Sent_30days";
	$field12->FieldType = "Number";

	/* add the fields to the data extension object */
	$csv->Fields[] = $field1;
	$csv->Fields[] = $field2;
	$csv->Fields[] = $field3;
	$csv->Fields[] = $field4;
	$csv->Fields[] = $field5;
	$csv->Fields[] = $field6;
	$csv->Fields[] = $field7;
	$csv->Fields[] = $field8;
	$csv->Fields[] = $field9;
	$csv->Fields[] = $field10;
	$csv->Fields[] = $field11;
	$csv->Fields[] = $field12;
	
	$object = new SoapVar($csv, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
}

if($_POST['AllSubscribersDE'] == "yes"){
	
	
	/****************************************** 
	******************************************
	Create Your_Subscribers with EmailAddress (primary key by default), SubscriberKey, etc.
	******************************************
	*****************************************/
	$parent_id = get_folder("DataExtension", "_Subscribers");
	$as = create_de("All_Subscribers", "all_subscribers", "DO NOT DELETE. Core subscribers and attributes.", $parent_id);
	$as->IsSendable = "True";
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Email_Address";
	$field1->FieldType = "EmailAddress";
	$field1->IsRequired = "True"; // default is false, required to be true for primary key
	
	/* set it so that the data extension fields EmailAddress maps to attribute Subscriber Key */
	$as->SendableDataExtensionField = new ExactTarget_DataExtensionField();
	$as->SendableDataExtensionField->Name = "SubscriberKey";
	$as->SendableSubscriberField  = new ExactTarget_Attribute();
	$as->SendableSubscriberField ->Name = "Subscriber Key"; /* This could be Email Address or Subscriber ID */
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "SubscriberKey";
	$field2->FieldType = "Text";
	$field2->MaxLength = "100";
	$field2->IsPrimaryKey = "True";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "First_Name";
	$field3->FieldType = "Text";
	$field3->MaxLength = "255";

	/* add the fields to the data extension object */
	$as->Fields[] = $field1;
	$as->Fields[] = $field2;
	$as->Fields[] = $field3;
	
	$object = new SoapVar($as, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
}

if($_POST['AllProgramMembersDE'] == "yes"){	
	
	/****************************************** 
	******************************************
	Create the All_Program_Members DE
	******************************************
	*****************************************/	
	$parent_id = get_folder("DataExtension", "_Subscribers");
	$apm = create_de("All_Program_Members", "all_program_members", "DO NOT DELETE. All subscribers who are a part of a Path to Value program.", $parent_id);
	$apm->IsSendable = "True";
	/* set it so that the data extension fields EmailAddress maps to attribute Subscriber Key */
	$apm->SendableDataExtensionField = new ExactTarget_DataExtensionField();
	$apm->SendableDataExtensionField->Name = "SubscriberKey";
	$apm->SendableSubscriberField  = new ExactTarget_Attribute();
	$apm->SendableSubscriberField ->Name = "Subscriber Key"; /* This could be Email Address or Subscriber ID */
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Email_Address";
	$field1->FieldType = "EmailAddress";
	$field1->IsRequired = "True"; // default is false, required to be true for primary key
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "SubscriberKey";
	$field2->FieldType = "Text";
	$field2->MaxLength = "100";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "Program_Name";
	$field3->FieldType = "Text";
	$field3->MaxLength = "255";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "Last_Email_Received";
	$field4->FieldType = "Text";
	$field4->MaxLength = "255";
	
	$field5 = new ExactTarget_DataExtensionField();
	$field5->Name = "Last_Program_Email_Send_Date";
	$field5->FieldType = "Date";
	
	$field6 = new ExactTarget_DataExtensionField();
	$field6->Name = "Last_Program_Click";
	$field6->FieldType = "Date";

	/* add the fields to the data extension object */
	$apm->Fields[] = $field1;
	$apm->Fields[] = $field2;
	$apm->Fields[] = $field3;
	$apm->Fields[] = $field4;
	$apm->Fields[] = $field5;
	$apm->Fields[] = $field6;
	
	$object = new SoapVar($apm, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
}
if($_POST['ListMembersDE'] == "yes"){
	
	/****************************************** 
	******************************************
	Create the List_Members DE
	******************************************
	*****************************************/	
	$parent_id = get_folder("DataExtension", "_Subscribers");
	$lm = create_de("List_Members", "list_members_subscriber", "DO NOT DELETE. Provides all the lists a subscriber is on.", $parent_id);
	$lm->IsSendable = "True";
	/* set it so that the data extension fields EmailAddress maps to attribute Subscriber Key */
	$lm->SendableDataExtensionField = new ExactTarget_DataExtensionField();
	$lm->SendableDataExtensionField->Name = "SubscriberKey";
	$lm->SendableSubscriberField  = new ExactTarget_Attribute();
	$lm->SendableSubscriberField ->Name = "Subscriber Key"; /* This could be Email Address or Subscriber ID */
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Email_Address";
	$field1->FieldType = "EmailAddress";
	$field1->IsRequired = "True"; // default is false, required to be true for primary key
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "SubscriberKey";
	$field2->FieldType = "Text";
	$field2->MaxLength = "100";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "List_Name";
	$field3->FieldType = "Text";
	$field3->MaxLength = "255";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "List_ID";
	$field4->FieldType = "Number";
	
	$field5 = new ExactTarget_DataExtensionField();
	$field5->Name = "Date_Added";
	$field5->FieldType = "Date";
	
	$field6 = new ExactTarget_DataExtensionField();
	$field6->Name = "Date_Unsubscribed";
	$field6->FieldType = "Date";

	/* add the fields to the data extension object */
	$lm->Fields[] = $field1;
	$lm->Fields[] = $field2;
	$lm->Fields[] = $field3;
	$lm->Fields[] = $field4;
	$lm->Fields[] = $field5;
	$lm->Fields[] = $field6;
	
	$object = new SoapVar($lm, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	/***************************************
	
	Additional steps that need to happen...
	- Create query that pulls Program_Members:
		
	
	
	**************************************/
	
}


// this is the Path to Value Welcome Program
if($_POST['Welcome'] == 'yes'){	
	// create the email folder "Welcome Program"
	
	// create the Data Filters 1, 2, and 3 based upon X days, X+Y days, and Z days
	
}

if($_POST['Commerce'] == 'yes'){
	// create the Data Extension folder "_Customers"
	
	// update the Common_Subscriber_view DE with the customer-specific fields
	
	
}

?>