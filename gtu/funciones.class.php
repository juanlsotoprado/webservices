<?php
class Funciones{
	public static function extraerDatosPersonales($cedula, $nacionalidad=""){
		$conexion=new Conexion();
		if($nacionalidad==""){
			$where="WHERE cedula='$cedula'";
		}else{
			$where="WHERE cedula='$cedula' AND nacionalidad='$nacionalidad'";
		}
		$query = "SELECT * FROM persona $where limit 1";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
	
	public static function extraerCitaPorCedula($cedula, $nacionalidad="") {
		$conexion=new Conexion();
		if($nacionalidad==""){
			$where="WHERE p.cedula='$cedula' AND  ((c.estatus_id=1 AND c.fechacita>='" . date('Y-m-d') . "') OR ((c.fechacita is null OR c.fechacita>='" . date('Y-m-d') . "') AND c.estatus_id=8))";
		}else{
			$where="WHERE p.cedula='$cedula' AND p.nacionalidad='$nacionalidad' AND  ((c.estatus_id=1 AND c.fechacita>='" . date('Y-m-d') . "') OR ((c.fechacita is null OR c.fechacita>='" . date('Y-m-d') . "') AND c.estatus_id=8))";
		}
		$query = "select c.estatus_id as estatus_id, c.horacita as horacita, c.fechacita as fechacita from cita c INNER JOIN persona p ON p.id=c.persona_id $where ORDER BY c.id DESC limit 1";
		
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
	
	public static function extraerDataCitaFecha($fecha){//se extraer las citas del estatus 
		$conexion=new Conexion();
		if($nacionalidad==""){
			$where="WHERE p.cedula='$cedula' AND  ((c.estatus_id=1 AND c.fechacita>='" . date('Y-m-d') . "') OR ((c.fechacita is null OR c.fechacita>='" . date('Y-m-d') . "') AND c.estatus_id=8))";
		}else{
			$where="WHERE p.cedula='$cedula' AND p.nacionalidad='$nacionalidad' AND  ((c.estatus_id=1 AND c.fechacita>='" . date('Y-m-d') . "') OR ((c.fechacita is null OR c.fechacita>='" . date('Y-m-d') . "') AND c.estatus_id=8))";
		}
		$query = "SELECT
		a.id,
		a.cedula,
		a.nombre1,
		a.nombre2,
		a.apellido1,
		a.apellido2,
		to_char (b.fechacita,'dd-mm-YYYY') as fecha_cita,
		b.id as id_cita,
		b.horacita,
		b.confirmada,
		b.correlativo,
		c.id as id_proceso,
		b.tipo,
		c.descripcion,
		d.descripcion as estatus
		FROM
		persona a,
		cita b,
		procesodepartamento c,
		estatus d
		WHERE
		b.estatus_id = 1 AND
		a.id = b.persona_id AND
		b.proceso_id = c.id AND
		b.estatus_id = d.id AND
		b.fechacita = '$fecha' order by b.fechacita ASC";
		return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
	
	
}
