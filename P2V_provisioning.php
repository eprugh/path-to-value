<?php 

require('exacttarget_soap_client.php');
require('exacttarget_object_definition.php');
require('functions.php');

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
	$field2->IsRequired = "True";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "First_Name";
	$field3->FieldType = "Text";
	$field3->MaxLength = "255";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "Opt_In_Date";
	$field4->FieldType = "Date";

	/* add the fields to the data extension object */
	$as->Fields[] = $field1;
	$as->Fields[] = $field2;
	$as->Fields[] = $field3;
	$as->Fields[] = $field4;
	
	$object = new SoapVar($as, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	print_r($results);
	
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
	// create the email folder "Welcome Program" under the _Path to Value folder
	
	// create the Data Filters 1, 2, and 3 based upon X days, X+Y days, and Z days
	//use All_Subscribers and Opt_In_Date
	
	// create a Data Filter against "All_Program_Members" where "Program_Name = 'Welcome_Program' and Last_Program_Email_Send_Date is after 'today minus 14'"
	
	// create the SendClassification "Welcome_Program"
	
	// create the holder shells for the emails (Blank HTML) for Welcome.E1, Welcome.E2, Welcome.E3
	
	// create the folder within User-initiated sends for Welcome
	
	// create 3x User-initiated Sends that use the Welcome_Program Send Classification and the Welcome.E1, ... emails
	
}

