<?php
/*AUTOR: MARIO JOS� D�AZ AMARISCUA
 *FECHA: 10 de Septiembre del 2013
*DESCRIPCI�N: Clase que realiza la funci�n de ejecutor de la BD, cumpliedo con las funciones basicas
* */
include 'parametros.php';
class Conexion extends Parametro{
        
	private function conectar($host, $user, $password, $db, $port){
		$conexion=pg_connect("host=$host user=$user password=$password dbname=$db port=$port");
		$this->conexion=$conexion;
		return $this->conexion;
	}

	public function ejecutarConsulta($sql, $link){
		try{
			$parametro=new Parametro();
			$conexion=$this->conectar($parametro->host[$link], $parametro->user[$link], $parametro->password[$link], $parametro->db[$link], $parametro->port[$link]);
			$respuesta=pg_query($conexion, $sql);
			$cerrar=$this->cerrarConexion($conexion);
			return $respuesta;
		}
		catch(Exception $e){
			echo "Error al ejecutar consulta";
		}
	}

	public function contarConsulta($resultado){
		try{
			return count($resultado);
		}
		catch(Exception $e){
			echo "Error al ejecutar consulta";
		}
	}

	public function descomponerFila($resultado){
		try{
			return pg_fetch_array($resultado);
		}
		catch(Exception $e){
			echo "Error al descomponer fila";
		}
	}

	public function cerrarConexion($conexion){
		try{
			return pg_close($conexion);
		}
		catch(Exception $e){
			echo "Error al cerrar la conexion";
		}
	}

  public function descomponerFilaAssoc($resultado){
		try{
			return pg_fetch_assoc($resultado);
		}
		catch(Exception $e){
			echo "Error al descomponer fila";
		}
	}
        
}

?>
