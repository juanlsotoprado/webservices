<?php
class Servicios{
	
	private $_SOAPSERVER = NULL;
	private $_HEADERVARS = "";
	private $_params     = array();
	private $_USER         = "";
	private $_PASSWORD     = "";

	public function __construct($soapServer_resource){
		$this->_SOAPSERVER     = $soapServer_resource;
		$this->_USER        = $_SERVER['PHP_AUTH_USER'];
		$this->_PASSWORD    = $_SERVER['PHP_AUTH_PW'];
	}

	public function consultarPersona($arr) {
		$User   = 'consulta';
		$Passwd = 'consulta_mppeuct';
		$Db     = 'sigefirrhh';
		$Port   = '5432';
		$Host   = '192.168.52.51';
		$Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
		$Dbconn = pg_connect($Str_conn) or die ("Error de conexion. ". pg_last_error());
		$i = 0;

		foreach($arr as $key=>$value){
			$vec[$i] = $value;
			$i++;
		}

		$expresion = "/^([0-9]+)$/i";
		if(!preg_match($expresion, $vec[0])) {
			return 1;
		}

		$query = "SELECT
								P .cedula,
								P .primer_nombre,
								P .segundo_nombre,
								P .primer_apellido,
								P .segundo_apellido,
								c.descripcion_cargo,
								T .fecha_antiguedad
							FROM
								personal P
							INNER JOIN trabajador T ON P .id_personal = T .id_personal
							INNER JOIN cargo c on t.id_cargo = c.id_cargo
							WHERE
								P .cedula = '".$vec[0]."'";

		$result = pg_query($Dbconn,$query);
		if($result){
			while ($row = pg_fetch_row($result)) {
				$arr = array(
									"cedula" => $row[0],
									"primer_nombre" => $row[1],
									"segundo_nombre" => $row[2],
									"primer_apellido" => $row[3],
									"segundo_apellido" => $row[4],
									"cargo" => $row[5],
									"fecha_ingreso" => $row[6]
								);
			}
			return $arr;
		}else{
			return 0;
		}
	}

}
ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$url_wsdl = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])."/";
$wsdl = "personal.wsdl";
$soap_server  = new SoapServer($url_wsdl.$wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();

?>