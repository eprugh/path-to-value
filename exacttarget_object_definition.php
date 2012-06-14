<?php

class ObjectDefinitionClass {

    
    function getDefintionofObject($objectType){
        $wsdl = 'https://webservice.exacttarget.com/etframework.wsdl';
        $lstProps = array();                
        try{                
                $client = new ExactTargetSoapClient($wsdl, array('trace'=>1));  
                $client->username = $_POST['api_user_name'];
                $client->password = $_POST['api_password'];
                
                $request = new ExactTarget_ObjectDefinitionRequest();
                $request->ObjectType= $objectType;
                
                $defRqstMsg = new ExactTarget_DefinitionRequestMsg();
                $defRqstMsg->DescribeRequests[] =  new SoapVar($request, SOAP_ENC_OBJECT, 'ObjectDefinitionRequest', "http://exacttarget.com/wsdl/partnerAPI");

                /* Call the Retrieve method passing the instantiated ExactTarget_RetrieveRequestMsg object */
                $status = $client->Describe($defRqstMsg);
                $results = $status->ObjectDefinition; 
                //print 'ResultCount: '.count($results)."\n";
                //print 'ExtendedProperties: '.count($results->ExtendedProperties->ExtendedProperty)."\n"; 
                //print 'Properties: '.count($results->Properties)."\n"; 
                
                
                if (count($results->Properties) > 0) { 
                 
                $properties = $results->Properties; 
                foreach( $properties as $letter ){
                    if($letter->IsRetrievable==true){
                        $lstProps[] = $letter->Name;
                    }
                    }
                   // print_r ($lstProps);
    
                }
                
           return $lstProps;     
        }catch (SoapFault $e) {
            /* output the resulting SoapFault upon an error */
        var_dump($e);
        }       
    }

}

try{
    
    $test = new ObjectDefinitionClass();
    $test->getDefintionofObject("Account");
    
}catch(Exception $e){
    print $e->__toString();
}
?>
