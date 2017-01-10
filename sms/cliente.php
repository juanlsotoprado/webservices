<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php

ini_set("soap.wsdl_cache_enabled", "0");

/*$string = "04143894912";
if (!preg_match('/^[0-9]{11}/', $string)) {
    echo "NO VALIDO";
}
exit;*/
try {
	$options = array();

	#(numero,mensaje,origen)
	$params	 = array('numero'=>'04143894912','mensaje' => 'Mensaje de Prueba Validar numeros 5','origen' => 'Prueba-ws', 'uid' => 'rserrano');
	$client = new SoapClient("http://webservices.mppeuct.gob.ve/sms/sms.wsdl",$options);
	$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/sms/schema.xsd");
	$result = $client->enviarSms(new SoapParam($soapstruct, "message"));
	
	//echo $client->__getLastResponse();

  	print($result);

} catch (SoapFault $e) {
	print_r($e->getMessage());
}
?>
