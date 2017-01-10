<?php
ini_set("soap.wsdl_cache_enabled", "0");
try {
    //$params = array('limite'=>'10');
    $id = 1; //esta es el identificador para este correo en especifico, si se desea otro agregar otro contador a la BD y utilizarlo
    $params = array('limite' => '1');
    $clientOptions = array();
    $wsdl = "http://172.17.90.49/webservices/mensajes_masivos/mensajes_masivos.wsdl";
    $client = new SoapClient($wsdl, $clientOptions);
    $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://172.17.90.49/webservices/mensajes_masivos/schema.xsd");
    $objeto = $client->mensajesMasivosCorreo(new SoapParam($soapstruct, "message"));
    echo "<pre>";
    echo print_r($objeto,true);
    echo "</pre>";

    echo date('Y-m-d h:i:s') . ' --> ';
    //var_dump($objeto);die();
    switch ($objeto['error']) {
        case 0:
            echo "Destinatarios: <br>";
            echo $objeto['respuesta'];
            break;

        case 1:
            echo "[PARAMETRO FALTANTE]: 			Debe pasar el parametro 'limite'";
            break;

        case 2:
            echo "[PARAMETRO VACIO]: 				El parametro 'Limite' debe ser un número entero";
            break;

        case 3:
            echo "[ERROR INTERNO]: 					Error interno del servicio, revisar el log de errores fecha: " . date('Y-m-d h:i:s');
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
