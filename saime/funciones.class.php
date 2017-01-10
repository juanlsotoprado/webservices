<?php
ini_set("soap.wsdl_cache_enabled", "0");
class Funciones {
	public static function obtener_datos_saime($cedula){
		$conexion=new Conexion();
		$query = "SELECT cedula,primernombre,segundonombre,
						primerapellido,segundoapellido,
						sexo,fecha_nacimiento,nacionalidad FROM saime where cedula = '".$cedula."'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
}
