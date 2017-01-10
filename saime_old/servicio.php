<?php

require_once("../cls/cls_conexion.php");
require_once("../cls/cls_funciones.php");

class Servicios{
	
	private $_SOAPSERVER = NULL;
	private $_HEADERVARS = "";
	private $_params     = array();
	private $_USER         = "";
	private $_PASSWORD     = "";

	#Propiedades de Conexion
	var $conexion_parametro = true;
	var $servidor = "192.168.52.28";
	var $bdatos = "global";
	var $usuario = "usrc_loeu";
	var $password = "8A0BA4064896E708705686622F3CB4DC";
	var $arr_conexion = Array();

	#============================================================================================================================

	public function __construct($soapServer_resource){
		$this->_SOAPSERVER     = $soapServer_resource;
		$this->_USER        = $_SERVER['PHP_AUTH_USER'];
		$this->_PASSWORD    = $_SERVER['PHP_AUTH_PW'];
	}

	#============================================================================================================================	

	function crear_conexion(){
		#Crear conexion
		$cn = new cls_conexion();
		if($this->conexion_parametro){
			$cn->modo = 1;
			$cn->auditoria = false;
			if(count($this->arr_conexion)>=4){
				$cn->servidor = $this->arr_conexion[0];
				$cn->bdatos = $this->arr_conexion[1];
				$cn->usuario = $this->arr_conexion[2];
				$cn->password = $this->arr_conexion[3];
				$cn->auditoria = ($this->arr_conexion[4]=="")?(false):(true);
			}else{
				$cn->servidor = $this->servidor;
				$cn->bdatos = $this->bdatos;
				$cn->usuario = $this->usuario;
				$cn->password = $this->password;
			}#end id
		}#end if
		$cn->correos = "rserrano@mppeuct.gob.ve"; 	//Correos para notificar errores
		$cn->notificar_error_query = true; 			//Notificar errores comunes
		$cn->notificar_error_conexion = true; 		//Notificar Errores de conexion a Servidor
		$cn->notificar_error_conexion_bd = true;	//Notificar Errores de conexion a base de datos
		return $cn;
	}#end function

	function consultarSaime($cedula){
		$i = 0;
		foreach($cedula as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$cn = $this->crear_conexion();
		$cn->query = "SELECT numcedula,primernombre,segundonombre,primerapellido,segundoapellido,sexo,fechanac,nacionalidad,paisorigen,letra FROM SAIME_cedulas where numcedula = '".$vec[0]."'";
		if($cn->ejecutar()){
			if($cn->reg_total>0){
				$i = 0;
				$rs = $cn->consultar();
				$arr = array("numcedula" => $rs["numcedula"],"primernombre" => $rs["primernombre"],"segundonombre" => $rs["segundonombre"],"primerapellido" => $rs["primerapellido"],"segundoapellido" => $rs["segundoapellido"],"sexo" => $rs["sexo"],"fechanac" => $rs["fechanac"],"nacionalidad" => $rs["nacionalidad"],"paisorigen" => $rs["paisorigen"],"letra" => $rs["letra"]);
				return $arr;
			}else{
				return 0;
				//throw new SoapFault("Error ", 'No encontrado');
			}#end if
		}#end if
	}#end function

	public function consultarSaime1($arr) {
		/*$User   = 'sms_mail';
		$Passwd = 'sms_mail2015';
		$Db     = 'smsgammu';
		$Port   = '5432';
		$Host   = '172.17.9.17';
		$Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
		$Dbconn = pg_connect($Str_conn) or die ("Error de conexion. ". pg_last_error());
		$i = 0;
		foreach($arr as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}
		$uid = "";
		if(!empty($vec[3])) {
			$uid = $vec[3];
		}
		$query = " INSERT into mensajes (telefono,mensaje, t_entrada,status,origen,uid,prioridad) VALUES ('".$vec[0]."','".substr($vec[1],0,139)."','".time()."','No enviado','".substr($vec[2],0,15)."','".$uid."','11')";
		$a = pg_query($Dbconn,$query);
		//$res1 = pg_get_result($Dbconn);*/
		//$err = pg_result_error($res1);
		
		//numcedula,primernombre,segundonombre,primerapellido,segundoapellido,sexo,fechanac,nacionalidad,paisorigen,letra
		
		//$arreglo = array("nombreNucleo"=>'Caracas',"siglas"=>'UCV',"nombreInstitucion"=>'Universidad Central');
		//E	3839	ESP	ESP	Jose		Gomez	Asensi	01/01/0001		00	01	2	0	M

		return $arr;
		/*if($a){
			return 1;
		}else{
			return 0;
		}*/
	}

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

/*$url_wsdl = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])."/";
$wsdl = "saime.wsdl";
$soap_server  = new SoapServer($url_wsdl.$wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();*/

$server = new SoapServer('http://webservices.mppeuct.gob.ve/saime/saime.wsdl', array('encoding'=>'ISO-8859-1'));
$server->setClass('Servicios');
$server->handle();

?>