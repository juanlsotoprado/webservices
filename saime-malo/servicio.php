<?php
require_once("../conexion/conexion.class.php");
require_once("funciones.class.php");

class Servicios extends Conexion{
	

	public function __construct($soapServer_resource){
		$this->_SOAPSERVER     = $soapServer_resource;
		$this->_USER        = $_SERVER['PHP_AUTH_USER'];
		$this->_PASSWORD    = $_SERVER['PHP_AUTH_PW'];
	}

	/*function consultarSaime($cedula){
		$i = 0;
		foreach($cedula as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$cn =$this->crear_conexion();
		$cn->query = "SELECT cedula,primernombre,segundonombre,
						primerapellido,segundoapellido,
						sexo,fecha_nacimiento,nacionalidad FROM saime where cedula = '".$vec[0]."'";
		if($cn->ejecutar()){
			if($cn->reg_total>0){
				$i = 0;
				$rs = $cn->consultar();
				$arr = array("numcedula" => $rs["cedula"],"primernombre" => $rs["primernombre"],"segundonombre" => $rs["segundonombre"],"primerapellido" => $rs["primerapellido"],"segundoapellido" => $rs["segundoapellido"],"sexo" => $rs["sexo"],"fecha_nacimiento" => $rs["fechanac"],"nacionalidad" => $rs["nacionalidad"]);
				return $arr;
			}else{
				return 0;
				//throw new SoapFault("Error ", 'No encontrado');
			}#end if
		}#end if
	}#end function*/
	
	function consultarSaime($cedula){
		$i = 0;
		foreach($cedula as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$cedula=$vec[0];
		if($cedula!=''){//validación de parametros no vacío
		
			try{
				$respuesta=Funciones::obtener_datos_saime($cedula);
				if(count($respuesta)){
					return array(
							"numcedula" => $respuesta[0]["cedula"],
							"primernombre" => $respuesta[0]["primernombre"],
							"segundonombre" => $respuesta[0]["segundonombre"],
							"primerapellido" => $respuesta[0]["primerapellido"],
							"segundoapellido" => $respuesta[0]["segundoapellido"],
							"sexo" => $respuesta[0]["sexo"],
							"fechanac" => $respuesta[0]["fecha_nacimiento"],
							"nacionalidad" => $respuesta[0]["nacionalidad"]	
					);
				}
			}
			catch(Exception $e){
				return 0;//error de procesamiento_interno
			}
		
		}else{
			return 0;//no se esta pasando el parametro requerido
		}
		return 0;

	}

	

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache


$server = new SoapServer('http://webservices.mppeuct.gob.ve/saime/saime.wsdl', array('encoding'=>'ISO-8859-1'));
$server->setClass('Servicios');
$server->handle();

?>