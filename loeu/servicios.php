<?php
require_once("../conexion/conexion.mysql.class.php");
require_once("funciones.class.php");
require_once('../cls/cls_funciones.php');

class Servicios extends MySQL{
	private $_SOAPSERVER = NULL;
	private $_HEADERVARS = "";
	private $_params     = array();

	public function __construct($soapServer_resource){
		$funciones = new cls_funciones();
		$this->_SOAPSERVER     = $soapServer_resource;
	}

	function obtenerUniversidades($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		try{
			$respuesta=Funciones::extraerUniversidades($filtros);
			$output["error"]=0;
			$output["respuesta"]=$respuesta;
		}
		catch(Exception $e){
			$output["error"]=3;
			$output["respuesta"]=array();
		}
		return $output;
	}

	function obtenerCarrerasUniversidad($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		if(array_key_exists('codigo_institucion', $filtros)){
			if(trim($filtros['codigo_institucion'])!=''){
				try{
					$respuesta=Funciones::extraerCarrerasUniversidad(trim($filtros['codigo_institucion']));
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();
					//error interno del servidor
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();
			    //busqueda vacia
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();
			//no se ha establecido el buscador
		}
		return $output;//sin error, se envia la respuesta
	}
	
	function obtenerDetalleUniversidadPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		if(array_key_exists('codigo_institucion', $filtros)){
			if(trim($filtros['codigo_institucion'])!=''){
				try{
					$respuesta=Funciones::extraerDetalleUniversidad(trim($filtros['codigo_institucion']));
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();
					//error interno del servidor
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();
				//busqueda vacia
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();
			//no se ha establecido el buscador
		}
		return $output;//sin error, se envia la respuesta
	}
	
	function obtenerDetalleCarreraPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		if(array_key_exists('codigo_carrera', $filtros)){
			if(trim($filtros['codigo_carrera'])!=''){
				try{
					$respuesta=Funciones::extraerDetalleCarrera(trim($filtros['codigo_carrera']));
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();
					//error interno del servidor
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();
				//busqueda vacia
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();
			//no se ha establecido el buscador
		}
		return $output;//sin error, se envia la respuesta
	}
	
	

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer('http://webservices.mppeuct.gob.ve/loeu/loeu.wsdl', array('encoding'=>'ISO-8859-1'));
$server->setClass('Servicios');
$server->handle();
?>
