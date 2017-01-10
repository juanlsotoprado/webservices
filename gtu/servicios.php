<?php
require_once("../conexion/conexion.class.php");
require_once("funciones.class.php");
require_once('../cls/cls_funciones.php');

class Servicios extends Conexion{
	private $_SOAPSERVER = NULL;
	private $_HEADERVARS = "";
	private $_params     = array();
	private $_USER         = "";
	private $_PASSWORD     = "";
	private static $_AUTORIZADO=0;
	private static $_ERROR_AUTENTICACION=1;
	private static $_ERROR_IP=2;
	private static $_ERROR_CONEXION=3;

	public function __construct($soapServer_resource){
		$funciones = new cls_funciones();
		$this->_SOAPSERVER     = $soapServer_resource;
		$this->_USER        = $_SERVER['PHP_AUTH_USER'];
		$this->_PASSWORD    = $_SERVER['PHP_AUTH_PW'];
		$this->_IP = $funciones->obtener_ip();
	}
	
	function autorizar(){
		switch($this->_IP){
			case "127.0.0.1":
			case "172.17.90.71":
			case "172.17.90.97":
			case "172.17.90.178":
			case "192.168.52.18":
			case "172.17.9.23":
			break;
			default:
			return Servicios::$_ERROR_IP;
		}
		if($this->_USER == "mppeuct" and $this->_PASSWORD == md5("MPPEUCT-#itysiSt3mqs34")){
			return Servicios::$_AUTORIZADO;
		}else{
			return Servicios::$_ERROR_AUTENTICACION;
		}
	}
	
	function consultarConvocadosFecha($parametros){
		$this->autorizacion=$this->autorizar();
		if($this->autorizacion!=Servicios::$_AUTORIZADO) {
			return array('error'=>$this->autorizacion, 'respuesta'=>array());
		}
		$i=0;
		foreach($parametros as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$fecha=$vec[0];
		$citas=Funciones::extraerDataCitaFecha($fecha);
		return $citas;
		
		$conexion=new Conexion();
	}
	

	
	
	function consultarEstatusCedula($parametros){//servicio para el uso de la mensajeria de texto, recibe como parametros la cedula y la nacionalidad
	//	$this->autorizacion=$this->autorizar();
		$this->autorizacion=Servicios::$_AUTORIZADO;
		if($this->autorizacion!=Servicios::$_AUTORIZADO) {
			return array('error'=>$this->autorizacion, 'respuesta'=>array());
		}
		$i=0;
		foreach($parametros as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$cedula=$vec[0];
		$nacionalidad=$vec[1];
		$conexion=new Conexion();
		$persona=array();
		$sms="";
		
		if($nacionalidad=='P'){
			$persona=Funciones::extraerDatosPersonales($cedula, $nacionalidad);
		}else if($nacionalidad=='V' || $nacionalidad=='E'){
			$persona=Funciones::extraerDatosPersonales($cedula);
		}
		if(count($persona)>0){
			if($nacionalidad=='P'){
				$cita=Funciones::extraerCitaPorCedula($cedula, $nacionalidad);
			}else{
				$cita=Funciones::extraerCitaPorCedula($cedula);
			}
			$nombre=$persona[0]['nombre1'].' '.$persona[0]['apellido1'];
			$fecha=trim(date('d-m-Y', strtotime($cita[0]['fechacita'])));
			$hora=trim($cita[0]['horacita']);
			if(count($cita)>0){
				if($cita[0]['estatus_id']==1){
					$sms="GTU: Estimado(a) Sr(a) $nombre, su cita esta pautada para el día $fecha a la(s) $hora";
				}else{
					$sms="GTU: Estimado(a) Sr(a) $nombre, su cita aún no ha sido asignada, pronto se le indicará la fecha y hora.";
				}
			}else{
				$sms="GTU: Estimado(a) Sr(a) $nombre, no se poseen solicitudes ni citas pendientes para usted en el sistema.";
			}
		}else{
			$sms="GTU: Lo sentimos, el número de identificación que usted consultó no se encuentra registrado en el sistema. Verifique.";
		}
		
		return array('error'=>$this->autorizacion, 'respuesta'=>$sms);
	}
	
	
	function consultarCedula($parametros){//servicio de consulta por cedula GTU
		//se obtiene el valor del arreglo
		$this->autorizacion=$this->autorizar();
		if($this->autorizacion!=Servicios::$_AUTORIZADO) {
			return array('error'=>$this->autorizacion, 'respuesta'=>array());
		}
		$i=0;
		foreach($parametros as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$cedula=$vec[0];
		$nacionalidad=$vec[1];
		try{
		//nueva conexión a postgres
		$conexion=new Conexion();
		
		//restricciones
		if($nacionalidad=='P'){
			$where=" where  p.cedula='$cedula' AND p.nacionalidad='P' AND (c.estatus_id='2' OR c.estatus_id='10')";
		}else if($nacionalidad=='V' || $nacionalidad=='E'){
			$where=" where  p.cedula='$cedula' AND (c.estatus_id='2' OR c.estatus_id='10')";
		}else{
			return array('error'=>0, 'respuesta'=>array());
		}
		
		//consulta en cita, persona, cita_externo y documento_cita
		$query = "select 
				p.cedula,
				c.fechacita,
				c.tipo,
				dc.documento_id, 
				dc.cantidad,
				dc.observaciones,
				c.estatus_id  
				from cita c 
				inner join persona p on c.persona_id=p.id 
				left join documento_cita dc on (dc.cita_id=c.id AND dc.persona_id=c.persona_id)
				$where
				union
				select p.cedula,
				c.fechacita,
				c.tipo,
				dc.documento_id,
				dc.cantidad,
				dc.observaciones,
				c.estatus_id
				from cita c 
				inner join cita_externo ce on ce.cita_id=c.id
				inner join persona p on ce.persona_id=p.id 
				left join documento_cita dc on (dc.cita_id=c.id AND dc.persona_id=ce.persona_id)
				$where";
		//return $query;
		//consulta a la base de datos CITAVIRTUAL
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$array=array();
		$i=0;
		$contar_iteracion=0;
		$documentos=array();
		$cedula="";
		$fechacita="";
		$tipo="";
		$observaciones="";
		while($row=$conexion->descomponerFila($resultado)){
			if($i==0){
				$cedula=$row['cedula'];
				$fechacita=$row['fechacita'];
				$observaciones=$row['observaciones'];
				if($row['estatus_id']==2){
					$tipo="A";
				}else{
					$tipo="F";
				}
			}
			if($row['documento_id']!=''){
				$documentos[$i]["documento_id"]=$row['documento_id'];
				$documentos[$i]["cantidad"]=$row['cantidad'];
				$i++;
			}
			$contar_iteracion++;
		}
		if($contar_iteracion>0){
			$array["cedula"]=$cedula;
			$array["fechacita"]=$fechacita;
			$array["tipo"]=$tipo;
			$array["observaciones"]=$observaciones;
			$array["documentos"]=$documentos;
		}
		return array('error'=>'0', 'respuesta'=>$array);
		}
		catch(Exception $e){
			return array('error'=>Servicios::$_ERROR_CONEXION, 'respuesta'=>array());
		}
	}
}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer('http://webservices.mppeuct.gob.ve/gtu/gtu.wsdl');
$server->setClass('Servicios');
$server->handle();
?>