if($_POST['Commerce'] == 'yes'){
	// create the Data Extension folder "_Customers"
	$parent_id = get_folder("DataExtension", "Data Extensions");
	$new_folder = create_folder($parent_id, "dataextension", "_Customers", "_Customers");
	if($new_folder->StatusCode == "OK"){ 
		echo "<p>Email folder _Path to Value created successfully.</p>";
	} else {
		echo "<p>There was an error creating the email folder _Path to Value: " . $new_folder->StatusMessage . "</p>";
	}
	
	// update the Common_Subscriber_view DE with the customer-specific fields
	
	// create the Order_Headers Data Extension with standard fields
	$parent_id = get_folder("DataExtension", "_Customers");
	$oh = create_de("Order_Headers", "order_headers", "DO NOT DELETE. The meta data about a specific order based on Order Number or ID.", $parent_id);
	$oh->IsSendable = "True";
	/* set it so that the data extension fields EmailAddress maps to attribute Subscriber Key */
	$oh->SendableDataExtensionField = new ExactTarget_DataExtensionField();
	/* This was taken from the Order_Headers example, this might need to be changed to Subscriber Key*/
	$oh->SendableDataExtensionField->Name = "Email_Address";
	$oh->SendableSubscriberField  = new ExactTarget_Attribute();
	$oh->SendableSubscriberField ->Name = "Subscriber Key"; /* This could be Email Address or Subscriber ID */
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Customer_ID";
	$field1->FieldType = "Number";
	$field1->IsRequired = "True";
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "Order_Number";
	$field2->FieldType = "Number";
	$field2->IsRequired = "True"; 
	$field2->IsPrimaryKey = "True";
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "Purchase_Date";
	$field3->FieldType = "Date";
	$field3->IsRequired = "True";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "Email_Address";
	$field4->FieldType = "EmailAddress";
	$field4->IsRequired = "True";
	
	$field5 = new ExactTarget_DataExtensionField();
	$field5->Name = "Total_Purchase_Value";
	$field5->FieldType = "Decimal";
	$field5->Precision = "18";
	$field5->Scale = "2";
	$field5->IsRequired = "True";
	
	$field6 = new ExactTarget_DataExtensionField();
	$field6->Name = "Number_of_Items";
	$field6->FieldType = "Number";
	$field6->IsRequired = "True";
	
	$field7 = new ExactTarget_DataExtensionField();
	$field7->Name = "First_Name";
	$field7->FieldType = "Text";
	$field7->MaxLength = "255";
	$field7->IsRequired = "True";
	
	$field8 = new ExactTarget_DataExtensionField();
	$field8->Name = "SubscriberKey";
	$field8->FieldType = "Text";
	$field8->MaxLength = "100";

	/* add the fields to the data extension object */
	$oh->Fields[] = $field1;
	$oh->Fields[] = $field2;
	$oh->Fields[] = $field3;
	$oh->Fields[] = $field4;
	$oh->Fields[] = $field5;
	$oh->Fields[] = $field6;
	$oh->Fields[] = $field7;
	$oh->Fields[] = $field8;
	
	$object = new SoapVar($oh, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	print_r($results);
	
	// create the Order_Details Data Extension with standard fields
	$parent_id = get_folder("DataExtension", "_Customers");
	$od = create_de("Order_Details", "order_details", "DO NOT DELETE. The meta data about a specific order based on Order Number or ID.", $parent_id);
	
	$field1 = new ExactTarget_DataExtensionField();
	$field1->Name = "Customer_ID";
	$field1->FieldType = "Number";
	$field1->IsRequired = "True";
	
	$field2 = new ExactTarget_DataExtensionField();
	$field2->Name = "SKU";
	$field2->FieldType = "Text";
	$field2->MaxLength = "255";
	$field2->IsRequired = "True"; 
	
	$field3 = new ExactTarget_DataExtensionField();
	$field3->Name = "Category";
	$field3->FieldType = "Text";
	$field3->MaxLength = "255";
	$field3->IsRequired = "True";
	
	$field4 = new ExactTarget_DataExtensionField();
	$field4->Name = "Quantity";
	$field4->FieldType = "Number";
	$field4->IsRequired = "True";
	
	$field5 = new ExactTarget_DataExtensionField();
	$field5->Name = "Shipping_Date";
	$field5->FieldType = "Date";
	$field5->IsRequired = "True";
	
	$field6 = new ExactTarget_DataExtensionField();
	$field6->Name = "Order_Number";
	$field6->FieldType = "Number";
	$field6->IsRequired = "True";
	
	$field7 = new ExactTarget_DataExtensionField();
	$field7->Name = "Price";
	$field7->FieldType = "Decimal";
	$field7->Precision = "18";
	$field7->Scale = "2";
	$field7->IsRequired = "True";
	
	$field8 = new ExactTarget_DataExtensionField();
	$field8->Name = "On_Sale";
	$field8->FieldType = "Boolean";
	$field8->DefaultValue = "False";
	$field8->IsRequired = "True";
	
	/* add the fields to the data extension object */
	$od->Fields[] = $field1;
	$od->Fields[] = $field2;
	$od->Fields[] = $field3;
	$od->Fields[] = $field4;
	$od->Fields[] = $field5;
	$od->Fields[] = $field6;
	$od->Fields[] = $field7;
	$od->Fields[] = $field8;
	
	$object = new SoapVar($od, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");

	/* create the data extension */
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	print_r($results);
	// create the Import Activity to Order_Headers with order_headers%%Year%%-%%month%%-%%day%%.csv
	/* Create Import Definition Object */
	$importdef = new ExactTarget_ImportDefinition();
	$importdef->Name = "Order Headers";
	$importdef->CustomerKey = "order_headers";
	$importdef->Description = "DO NOT DELETE. Import for Order Headers Data Extension for Commerce integration.";
   
	//Allow errors during the import (optional)
	$importdef->AllowErrors = true; 

	// Specify the Data Extension (required)
	$de = new ExactTarget_DataExtension();
	$de->CustomerKey = "order_headers";
	$lo = new SoapVar($de, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");
	$importdef->DestinationObject = $lo;
   
	// Specify the File Transfer Location (where is the file coming from?) (required)  
	$ftl= new ExactTarget_FileTransferLocation();
	$ftl->CustomerKey = "ExactTarget Enhanced FTP";
	$importdef->RetrieveFileTransferLocation = $ftl;
   
	// Specify the UpdateType (optional)  
	$importdef->UpdateType  = ExactTarget_ImportDefinitionUpdateType::AddAndUpdate;
   
	// Map fields (required)
	$importdef->FieldMappingType = ExactTarget_ImportDefinitionFieldMappingType::InferFromColumnHeadings;

	// Specify the File naming Specifications
	$importdef->FileSpec = "order_headers%%Year%%-%%month%%-%%day%%.csv";
	
	// Specify the FileType
	$importdef->FileType = ExactTarget_FileType::CSV;

	// Create the Import Definition 
	$object = new SoapVar($importdef, SOAP_ENC_OBJECT, 'ImportDefinition', "http://exacttarget.com/wsdl/partnerAPI");
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);

	// Print out the results
	$results = $client->Create($request);
	print_r($results);
	
	// create the Import Activity to Order_Details with order_details%%Year%%-%%month%%-%%day%%.csv
		/* Create Import Definition Object */
	$importdef = new ExactTarget_ImportDefinition();
	$importdef->Name = "Order Details";
	$importdef->CustomerKey = "order_details";
	$importdef->Description = "DO NOT DELETE. Import for Order Details Data Extension for Commerce integration.";
   
	//Allow errors during the import (optional)
	$importdef->AllowErrors = true; 

	// Specify the Data Extension (required)
	$de = new ExactTarget_DataExtension();
	$de->CustomerKey = "order_details";
	$lo = new SoapVar($de, SOAP_ENC_OBJECT, 'DataExtension', "http://exacttarget.com/wsdl/partnerAPI");
	$importdef->DestinationObject = $lo;
   
	// Specify the File Transfer Location (where is the file coming from?) (required)  
	$ftl= new ExactTarget_FileTransferLocation();
	$ftl->CustomerKey = "ExactTarget Enhanced FTP";
	$importdef->RetrieveFileTransferLocation = $ftl;
   
	// Specify the UpdateType (optional)  
	$importdef->UpdateType  = ExactTarget_ImportDefinitionUpdateType::AddAndUpdate;
   
	// Map fields (required)
	$importdef->FieldMappingType = ExactTarget_ImportDefinitionFieldMappingType::InferFromColumnHeadings;

	// Specify the File naming Specifications
	$importdef->FileSpec = "order_details%%Year%%-%%month%%-%%day%%.csv";
	
	// Specify the FileType
	$importdef->FileType = ExactTarget_FileType::CSV;

	// Create the Import Definition 
	$object = new SoapVar($importdef, SOAP_ENC_OBJECT, 'ImportDefinition', "http://exacttarget.com/wsdl/partnerAPI");
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);

	// Print out the results
	$results = $client->Create($request);
	print_r($results);
	
	// now create the SQL query to aggregate & derive the last purchase data
	/*
	SQL:
	SELECT oh.Customer_ID, oh.Email_Address, oh.Email_Address as SubscriberKey,
	Sum(Total_Purchase_Value) as Total_Spend, Avg(Total_Purchase_Value) as Avg_Spend_per_Purchase,
	Sum(Number_of_Items) / Count(oh.Customer_ID) as Avg_Items_per_Purchase, Max(Purchase_Date) as Last_Purchase_Date,
	Count(oh.Customer_ID) as Number_of_Purchases,
	Round(DateDiff(day, Min(Purchase_Date), Max(Purchase_Date)) / Count(oh.Customer_ID), 0) as Avg_Days_Between_Purchase,
	Is_A_Customer = 1

	from Common_Subscriber_View csv
	Left Outer Join Order_Headers oh
	On oh.Email_Address = csv.Email_Address

	Group By oh.Customer_ID, oh.Email_Address
	*/
	
	// now create a sample filter or two using the Common_Subscriber_View
	// Last_Purchase_Date is after 'today minus 90 days' and Number_of_Purchases > 1 and Total_Spend > 100
	
	
}

if($_POST['DemoAccount'] == 'yes'){
	// create a new "_Archive" folder
	$parent_id = get_folder("email", "my emails");
	$new_folder = create_folder($parent_id, "email", "_Archive", "_Archive");
	
	////////////////////////////////////
	// create a new list called "Seed List"
	////////////////////////////////////
    $list = new ExactTarget_List();
    $list->ListName = "Seed List";
    $object = new SoapVar($list, SOAP_ENC_OBJECT, 'List', "http://exacttarget.com/wsdl/partnerAPI");

	/* Create the Create Request */
    $request = new ExactTarget_CreateRequest();
    $request->Options = NULL;
    $request->Objects = array($object);

	/* Execute the Create Request */
    $results = $client->Create($request);
	
	////////////////////////////////////
	// add a new subscriber based on the test email supplied
	////////////////////////////////////
	$test_email = $_POST['TestEmail'];
	
	$subscriber = new ExactTarget_Subscriber();
	$subscriber->SubscriberKey = $test_email; // optional depending on account configuration
	$subscriber->EmailAddress = $test_email; // required
	//$subscriber->Lists[] = 
	
	/* Create the subscriber */
	$object = new SoapVar($subscriber, SOAP_ENC_OBJECT, 'Subscriber', "http://exacttarget.com/wsdl/partnerAPI");
	$request = new ExactTarget_CreateRequest();
	$request->Options = NULL;
	$request->Objects = array($object);
	$results = $client->Create($request);
	
	////////////////////////////////////
	// add an HTML email in the _Archive folder with AMPscript to add a random number of records to All_Subscribers
	/*****
	AMPscript is...
	
	%%[
	// generate a random number of net new subscribers
	SET @number = Random(100, 1000)
	
	Write(Concat(@number, " subscribers added."))
	
	// loop through all of the random email addresses to input the date
	FOR @i = 1 to RowCount(@number) DO
		UpsertDE("All_Subscribers", 1, "Email_Address", Concat("email", Multiply(Random(100,10000), @number), "@bh.exacttarget.com"), "Opt_In_Date", NOW())
	NEXT @i
	]%%
	
	*/
	////////////////////////////////////
	
}

?>