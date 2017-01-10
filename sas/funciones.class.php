<?php
class Funciones{
	public static function extraerBeneficiarios($cedula){//extraer los beneficiarios de un títular
		$conexion=new Conexion();
		$query = "select  ced_aseg as cedula,
				  as_pnombre ||' '|| as_snombre as nombre_completo,
				  as_papellido ||' '||as_sapellido as apellido_completo,
		          as_parentesco as parentesco
		          from titulares_con_asegurados
		          where ced_tit='$cedula'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$SAS_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
	
	public static function extraerDatosBeneficiario($cedula){//extraer los beneficiarios de un títular
		$conexion=new Conexion();
		$query = "SELECT asegurados.as_cedula as cedula, asegurados.as_pnombre||' '||  asegurados.as_snombre as nombre_completo, asegurados.as_papellido||' '||asegurados.as_sapellido as apellido_completo, asegurados.as_npoliza numero_poliza, asegurados.as_parentesco as parentesco
   					FROM siscam.asegurados
  					WHERE asegurados.as_cedula ='$cedula'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$SAS_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
}
