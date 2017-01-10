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

	public function enviarSms($arr) {
		$User   = 'sms_mail';
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
		if (!preg_match('/^[0-9]{11}/', $vec[0])) {
		    return 0;
		}
		$query = " INSERT into mensajes (telefono,mensaje, t_entrada,status,origen,uid,prioridad) VALUES ('".$vec[0]."','".substr($vec[1],0,139)."','".time()."','No enviado','".substr($vec[2],0,15)."','".$uid."','11')";
		$a = pg_query($Dbconn,$query);
		//$res1 = pg_get_result($Dbconn);
		//$err = pg_result_error($res1);
		if($a){
			return 1;
		}else{
			return 0;
		}
	}

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$url_wsdl = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])."/";
$wsdl = "sms.wsdl";
$soap_server  = new SoapServer($url_wsdl.$wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();

?>
