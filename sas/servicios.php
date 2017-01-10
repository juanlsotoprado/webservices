<?php
require_once("../conexion/conexion.class.php");
require_once("funciones.class.php");
require_once('../cls/cls_funciones.php');

class Servicios extends Conexion{
	private $_SOAPSERVER = NULL;
	private $_HEADERVARS = "";
	private $_params     = array();

	public function __construct($soapServer_resource){
		$funciones = new cls_funciones();
		$this->_SOAPSERVER     = $soapServer_resource;
	}
	
	function extraerDatosBeneficiario($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		
		if(array_key_exists('cedula', $filtros)){
			if(trim($filtros['cedula'])!=''){
				try{
					 $respuesta=Funciones::extraerDatosBeneficiario(trim($filtros['cedula']));
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error interno
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//error del tipo 2, la cedula no puede venir vacia.
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//error del tipo 1, no se recibio la cedula a consultar
		}

		return array('error'=>0, 'respuesta'=>$respuesta);
		
	}
	
	function getBeneficiariosPorCedulaTitular($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		
		if(array_key_exists('cedula', $filtros)){
			if(trim($filtros['cedula'])!=''){
				try{
					 $respuesta=Funciones::extraerBeneficiarios(trim($filtros['cedula']));
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error interno
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//error del tipo 2, la cedula no puede venir vacia.
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//error del tipo 1, no se recibio la cedula a consultar
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
		
	}
		
}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer('http://webservices.mppeuct.gob.ve/sas/sas.wsdl');
$server->setClass('Servicios');
$server->handle();
?>
