<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
ini_set("soap.wsdl_cache_enabled", "0");
//mensajes de exito/error
$FRACASO=0;
$EXITO=1;
$ACCESO_DENEGADO=2;

//parametros de seguridad
$login='usuario';
$password='123456';
	
//parametros que se recibirán POST/GET
$cedula='19733258';
$nacionalidad='V';
$telefono='04167839811';
	
//conexiones a los WSDL
$wsdl_gtu="http://webservices.mppeuct.gob.ve/gtu/gtu.wsdl";
$wsdl_sms="http://webservices.mppeuct.gob.ve/sms/sms.wsdl";
$schema_gtu="http://webservices.mppeuct.gob.ve/gtu/schema.xsd";
$schema_sms="http://webservices.mppeuct.gob.ve/sms/schema.xsd";

//definición unica de parametros de autenticación con el web service
$clientOptions = array('login' => $login, 'password' => $password);

try {
    $params = array('cedula'=>$cedula, 'nacionalidad'=>$nacionalidad, 'connection_timeout' => 30);
    $client = new SoapClient($wsdl_gtu,$clientOptions);
    $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", $schema_gtu);
	$objeto = $client->consultarEstatusCedula(new SoapParam($soapstruct, "message"));
	//print_r($objeto);die();
	if($objeto['error']==0){
		try{
			$params	 = array('numero'=>$telefono,'mensaje' => $objeto['respuesta'],'origen' => 'PRUEBA-MARIO', 'uid' => 'mjdiaz');
			echo "SOLO DE PRUEBA, DESCOMENTAR PARA DEJAR ENVIAR EL SMS<br>";
			print_r($params);die();
			$client = new SoapClient($wsdl_sms,$clientOptions);
			$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", $schema_sms);
			$result = $client->enviarSms(new SoapParam($soapstruct, "message"));
			echo $result;die();//se devuelve 1 si se envio el sms y 0 si no fue enviado.
		}
		catch(Exception $e){
			echo $FRACASO;die();
		}	
		
	}else{
		echo $ACCESO_DENEGADO;die();
	}	
	
} catch (SoapFault $exp) {
    print_r($exp->getMessage());//otros errores de SOAP
}
?>
