<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
ini_set("soap.wsdl_cache_enabled", "0");
try {
	//$params = array('limite'=>'10');
	$params = array('limite'=>'500');
	$clientOptions = array();
	$wsdl="http://webservices.mppeuct.gob.ve/global/global.wsdl";
	$client = new SoapClient($wsdl, $clientOptions);
	$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/global/schema.xsd");
	$objeto = $client->notificarGTU(new SoapParam($soapstruct, "message"));
	echo date('Y-m-d h:i:s').' --> ';
	//var_dump($objeto);die();
	switch ($objeto['error']){
		case 0:
			echo $objeto['respuesta'];
		break;
			
		case 1:
			echo "[PARAMETRO FALTANTE]: 			Debe pasar el parametro 'limite'";
		break;
		
		case 2:
			echo "[PARAMETRO VACIO]: 				El parametro 'Limite' debe ser un número entero";
		break;
		
		case 3:
			echo "[ERROR INTERNO]: 					Error interno del servicio, revisar el log de errores fecha: ".date('Y-m-d h:i:s');
		break;
		
		case 4:
			echo "[TIEMPO EXCEDIDO]: 				No se ha podido ejecutar, debido a que hay que esperar el limite de tiempo establecido ";
		break;
			
		case 5:
			echo "[SIN NOTIFICACIONES PENDIENTES]: 	No hay notificaciones que enviar.";
		break;
	}
	echo "<br>";
} catch (SoapFault $exp) {
    print_r($exp->getMessage());
}
?>
