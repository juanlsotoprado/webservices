<?php
class Funciones{
	
	
	public static function extraerDetalleCarrera($carrera_id){
		$parametros=new Parametro();
		$query = "SELECT c.*
		FROM
		carreras c
		where c.cod_carrera='$carrera_id'
		order by c.nombre_carrera";
		//return $query;
		$link = mysql_connect($parametros->host[Parametro::$LOEU], $parametros->user[Parametro::$LOEU], $parametros->password[Parametro::$LOEU]);
		mysql_select_db($parametros->db[Parametro::$LOEU], $link);
		$resultado=@mysql_query($query, $link);
		$rs = array();
		while($row= mysql_fetch_array($resultado, MYSQL_ASSOC)){
		$rs[]=$row;
		}
		return $rs;
	}
	
	public static function extraerDetalleUniversidad($codigo_institucion){
		$parametros=new Parametro();
		$query = "SELECT i.*
		 FROM
		 instituciones i
		 where i.cod_institucion='$codigo_institucion'
		 order by nombre_institucion";
		//return $query;
		$link = mysql_connect($parametros->host[Parametro::$LOEU], $parametros->user[Parametro::$LOEU], $parametros->password[Parametro::$LOEU]);
		mysql_select_db($parametros->db[Parametro::$LOEU], $link);
		$resultado=@mysql_query($query, $link);
		$rs = array();
		while($row= mysql_fetch_array($resultado, MYSQL_ASSOC)){
		$rs[]=$row;
		}
		return $rs;
	}
	
	
	public static function extraerCarrerasUniversidad($codigo_institucion){
		$parametros=new Parametro();
		$query = "SELECT
		c.cod_carrera codigo_carrera,
		c.nombre_carrera nombre_carrera,
		c.estado estatus
		
		FROM
		instituciones i
		left join carreras_instituciones ci on ci.cod_institucion=i.cod_institucion
		left join carreras c on c.cod_carrera=ci.cod_carrera
		where i.cod_institucion='$codigo_institucion'
		order by nombre_institucion";
		//return $query;
		$link = mysql_connect($parametros->host[Parametro::$LOEU], $parametros->user[Parametro::$LOEU], $parametros->password[Parametro::$LOEU]);
		mysql_select_db($parametros->db[Parametro::$LOEU], $link);
		$resultado=@mysql_query($query, $link);
		$rs = array();
		while($row= mysql_fetch_array($resultado, MYSQL_ASSOC)){
		$rs[]=$row;
		}
		return $rs;
		}
		
		
	public static function extraerUniversidades($filtros){
		$parametros=new Parametro();
		
		$tipo_estudio="";
		$codigo_institucion="";
		$dependencia_administrativa="";
		$estatus="";
		$estado_id="";
		$where="";
		
		if(array_key_exists('tipo_estudio', $filtros)){
			if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE ";
			}
			$where=$where.$and." id_tipo_estudio='".$filtros['tipo_estudio']."'";
		}
		
		if(array_key_exists('codigo_institucion', $filtros)){
		if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE";
			}
			$where=$where.$and." cod_institucion='".$filtros['codigo_institucion']."'";
		}
		
		if(array_key_exists('dependencia_administrativa', $filtros)){
		if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE ";
			}
			$where=$where.$and." id_dependencia_administrativa='".$filtros['dependencia_administrativa']."'";
		}
		
		if(array_key_exists('estatus', $filtros)){
		if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE ";
			}
			$where=$where.$and." estado='".$filtros['estatus']."'";
		}
		if(array_key_exists('estado_id', $filtros)){
			if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE ";
			}
			$where=$where.$and." substring(cod_parroquia_ine,1,2)='".$filtros['estado_id']."'";
		}
		
		
		$query = "SELECT
				cod_institucion,
				id_dependencia_administrativa,
				id_tipo_institucion,
				id_tipo_estudio,
				nombre_institucion,
				siglas,estado,
				cod_parroquia_pni,
				nombre_nucleo
				FROM
				instituciones
				$where 
				order by nombre_institucion";
		//return $query;
		$link = mysql_connect($parametros->host[Parametro::$LOEU], $parametros->user[Parametro::$LOEU], $parametros->password[Parametro::$LOEU]);
		mysql_select_db($parametros->db[Parametro::$LOEU], $link);
		$resultado=@mysql_query($query, $link);
		$rs = array();
		while($row= mysql_fetch_array($resultado, MYSQL_ASSOC)){
			$rs[]=$row;
		}
		return $rs;
	}
	
	
}
