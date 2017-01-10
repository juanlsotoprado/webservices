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


	//notificaciones GTU
	function notificarGTU($parametros){
		$limite = $parametros->limite;
		$desde = $parametros->desde;
		$hasta = $parametros->hasta;
		$id = $parametros->id;
		if($limite!=''){//validación de parametro no vacío
			if(is_numeric($limite)){//validación de parametro númerico
				try{
					$min_max=Funciones::extraer_minimo_maximo($id);
					
					//return ;die();
					//|| Funciones::validar_registros()['maxima']==NULL
					//if(count(Funciones::validar_limite_tiempo_minimo())>0 || Funciones::validar_registros()[0]['maxima']==''){//validación de tiempo minimo para ejecución
						$respuesta=Funciones::notificarGTU($limite, $desde, $hasta, $min_max[0]['inicio'], $min_max[0]['final']);
						
						//return $respuesta;
						if(count($respuesta)>0){
							$enviados=0;
							$errores=0;
							$email_completos="";
							for($i=0; $i<count($respuesta); $i++){
								//return Funciones::enviar_correo($respuesta[$i]['id'],$respuesta[$i]['nombre'], $respuesta[$i]['descripcion'], $respuesta[$i]['email'], date('d-m-Y', strtotime($respuesta[$i]['fechacita'])), $respuesta[$i]['horacita'], $respuesta[$i]['correlativo']);
								if(Funciones::enviar_correo($respuesta[$i]['id'],$respuesta[$i]['nombre'], $respuesta[$i]['descripcion'], $respuesta[$i]['email'], date('d-m-Y', strtotime($respuesta[$i]['fechacita'])), $respuesta[$i]['horacita'], $respuesta[$i]['correlativo'])){
									$enviados++;
								}else{
									$errores++;
								}
								$email_completos.='<br> '.$respuesta[$i]['email'].': '.$respuesta[$i]['correlativo'].' ';
							}
							Funciones::registrar_actividad($enviados, $errores);
							Funciones::actualizar_minmax($min_max[0]['final']+1, $min_max[0]['final']+$limite, $id);
							$respuesta='[EXITO]:	NOTIFICACIONES ENVIADAS: '.($enviados).' <br> '.$email_completos;
						}else{
							return array('error'=>5, 'respuesta'=>array());//no hay notificaciones que enviar
						}
					/*}else{
						return array('error'=>4, 'respuesta'=>array());//no se ha cumplido con el tiempo minimo reglamentario
					}*/
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//los valores no se recibieron correctamente
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	
	
	function obtenerEstructuraPorTipo($parametros){
		$filtro_2 = $parametros->tipo;
		if($filtro_2!=''){
			if(is_numeric($filtro_2)){
				try{
					$respuesta=Funciones::extraerEstructuraPorTipo($filtro_2);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerEstructuraPorNivelCompleto($parametros){
		$filtro_2 = $parametros->nivel;
		if($filtro_2!=''){
			if(is_numeric($filtro_2)){
				try{
					$respuesta=Funciones::extraerEstructuraPorNivelCompleto($filtro_2);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerEstructuraPorNivel($parametros){
		$filtro_1 = $parametros->padre_id;
		$filtro_2 = $parametros->nivel;
		if($filtro_1!='' && $filtro_2!=''){
			if(is_numeric($filtro_1) && is_numeric($filtro_2)){
				try{
					$respuesta=Funciones::extraerEstructuraPorNivel($filtro_1, $filtro_2);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerDetalleOficinaPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->oficina_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleOficinaPorId($filtros);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerDirecciones($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		try{
			$respuesta=Funciones::extraerDirecciones();
		}
		catch(Exception $e){
			return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerCoordinacionesPorDireccionLineaId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->direccion_linea_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerCoordinacionesPorDireccionLineaId($filtros);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}

		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerDireccionesLineaPorDireccionId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->direccion_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDireccionesLineaPorDireccionId($filtros);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
				}
			} else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}

		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerEstados($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		try{
			$respuesta=Funciones::extraerEstados();
		}
		catch(Exception $e){
			return array('error'=>3, 'respuesta'=>array());//error de procesamiento_interno
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerMunicipios($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->estado_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerMunicipios(trim($filtros));
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerParroquias($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->municipio_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerParroquias(trim($filtros));
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error interno en procesamiento
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerEstructuraEntes($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->ente_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerEstructuraEnte($filtros);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error interno en procesamiento
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	function obtenerEstructuraEnteHijos($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->ente_padre_id;
		if(trim($filtros)!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDependenciasEnte($filtros);
				}
				catch(Exception $e){
					return array('error'=>3, 'respuesta'=>array());//error interno en procesamiento
				}
			}else{
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerEtnias($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$filtros = $parametros->filtros;
		try{
			$respuesta=Funciones::extraerEtnias($filtros);
		}
		catch(Exception $e){
			return array('error'=>3, 'respuesta'=>array());//error interno en procesamiento
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function obtenerDetalleEtniaPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->etnia_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleEtnia($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}

	function obtenerDetalleEntePorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->ente_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleEnte($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}

	function obtenerDetalleEstadoPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->estado_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleEstado($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}

	function obtenerDetalleMunicipioPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->municipio_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleMunicipio($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}

	function obtenerDetalleParroquiaPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->parroquia_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleParroquia($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}
	//TODO: SERVICIOS DE LA TABLA DISCAPACIDAD

	function obtenerDetalleDiscapacidadPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->discapacidad_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetalleDiscapacidad($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}


	function obtenerDiscapacidades($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		try{
			$respuesta=Funciones::extraerDiscapacidades();
			$output["error"]=0;
			$output["respuesta"]=$respuesta;
		}
		catch(Exception $e){
			$output["error"]=3;//error de procesamiento interno
			$output["respuesta"]=array();
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}


	//TODO: SERVICIOS DE LA TABLA PAIS
	function obtenerPaises($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		try{
			$respuesta=Funciones::extraerPaises();
			$output["error"]=0;
			$output["respuesta"]=$respuesta;
		}
		catch(Exception $e){
			$output["error"]=3;
			$output["respuesta"]=array();
		}
		return $output;
	}

	function obtenerDetallePaisPorId($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
		//filtros
		$output = array(
				"error" => 0,
				"respuesta" => array()
		);
		$filtros = $parametros->pais_id;
		if($filtros!=''){
			if(is_numeric($filtros)){
				try{
					$respuesta=Funciones::extraerDetallePais($filtros);
					$output["error"]=0;
					$output["respuesta"]=$respuesta;
				}
				catch(Exception $e){
					$output["error"]=3;
					$output["respuesta"]=array();//error interno en procesamiento
				}
			}else{
				$output["error"]=2;
				$output["respuesta"]=array();//tipo de valor no valido
			}
		}else{
			$output["error"]=1;
			$output["respuesta"]=array();//no se esta pasando el parametro requerido
		}
		return $output;
	}




}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer('http://webservices.mppeuct.gob.ve/global/global.wsdl');
$server->setClass('Servicios');
$server->handle();
?>
