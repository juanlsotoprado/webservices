<?php
class Servicios{

	function conectar(){

		//Establecer la conexion con el servidor LDAP
		$servidor = "ldap.mcti.gob.ve";
		$puerto = "389";
		$conexion = ldap_connect($servidor,$puerto) or die("No ha sido posible conectarse al servidor $servidor");
		return $conexion;
		//END function
	}

	function consultarDatosPersonalesLdap($parametros){
		$respuesta=array();
		$parametros = $parametros->filtros;
		if(count($parametros)>0){
			if(array_key_exists('cedula', $parametros)){
				$ds = $this->conectar();
				$r = ldap_bind($ds);
				$cedula=$parametros['cedula'];
            	$sr = ldap_search($ds, "dc=mcti,dc=gob,dc=ve", "employeenumber=$cedula");
            	$info1 = ldap_get_entries($ds, $sr);
            	if($info1["count"]!=0){
            		$respuesta=array("numcedula" => $info1[0]['employeenumber'][0],"nombre" => $info1[0]['cn'][0],"correo" => $info1[0]['mail'][0],"oficina" => $info1[0]['ou'][0]);
            	}
            	ldap_close($ds);
			} else if(array_key_exists('usuario', $parametros)){
				$ds = $this->conectar();
				$r = ldap_bind($ds);
				$usuario=$parametros['usuario'];
				$sr = ldap_search($ds, "dc=mcti,dc=gob,dc=ve", "uid=$usuario");
				$info1 = ldap_get_entries($ds, $sr);
				if($info1["count"]!=0){
            		$respuesta=array("numcedula" => $info1[0]['employeenumber'][0],"nombre" => $info1[0]['cn'][0],"correo" => $info1[0]['mail'][0],"oficina" => $info1[0]['ou'][0]);
				}
            	ldap_close($ds);
			}else{
			
				return array('error'=>2, 'respuesta'=>array());//tipo de valor no valido
			}
		}else{
			return array('error'=>1, 'respuesta'=>array());//no se esta pasando el parametro requerido
		}
		return array('error'=>0, 'respuesta'=>$respuesta);
	}

	function consultarLdap($arr) {

		$i = 0;
		foreach($arr as $key => $value){
			$vec[$i] = $value;
			$i++;
		}

		$ds = $this->conectar();

		$sr = ldap_search($ds, "dc=mcti,dc=gob,dc=ve", "uid=$vec[0]");
		$info = ldap_get_entries($ds, $sr);
		$dns = '';

		for ($i=0;$i<$info["count"]; $i++){
			$dns = $info[$i]["dn"];
		}

		$dn = $dns;
		$falla = 0;

		//Si no se consigue registro asociado se envia mensaje de error
		if ($info["count"] == 0){
			//No encontrado
			return 0;
		}

		// realizando la autenticación
		@$ldapbind = ldap_bind($ds, $dn, $vec[1]);
		//si la autenticacion es true
		if($ldapbind){
			$sr = ldap_search($ds, "dc=mcti,dc=gob,dc=ve", "uid=$vec[0]");
			$info1 = ldap_get_entries($ds, $sr);
			$arr = array("numcedula" => $info1[0]['employeenumber'][0],"nombre" => $info1[0]['cn'][0],"correo" => $info1[0]['mail'][0],"oficina" => $info1[0]['ou'][0]);
			return $arr;
		}else{
			//Error de clave
			return 0;
		}

		ldap_close($ds);
	}
}#end class

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$server = new SoapServer('http://webservices.mppeuct.gob.ve/ldap/ldap.wsdl');
$server->setClass('Servicios');
$server->handle();

?>
