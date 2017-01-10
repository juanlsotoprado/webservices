<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php

ini_set("soap.wsdl_cache_enabled", "0");

try {
    $params = array('cedula'=>'19733258', 'nacionalidad'=>'V');
	$clientOptions = array('login' => 'usuario', 'password' => '123456');
	$wsdl="http://webservices.mppeuct.gob.ve/gtu/gtu.wsdl";
    $client = new SoapClient($wsdl,$clientOptions);
    $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/gtu/schema.xsd");
    $objeto = $client->consultarCedula(new SoapParam($soapstruct, "message"));
	var_dump($objeto);
} catch (SoapFault $exp) {
    print_r($exp->getMessage());
}
?>
