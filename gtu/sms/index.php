<?php
require_once("../../conexion/conexion.class.php");
$conexion=new Conexion();
$sql="select * from gammu_recibe_sms where status='New' or status=''";
$respuesta=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
$pendientes=array();
while($row=$conexion->descomponerFila($respuesta)){
	$pendientes[]=$row;
}
ini_set("soap.wsdl_cache_enabled", "0");
$peticiones_maximas_permitidas=3;
$wsdl_gtu="http://webservices.mppeuct.gob.ve/gtu/gtu.wsdl";
$wsdl_sms="http://webservices.mppeuct.gob.ve/sms/sms.wsdl";
$schema_gtu="http://webservices.mppeuct.gob.ve/gtu/schema.xsd";
$schema_sms="http://webservices.mppeuct.gob.ve/sms/schema.xsd";
$clientOptions = array('login' =>'usuario', 'password' => '123456');
try {
	if(count($pendientes)>0){
		for($i=0; $i<count($pendientes); $i++){
			//validar la fecha de recepción
			$telefono=$pendientes[$i]['telf_remoto'];
			$fecha_actual=date('Y-m-d');
			$sql="select count(id) as cantidad from gammu_recibe_sms where telf_remoto='$telefono' AND fecha_ori_sms='$fecha_actual' AND (status='Send' OR status='Wrong' OR status='Limit')";
			//echo $sql;die();
			$resultado=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
			$respuesta=$conexion->descomponerFila($resultado);
			if($respuesta['cantidad']<=$peticiones_maximas_permitidas){
				$error="";
				//$mensaje de entrada
				$mensaje=$pendientes[$i]['mensaje'];
				$mensaje=explode(' ', $mensaje);
				if(count($mensaje)==2){
					$identificador=$mensaje[0];
					if((substr($mensaje[1], 0, 1)=='V' || substr($mensaje[1], 0, 1)=='E' || substr($mensaje[1], 0, 1)=='P') && $identificador=='GTU'){
						$nacionalidad=substr($mensaje[1], 0, 1);
						if(substr($mensaje[1], 1)!=''){
							$cedula=substr($mensaje[1], 1);
							if(is_numeric($cedula)){
								$error="BIEN-FORMADO";
							}else{
								$error="MAL-FORMADO";
							}
						}else{
							$error="MAL-FORMADO";
						}
					}else{
						$error="MAL-FORMADO";
					}
				}else{
					$error="MAL-FORMADO";
				}
				if($error=="BIEN-FORMADO"){
					$mensaje=$pendientes[0]['mensaje'];
					$params = array('cedula'=>$cedula, 'nacionalidad'=>$nacionalidad, 'connection_timeout' => 30);
					$client = new SoapClient($wsdl_gtu,$clientOptions);
					$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", $schema_gtu);
					$objeto = $client->consultarEstatusCedula(new SoapParam($soapstruct, "message"));
					if($objeto['error']==0){
						try{
							$params	 = array('numero'=>$pendientes[$i]['telf_remoto'],'mensaje' => $objeto['respuesta'],'origen' => 'Respuesta automática', 'uid' => 'GTU');
							$client = new SoapClient($wsdl_sms,$clientOptions);
							$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", $schema_sms);
							//print_r($params);echo "<br>";
							$result = $client->enviarSms(new SoapParam($soapstruct, "message"));
							//$result=1;
							if($result==1){
								$fecha=date('Y-m-d h:i:s');
								$id=$pendientes[$i]['id'];
								$sql="UPDATE gammu_recibe_sms SET status='Send', fecha_respuesta='$fecha' where id='$id'";
								$respuesta=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
							}
							//se devuelve 1 si se envio el sms y 0 si no fue enviado.
						}
						catch(Exception $e){
							echo "ERROR ENVIANDO SMS";
							die();//fracaso, ocurrio un error
						}
					}else{
						echo "ACCESO DENEGADO A LA IP ACTUAL";
						die();//aceeso de IP denegado
					}
				}else{
					$fecha=date('Y-m-d h:i:s');
					$id=$pendientes[$i]['id'];
					$sql="UPDATE gammu_recibe_sms SET status='Wrong', fecha_respuesta='$fecha' where id='$id'";
					$respuesta=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
				}
			}else if($respuesta['cantidad']==$peticiones_maximas_permitidas+1){
				try{
					$sms="GTU: le informamos que ha excedido el número de consultas máximas permitidas por día, intente mañana nuevamente.";
					$params	 = array('numero'=>$pendientes[$i]['telf_remoto'],'mensaje' => $sms,'origen' => 'Respuesta automática', 'uid' => 'GTU');
					$client = new SoapClient($wsdl_sms,$clientOptions);
					$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", $schema_sms);
					//print_r($params);echo "<br>";
					$result = $client->enviarSms(new SoapParam($soapstruct, "message"));
					//$result=1;
					if($result==1){
						$fecha=date('Y-m-d h:i:s');
						$id=$pendientes[$i]['id'];
						$sql="UPDATE gammu_recibe_sms SET status='Limit', fecha_respuesta='$fecha' where id='$id'";
						$respuesta=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
					}
				}
				catch(Exception $e){
					echo "ERROR ENVIANDO SMS";
					die();//fracaso, ocurrio un error
				}
				//se devuelve 1 si se envio el sms y 0 si no fue enviado.
			}else{
				$fecha=date('Y-m-d h:i:s');
				$id=$pendientes[$i]['id'];
				$sql="UPDATE gammu_recibe_sms SET status='Limit', fecha_respuesta='$fecha' where id='$id'";
				$respuesta=$conexion->ejecutarConsulta($sql, Conexion::$GAMMU);
			}
		}
	}else{
		echo "NO HAY PETICIONES PENDIENTES";die();
	}
	echo "MENSAJES RESPONDIDOS";die();
} catch (SoapFault $exp) {
	print_r($exp->getMessage());//otros errores de SOAP
}
?>
