<?php
include 'parametros.php';

class MySQL extends Parametro{
	
	private function conectar($host, $user, $password, $db, $port){
		$conexion=mysql_connect($host, $user,$password);
		mysql_select_db($db,$conexion) or die(mysql_error());
		return $conexion;
	}
	
	public function ejecutarConsulta($sql, $link){
		try{
			$parametro=new Parametro();
			$conexion=$this->conectar($parametro->host[$link], $parametro->user[$link], $parametro->password[$link], $parametro->db[$link], $parametro->port[$link]);
			$respuesta=mysql_query($conexion, $sql);
			return $respuesta;
		}
		catch(Exception $e){
			echo "Error al ejecutar consulta";
		}
	}

	public function descomponerFila($resultado){
		try{
			return mysql_fetch_array($resultado);
		}
		catch(Exception $e){
			echo "Error al descomponer fila";
		}
	}

	public function cerrarConexion($conexion){
		try{
			return mysql_close($conexion);
		}
		catch(Exception $e){
			echo "Error al cerrar la conexion";
		}
	}


}
?>
