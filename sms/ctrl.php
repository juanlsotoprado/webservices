<?php

try {
    $options = array();
	$mensaje = $_POST["msj"]."
"."ROBOT MPPEUCT";
    #(numero,mensaje,origen)
    $params     = array('numero'=>$_POST["num"], 'mensaje' => $mensaje, 'origen' => 'Web', 'uid' => 'rserrano');
    $client = new SoapClient("http://webservices.mppeuct.gob.ve/sms/sms.wsdl",$options);
    $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/sms/schema.xsd");
    $result = $client->enviarSms(new SoapParam($soapstruct, "message"));

    print($result);

} catch (SoapFault $e) {
    print_r($e->getMessage());
}
?>
