<?php
//require_once("cls_constantes.php");
//require_once("cls_auditorias.php");
//require_once("cls_funciones.php");

class prop_campos{
	var $nombre;
	var $longitud;
	var $flags;
	var $tabla;
	var $tipo_m;
	var $tipo;
	var $default;
	var $null;
	var $key;
	var $extra;
}// end class

class cls_conexion{
	var $servidor = C_SERVIDOR;
 	var $bdatos = C_BDATOS;
  	var $usuario = C_USUARIO;
	var $password = C_PASSWORD;
	var $conexion;
   var $estado = false;
	//======================
	var $query;
	var $tabla;
	var $registros;
	var $nro_filas = 0;
	var $nro_campos;
	var $paginacion = C_NO;
	var $pagina = 0;
	var $nro_paginas;
	var $reg_ini = C_REG_INI;
	var $reg_bloque = C_REG_BLOQUE;
	public $reg_total = 0;
	var $propietario = C_BD_ADMINISTRACION;
	var $cfg_prop = "false";
	var $modo = "";
	var $alias_tabla;
	var $arreglo_registros;					#Arreglo de Registro
	//======================
	var $insert_id;
	//======================
	var $en_transaccion = C_NO;
	var $error=false;
	var $errno=0;
	var $errno_m;
	var $errmsg="";
	var $errabs = 0;
	var $error_detectado = false;
	var $mostrar_error = true;
	var $es_consulta;
	var $tipo_sentencia="";
	//=============================
	// propiedades para la Auditoria
	var $auditoria = true;
	var $descripcion_evento="";
	//=============================
	// propiedades para los campos
	var $nombre;
	var $tipo;
	var $tipo_m;
	var $longitud;
	var $valor;
	var $val_enum;
	// errores en la conexion
	// propiedades para los registros
	var $resultset;
	var $registro;
	var $n_filas;
	var $n_campos;
	var $con_descrip=false;
	var $taux = "_t_aux";
	var $ckeys = "";
	var $claves;
	#String de Conexion
	var $imprimir_conexion = false;
	var $ip_imprimir_conexion = "";
	var $order_by = "";							#Campo por el que se desea ordenar el query en caso de activar la paginacion
	var $orden = "0";
	//==========================================================
	// Funcion constructora de la clase
	//===========================================================

	function cls_conexion(){

	}// end function
	//==========================================================
	// Funcion para crear la conexion a la base de datos
	//===========================================================
	function conectar($servidor="",$usuario="",$password="",$bdatos=""){
		/*if($this->modo == "1"){
			$this->auditoria = false;
		}#end if*/

		if($this->modo == ""){
			$this->bdatos = C_BDATOS_2;
			$this->usuario = C_USUARIO_2;
			$this->password = C_PASSWORD_2;
		}//end if

		if($this->modo == "2"){
			$this->bdatos = C_BDATOS_3;
			$this->usuario = C_USUARIO_3;
			$this->password = C_PASSWORD_3;
		}//end if
		
		
		if ($servidor!=""){
				$this->servidor = $servidor;
		}// end if
		if ($usuario!=""){ 
				$this->usuario = $usuario;
		}// end if
		if ($password!=""){
				$this->password = $password;
		}// end if
		if ($bdatos!=""){
				$this->bdatos = $bdatos;
		}// end if

		if($this->imprimir_conexion==true and $this->ip_imprimir_conexion!=""){
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] == $this->ip_imprimir_conexion){
				//echo " <br>--> (".$this->servidor.", ".$this->usuario.", ".$this->password.", ".$this->query.")";
			//	echo " <br>--> (".$this->servidor.", ".$this->usuario.", ".$this->password.")<br>".$this->query."<hr>";
			}#end if
		}#end if

		//echo "<div style=\"z-index:10;width:auto;position:relative;height:auto;background-color:#FFF;border:#F00 solid 2px;margin:5px;display:block;	\"> (".$this->servidor.", ".$this->usuario.", ".$this->password.")<br>".$this->query."</div>";
		//echo " <br><br>".$this->query."<hr>";
		//die;
		//$this->servidor = "172.17.2.31";
		//exit;
		$cn = false;
		$this->conexion = mysqli_connect($this->servidor,$this->usuario,$this->password,$this->bdatos); 
		if (mysqli_connect_errno()) {
		    printf("Error en conexión: %s\n", mysqli_connect_error());
		    exit();
		}else{
			$this->estado = true;
		}
    }// end function
	//===========================================================
	public function ejecutar($query_x=""){
		if($this->modo == ""){
			$this->bdatos = C_BDATOS_2;
			$this->usuario = C_USUARIO_2;
			$this->password = C_PASSWORD_2;
			$this->modo="";
		}//end if
		if (!$this->estado){
			$this->conectar();
		}// end if

		if($this->en_transaccion==C_SI and $this->errabs>0){
			return false;
		}// end if
		if ($query_x!=""){
			$this->query = $query_x;
		}// end if

		$query = $this->query;
		$query = $this->hacer_query($query);
		$this->tipo_sentencia = $this->tipo_sentencia($query);

		mysqli_real_escape_string($this->conexion,$query);
		$result = mysqli_query($this->conexion,$query);
		if (mysqli_errno($this->conexion)>0){
			$this->errabs++;
			$this->es_error(true);
			return false;
		}// end if
		
		if ($this->es_select($result)){
			$this->nro_filas = mysqli_num_rows($result);
			$this->nro_campos = mysqli_num_fields($result);
			$this->reg_total = $this->nro_filas;
			//echo "<br>---->>".$this->reg_total;

			if ($this->paginacion == C_SI and is_numeric($this->reg_ini) and is_numeric($this->reg_bloque) and substr_count(strtoupper(substr($query,0,10)),strtoupper("SELECT "))>0 and substr_count(strtoupper($query),strtoupper(" LIMIT "))==0){

				$reg_ini = $this->reg_ini;
				$reg_bloque = $this->reg_bloque;
				$this->nro_paginas = ceil($this->reg_total/$this->reg_bloque);
				if ($this->pagina==0){
					$this->pagina = $this->nro_paginas;
				}// end if
				if (is_numeric($this->pagina)){
					$this->pagina = abs($this->pagina);
					if ($this->pagina > $this->nro_paginas){
						$this->pagina = $this->nro_paginas;
					}// end if
					$reg_ini = $reg_bloque * ($this->pagina-1);
				}else{
					$this->pagina = 1;
				}// end if

				if($this->order_by!=""){
					list($q,$c) = explode("order by",$query);
					$this->obtener_campos($query);
					$asc_desc = array("ASC","DESC");
					if($this->campos[$this->order_by] != "" and $this->campos[$this->order_by] != "*"){
						$query = $q." order by ".$this->campos[$this->order_by]." ".$asc_desc[$this->orden];
					}#end if
				}#end if

				$limit = " LIMIT $reg_ini,$reg_bloque";
				//echo "<br>Query: ".$query.$limit;
				$result = mysqli_query($this->conexion, $query.$limit);
				if($result){
					$this->nro_filas = mysqli_num_rows($result);
				}#end if
			}// end if
		}else{
			$this->filas_afectadas = mysqli_affected_rows($this->conexion);
			$this->insert_id = mysqli_insert_id($this->conexion);
		}// end if
		$this->resultset = $result;
		if($this->auditoria){
			$aud = new cls_auditorias();
			$aud->inicio_sesion = $_[$_SESSION['id_usuario_cd']."nombre"];
			$aud->tabla = $this->tabla;
			$aud->descripcion_evento = $this->descripcion_evento;
			$aud->tipo_sentencia = $this->tipo_sentencia;
			$aud->query = $query;
			$aud->registrar_evento();
		}//end if
		return $this->resultset;
	}// end function
	//===========================================================
	function reg_total(){
		return $this->reg_total;
	}#end function
	//===========================================================
	function ejecutar_m($query_x=""){
		if ($query_x!=""){
			$this->query = $query_x;
		}// end if
		if (!$this->estado){
			$this->conectar();
		}// end if
		if ($this->en_transaccion==C_SI and $this->errabs>0){
			return false;
		}// end if
		$array = preg_split("/(?<!\\\)".C_SEP_Q."/",$this->query);
		//$array = explode(C_SEP_Q,$this->query);
		$this->nro_query = count($array);
		for ($i=0; $i<$this->nro_query;$i++){
			$this->query_m[$i]=$array[$i];
			$result_m[$i] = mysqli_query($array[$i]);
			$this->errno_m[$i] = mysqli_errno();
			if (mysqli_errno()>0){
				$this->errabs++;
			}// end if
		}// next
	}// end function
	//===========================================================
	function consultar($result_x=""){
		//echo "-->>>".$this->es_consulta."<<<--";
		if(!$this->es_consulta)
			return false;
		if ($result_x==""){
			$result_x = $this->resultset;
		}// end if
		//echo "<br>Result: -*->".$result_x;
		if($result_x!=""){
			$arreglo_x = mysqli_fetch_array($result_x);
		}#end if
		return $arreglo_x;
	}// end function
	#===========================================================
	function consultar_posicion($n="",$result_x=""){
		if(!$this->es_consulta)
			return false;
		if ($result_x==""){
			$result_x = $this->resultset;
		}// end if

		if($result_x!="" and $n!=""){
			$arreglo_x = mysqli_data_seek($result_x,$n);
		}#end if

		return $arreglo_x;
	}#end function
	#===========================================================
	function convertir_objeto($result_x=""){
		if(!$this->es_consulta)
			return false;
		if ($result_x==""){
			$result_x = $this->resultset;
		}// end if

		if($result_x!=""){
			$arreglo_x = mysqli_fetch_object($result_x);
		}#end if

		return $arreglo_x;
	}#end function
	//===========================================================
	function begin_trans(){
		mysqli_query("BEGIN");
		$this->errabs = 0;
	}// end function
	//===========================================================
	function end_trans($tipo_x=C_COMMIT){
		switch($tipo_x){
		case C_COMMIT:
			$this->commit();
		break;
		case C_ROLLBACK:
			$this->rollback();
		break;
		case C_IGNORAR_TRANS:
			// no hace nada
		break;
		}// end switch
		$this->errabs = 0;
	}// end function
	//===========================================================
	function rollback(){
		mysqli_query("ROLLBACK");
		$this->errabs = 0;
	}// end function
	//===========================================================
	function commit(){
		mysqli_query("COMMIT");
		$this->errabs = 0;
	}// end function
	//===========================================================
	function test($query_x=""){
		if ($query_x==""){
			$query_x = $this->query;
		}// end if
		$this->conectar();
		$result = $this->ejecutar($query_x);
		$cadena = "<table border='1' align='center'>";
		if (!$this->es_select($result)){
			return "consulta no valida";
		}// end if
		$cadena .= "<tr>";
		for ($i=0;$i<$this->nro_campos;$i++){
			$cadena .= "<th>".$this->nombre[$i]."</th>";
		}// next
		$cadena .= "</tr>";
		while($this->arreglo = $this->consultar($result)){
			$cadena .= "<tr>";
			for ($i=0;$i<$this->nro_campos;$i++){
				$cadena .= "<td>".$this->arreglo[$i]."</td>";
				//$cadena .= "<td>".$this->nombre[$i]."...".$this->tipo_m[$i]."...".$this->arreglo[$i]."</td>";
			}// next
			$cadena .= "</tr>";
		}// wend
		$cadena .= "</table>";
		return $cadena;
	}// end function
	//===========================================================
	function obtener_pk($result){
		$this->es_error($this->error);
		$tabla_aux="";
		$clave_aux="";
		unset($this->tablas);
		$j=0;// contador de tablas
		$m=0;// contador de claves
		$this->nro_campos = mysqli_num_fields($result);
		$this->nro_filas = mysqli_num_rows($result);
	    	for ($i=0;$i< $this->nro_campos;$i++){
			$tabla = mysqli_field_table($result,$i);
			$campo = mysqli_field_name($result,$i);
			$tabla_x = $tabla;
			if($tabla==null){
				$tabla = $this->taux;
				$this->aux[$i] = C_SI;
			}else{
				$this->aux[$i] = C_NO;
			}//end if
			$this->key[$i] = $this->key[$tabla][$campo];
			if($this->key[$i]!=null and $this->key[$i]!="" and $clave_aux[$tabla.".".$campo]!="1"){
				$this->keys[$m] = $tabla.".".$campo;
				$clave_aux[$tabla.".".$campo] = "1";
				$this->claves[$tabla][] = $campo;
				$m++;
			}// end if
		}// next
    }// end function
    //===========================================================
	function config_avanzada($result){
		$this->es_error($this->error);
		$tabla_aux="";
		$clave_aux="";
		unset($this->tablas);
		$j=0;// contador de tablas
		$m=0;// contador de claves

		/*
		$info_campo = $resultado->fetch_field_direct($result,$i);
		printf("Nombre:        %s\n", $info_campo->name);
		printf("Tabla:         %s\n", $info_campo->table);
		printf("Longitud máx.: %d\n", $info_campo->max_length);
		printf("Banderas:      %d\n", $info_campo->flags);
		printf("Tipo:          %d\n", $info_campo->type);
		*/

		if($result!=""){
			$this->nro_campos = mysqli_num_fields($result);
		}#end if
		
		//print_r($this->nro_campos);
		
		//echo "Total -->".count($this->nro_campos);	
		if($result!=""){
			$this->nro_filas = mysqli_num_rows($result);
		}#end if
	    	for ($i=0;$i< $this->nro_campos;$i++){
	    		
				$info_campo = mysqli_fetch_field_direct($result, $i);
			
				/*print_r($info_campo);
				printf("Nombre:     %s\n", $info_campo->name);
				printf("Tabla:    %s\n", $info_campo->table);
				printf("Longitud máx.: %d\n", $info_campo->max_length);
				printf("Banderas:    %d\n", $info_campo->flags);
				printf("Tipo:     %d\n", $info_campo->type);
				printf("longitud:     %d\n", $info_campo->length);*/

				$tabla = $info_campo->table;
				$campo = $info_campo->name;

			$tabla_x = $tabla;
			if($tabla==null){
				$tabla = $this->taux;
				$this->aux[$i] = C_SI;
			}else{
				$this->aux[$i] = C_NO;
			}// end if

			$this->tipo[$tabla][$campo] = $info_campo->type;
			$this->nombre[$tabla][$campo] = $campo;
			$this->longitud[$tabla][$campo] = $info_campo->length;
			$this->flags[$tabla][$campo] = $info_campo->flags;
			$this->tabla[$tabla][$campo] = $info_campo->table;
			$this->tipo_m[$tabla][$campo] = $this->tipo_meta($info_campo->type);
			$this->nombre[$i] = $campo;
			$this->longitud[$i] = $info_campo->length;
			$this->flags[$i] = $info_campo->flags;
			$this->tabla[$i] = $tabla;
			$this->tipo[$i] = $info_campo->type;
			$this->tipo_m[$i] = $this->tipo_meta($info_campo->type);
			if(strlen($this->tabla[$i]) <= 3){
				$this->alias_tabla[$i] = $this->tabla[$i];
			}//end if
			if ($this->tabla[$i]!=null and $this->tabla[$i]!=$tabla_aux and strlen($this->tabla[$i]) >= 3){
				$this->tablas[$j]=$this->tabla[$i];
				$tabla_aux = $this->tabla[$i];
				if ($tabla_x != null and !isset($tabla_v[$this->tabla[$i]])){
					$this->config_extra($tabla_aux);
					$tabla_v[$this->tabla[$i]]="1";
				}// end if
				$j++;
			}// end if
			$this->default[$tabla][$campo] = (isset($this->default[$tabla][$campo]))?$this->default[$tabla][$campo]:"";
			$this->null[$tabla][$campo] = (isset($this->null[$tabla][$campo]))?$this->null[$tabla][$campo]:"";
			$this->key[$tabla][$campo] = (isset($this->key[$tabla][$campo]))?$this->key[$tabla][$campo]:"";
			$this->extra[$tabla][$campo] = (isset($this->extra[$tabla][$campo]))?$this->extra[$tabla][$campo]:"";
			$this->default[$i] = $this->default[$tabla][$campo];
			$this->null[$i] = $this->null[$tabla][$campo];
			$this->key[$i] = $this->key[$tabla][$campo];
			$this->extra[$i] = $this->extra[$tabla][$campo];
			if(!empty($this->key[$i]) and !empty($this->key[$i]) and !empty($clave_aux[$tabla.".".$campo])!="1"){
				$this->keys[$m] = $tabla.".".$campo;
				$clave_aux[$tabla.".".$campo] = "1";
				$this->claves[$tabla][] = $campo;
				$m++;
			}// end if
		}// next
	}// end function
	//===========================================================
	#Actualizado 21/07/09 por R2S
	function describir_campos($tabla_x,$bd_x=""){
		if($bd_x!=""){
			$bd = $bd_x.".";
		}#end if
		$query_x = "SHOW FIELDS FROM ".$bd.$tabla_x;
		$result = mysqli_query($this->conexion,$query_x);
		if($result){
			$n_filas = mysqli_num_rows($result);
			$this->serial[$tabla_x] = "";
			for ($i=0;$i<$n_filas;$i++){
				$row_x = mysqli_fetch_array($result);
				if($i==0){
					$this->campo_clave = $row_x[0];
				}#end if
				$this->nombre[$i] = $row_x[0];
				$this->tipo[$i] = $row_x[1];
				if($row_x["Key"] == "PRI"){
					$this->campo_clave = $row_x[0];
				}#end if
			}#end next i
		}#end if result
	}#end function
	//===========================================================
	function config_extra($tabla_x){
		$query_x = "DESCRIBE $this->bdatos.$tabla_x";
		if($this->bdatos!=$this->propietario and substr($tabla_x,0,4)=="cfg_"){
			$query_x = "DESCRIBE $this->propietario.$tabla_x";
		}//end if
		$result = mysqli_query($this->conexion,$query_x);
		if($result){
			$n_filas = mysqli_num_rows($result);
		}#end if
		$this->serial[$tabla_x] = "";
		for ($i=0;$i<$n_filas;$i++){
			$row_x = mysqli_fetch_row($result);
			$nombre_x = $row_x[0];
			$this->null[$tabla_x][$nombre_x] = $row_x[2];
			$this->key[$tabla_x][$nombre_x] = $row_x[3];
			if($row_x[3]!=null and $row_x[3]!=""){
				$this->ckeys .= (($this->ckeys!="")?",":"").$tabla_x.".".$nombre_x;
			}// end if
			$this->default[$tabla_x][$nombre_x] = $row_x[4];
			if ($row_x[5]=="auto_increment"){
				$this->extra[$tabla_x][$nombre_x] = C_CLAVE_SERIAL;
				$this->serial[$tabla_x] = C_CLAVE_SERIAL;
			}else{
				$this->extra[$tabla_x][$nombre_x] = C_CLAVE_NORMAL;
			}// end if
		}// next
	}// end function
	//===========================================================
	function es_select($result_x){		
		//if (substr("'".$result_x."'",0,8)=="Resource" or substr_count(strtoupper(substr($this->query,0,10)),strtoupper("SELECT "))>0 or strtoupper(substr($this->query,0,7)) == "SELECT\n"){
		if (substr_count(strtoupper(substr($this->query,0,10)),strtoupper("SELECT "))>0 or strtoupper(substr($this->query,0,7)) == "SELECT\n"){			
			$this->es_consulta = true;
			return true;
        }// end if
			$this->es_consulta = false;
        return false;
	}// end function
	//===========================================================
	function hacer_query($query_x){
		$nro_palabras = explode(" ",$query_x);
		if (count($nro_palabras)>1 or substr_count(strtoupper(substr($query_x,0,10)),strtoupper("SELECT "))>0){
			return $query_x;
		}// end if
		$this->tabla[0] = $query_x;
		return "SELECT * FROM ".$this->tabla[0];
	}// end function
	//===========================================================
	function es_error($error=false){
		$funciones = new cls_funciones();
		$this->error = false;
		if ($error==false){
			return true;
		}// end if
		$this->error = true;
		$this->errno = mysqli_errno($this->conexion);
		$this->ult_errno = $this->errno;
		$this->errmsg = mysqli_error($this->conexion);
		$this->errmsg = $this->msg_errores($this->errno,$this->errmsg);
		$this->error_detectado = true;

		if($this->notificar_error_query){
			$this->notificar(2);
		}else{
			if($this->errno!=0){
				$this->notificar(3);
			}
		}#end if

		if ($this->mostrar_error){
			$funciones->alert($this->errmsg." Query: $this->query");
			//$funciones->alert($this->errmsg);
		}// end if
		return false;
	}// end function
	//===========================================================
	function show($msg){
		echo "<hr>$msg<hr>";
 	}// end function
	//===========================================================
	function tipo_meta($tipo_x){
	    	switch ($tipo_x){
	        	case "int":
	            	return C_TIPO_I;
	        	case "string":
	            	return C_TIPO_C;
	        	case "blob":
	            	return C_TIPO_X;
	        	case "float":
	            	return C_TIPO_N;
	        	case "real":
	            	return C_TIPO_N;
	        	case "date":
	        	case "timestamp":
	            	return C_TIPO_D;
	        	case "time":
	            	return C_TIPO_T;
	            default:
	            	return C_TIPO_C;
		}// end switch
	}// end function
	//===========================================================
	function extraer_bdatos(){
		$result_x = mysqli_list_dbs();
		$n_db = mysqli_num_rows($result_x);
		for ($i=0;$i<$n_db;$i++){
			$nombre_db[$i] = mysqli_db_name($result_x,$i);
		}// next
		mysqli_free_result($result_x);
		if ($n_db>0){
			return $nombre_db;
		}// end if
		return 0;
	}// end function
	//===========================================================
	function close($cn_x=""){
		$this->usuario = "";
		$this->password = "";
		$this->estado=false;
		if($cn_x!=""){
			mysqli_close($cn_x);
		}else{
			if($this->conexion){
				mysqli_close($this->conexion);
			}#end if
		}//end if
	}//end function
	//===========================================================
	function extraer_tablas($db_x=""){
		if ($db_x == ""){
			$db_x = $this->bdatos;
		}// end if
		
		if (!$this->estado){
			$this->conectar();
		}// end if

		$tableList = array();
		$res = mysqli_query($this->conexion,"SHOW TABLES");
		while($rs = mysqli_fetch_array($res)){
			$tableList[] = $rs[0];
		}
		return $tableList;

		/*
		$result_x = mysqli_list_tables($db_x);
		if($result_x){
			$n_tablas = mysqli_num_rows($result_x);
			for ($i=0;$i<$n_tablas;$i++){
				$nombre_tabla[$i] = mysqli_tablename($result_x,$i);
			}// next
			if ($this->bdatos!=$db_x){
				$result_x = mysqli_list_tables($this->bdatos);
			}// end if
			mysqli_free_result($result_x);
			if ($n_tablas>0){
				return $nombre_tabla;
			}// end if
		}#end if result
		return 0;*/
	}// end function
	//===========================================================
	function extraer_campos($tabla_x="",$db_x=""){
		if ($tabla_x==""){
			return 0;
		}// end if
		if ($db_x==""){
			$db_x = $this->bdatos;
		}// end if
		//echo "<br>-->".$db_x.".".$tabla_x;
		$result_x = mysqli_list_fields($db_x,$tabla_x);
		$n_campos = mysqli_num_fields($result_x);
		for ($i=0;$i<$n_campos;$i++){
			$nombre_campo[$i] = mysqli_field_name($result_x,$i);
		}// next
		if ($this->bdatos!=$db_x){
			$result_x = mysqli_list_tables($this->bdatos);
		}// end if
		mysqli_free_result($result_x);
		if($n_campos>0){
			return $nombre_campo;
		}// end if
		return 0;
	}// end function
	#===========================================================
	function liberar_resultset(){
		//return mysqli_free_result($this->resultset);
	}#end if
	#===========================================================
	function tipo_sentencia($query_x=""){
		if($query_x==""){
			$query_x = $this->query;
		}//end if
		$query_x = strtoupper($query_x);
		if (substr($query_x,0,6) == "SELECT"){
			return "SELECT";
        }else if (substr($query_x,0,6) == "UPDATE"){
			return "UPDATE";
        }else if (substr($query_x,0,6) == "INSERT"){
			return "INSERT";
        }else if (substr($query_x,0,6) == "DELETE"){
			return "DELETE";
			}else if (substr($query_x,0,4) == "CALL"){
			return "STORE_PROCEDURE";
        }else{
			return "SELECT";
		}// end if
	}//end if
	//===========================================================
	function msg_errores($nro_error,$msg_error=""){
		switch ($nro_error){
		case "1216":
			return "Error: fallo en la restricciones de la tabla";
			break;
		case "1217":
			return "Error: No se pudo hacer la eliminacion existe una restriccion en la tabla";
			break;
		case "1054":
			return "Error: columna desconocida en la consulta ejecutada";
			break;
		case "1062":
			return "Error: El registro que se intento agregar contiene una clave duplicada";
			break;
		}// end switch
		return $msg_error." N de error: ".$nro_error;
	}// end function

	function arreglo_registros($query=""){
		if($query!=""){
			$this->query = $query;
		}#end if
		if($this->query!=""){
			$this->ejecutar();
			$i = 0;
			while($rs = $this->consultar()){
				for($j=0;$j<$this->nro_campos;$j++){
					$this->arreglo_registros[$i][$j] = $rs[$j];
				}#next
				$i++;
			}#end while
		}#end if
		return $this->arreglo_registros;
	}#end function

	function __destruct(){
		$this->liberar_resultset();
	}#end function

	function obtener_campos($query_x=""){
		//echo $query_x;
		if($query_x==""){
			$query_x = $this->query;
		}//end if
		if($query_x!=""){
			//$query_x = strtoupper($query_x);
			//$query_x = strtoupper($query_x);
			if($this->tipo_sentencia=="SELECT"){
				//echo "";
				$tb = explode(" FROM",$query_x);
				//echo "<br>->".$tb[0];
				$tb_2 = explode(" ",$tb[0]);
				$tb_3 = explode(",",$tb_2[1]);
				//echo "<br>->".$tb_2[1];
				if(count($tb_3)>1){
					for($i=0;$i<count($tb_3);$i++){
						$campos[$i] = $tb_3[$i];
					}//next
				}else{
					$campos[0] = $tb_3[0];
				}//end if
			}else if($this->tipo_sentencia=="INSERT"){
				$tb = explode("(",$query_x);
				$tb_1 = explode(")",$tb[1]);
				$tb_2 = explode(",",$tb_1[0]);
				if(count($tb_2)>1){
					for($i=0;$i<count($tb_2);$i++){
							$campos[$i] = $tb_2[$i];
					}//next
				}else{
					$campos[0] = $tb_2[0];
				}//end if
			}else if($this->tipo_sentencia == "UPDATE"){
					$tb = explode(" SET ",$query_x);
					$tb_2 = explode(",",$tb[1]);
					if(count($tb_2)>1){
						for($i=0;$i<count($tb_2);$i++){
							$tb_3 = explode("=",$tb_2[$i]);
								$campos[$i] = $tb_3[0];
						}//next
					}else{
						$campos[0] = $tb_2[0];
					}//end if
			}//end if
			$func = new cls_funciones();
			for($i=0;$i<count($campos);$i++){
					$campos[$i] = $func->caracter_especial($campos[$i],"'",1);
			}//next
			return $this->campos = $campos;
		}//end if
	}//end function

	var $correos = "rserrano@mppeuct.gob.ve"; 	//Correos para notificar errores
	var $notificar_error_query = true; //Notificar errores comunes
	var $notificar_error_conexion = true; //Notificar Errores de conexion a Servidor
	var $notificar_error_conexion_bd = true;	//Notificar Errores de conexion a base de datos
/*	var $correos = "ohernandez@opsu.gob.ve"; 	//Correos para notificar errores
	var $notificar_error_query = true; //Notificar errores comunes
	var $notificar_error_conexion = true; //Notificar Errores de conexion a Servidor
	var $notificar_error_conexion_bd = true;	//Notificar Errores de conexion a base de datos*/

	function notificar($modo=0){
		if($this->correos!=""){
			$funciones = new cls_funciones();
			$correo = explode(";",$this->correos);
			$enviar = false;
			$query = $funciones->caracter_especial($this->query,"'");
			switch($modo){
				case 0:
					if($this->notificar_error_conexion){
						$cuerpo = "Desde ".$_SERVER["SERVER_ADDR"]."/".$_SERVER["REQUEST_URI"]." se produjo un Error de conexión con el Servidor de BD \nServidor:".$this->servidor."\nBase de Datos:".$this->bdatos."\nUsuario:".$this->usuario."\nPassword: ".$this->password."\nPara agilizar la solución consulta los casos \n";
						$enviar = true;
					}#end if
				break;
				case 1:
					if($this->notificar_error_conexion){
						$cuerpo = "Desde ".$_SERVER["SERVER_ADDR"]."/".$_SERVER["REQUEST_URI"]." se produjo un Error de conexión con la Base de datos \nServidor:".$this->servidor."\nBase de Datos:".$this->bdatos."\nUsuario:".$this->usuario."\nPassword: ".$this->password."\nPara agilizar la solución consulta los casos \n";
						$enviar = true;
					}#end if
				break;
				case 2:
					$cuerpo = "Desde ".$_SERVER["SERVER_ADDR"]."/".$_SERVER["REQUEST_URI"]." se produjo el siguiente Error: ".$query."\nPara agilizar la solución consulta los casos \n";
					$enviar = true;
				break;
				case 3:
					$cuerpo = "Verificar en ".$_SERVER["SERVER_ADDR"]."/".$_SERVER["REQUEST_URI"]." la siguiente sentencia sql: "."Err: ".$this->errno." - ".$this->errmsg."\n".$query."\nPara agilizar la solución consulta los casos \n";
					$enviar = true;
				break;
			}#end switch
			foreach ($_POST as $key => $value){
				if($value!=""){
    				$cuerpo .= "$key:$value\n";
    			}#end if
			}#end foreach
			if($enviar){
				for($i=0;$i<count($correo);$i++){
					//$funciones->enviar_correo("basedatos@opsu.gob.ve",$correo[$i],"OPSU-".$this->bdatos." - Error: ".$this->errno,utf8_decode($cuerpo))."- en"."(".$_SERVER[HTTP_HOST].")";
					$c = $funciones->enviar_correo("basedatos@opsu.gob.ve",$correo[$i],"(".$_SERVER["SERVER_ADDR"].") OPSU-".$this->bdatos." - Error: ".$this->errno."- en"."(".$_SERVER["HTTP_HOST"].")",utf8_decode($cuerpo));
					if(!$c){
						$c = $funciones->enviar_correo2("basedatos@opsu.gob.ve",$correo[$i],"(".$_SERVER["SERVER_ADDR"].") OPSU-".$this->bdatos." - Error: ".$this->errno."- en"."(".$_SERVER["HTTP_HOST"].")",utf8_decode($cuerpo));
					}
				}#end for i
			}#end if enviar
		}#end if correos
	}#end function

	/*function notificar($modo=0){
		if($this->correos!=""){
			$funciones = new cls_funciones();
			$correo = explode(";",$this->correos);
			$enviar = false;
			switch($modo){
				case 0:
					if($this->notificar_error_conexion){
						$cuerpo = "Error de conexión con el Servidor de BD \nServidor:".$this->servidor."\nBase de Datos:".$this->bdatos."\nUsuario:".$this->usuario."\nPassword: ".$this->password."\n";
						$enviar = true;
					}#end if
				break;
				case 1:
					if($this->notificar_error_conexion){
						$cuerpo = "Error de conexión con la Base de datos \nServidor:".$this->servidor."\nBase de Datos:".$this->bdatos."\nUsuario:".$this->usuario."\nPassword: ".$this->password."\n".$this->query;
						$enviar = true;
					}#end if
				break;
				case 2:
					$cuerpo = $this->errmsg." Query: $this->query";
					$enviar = true;
				break;
				case 3:
					$cuerpo = $this->query;
					$enviar = true;
				break;
			}#end switch
			if($enviar){
				for($i=0;$i<count($correo);$i++){
					$funciones->enviar_correo("basedatos@opsu.gob.ve",$correo[$i],"OPSU-".$this->bdatos." - Error: ".$this->errno,utf8_decode($cuerpo));
				}#end for i
			}#end if enviar
		}#end if correos
	}#end function
	*/
	function descomponer_query($query_x){

		$i=0;

		$query_x = strtolower($query_x);

		//------------------ extraer select -------------------------
		$we=explode("from",$query_x);
		$total_select=0;


		while($i < count($we) && count($we) > 2 ){

			$abre=explode("(",$we[$i]);
			$cierra=explode(")",$we[$i]);


			if(eregi('\(',$we[$i]) && eregi('\)',$we[$i]) && count($abre) < count($cierra) ) {

				if(eregi('[^,]$',$we[$i]) ) break;

			}else if(eregi('\(',$we[$i]) ) {

			}else if(eregi('\)',$we[$i]) ) {

				if(eregi('[^,]$',$we[$i]) ) break;

			}

			$select= $select . $we[$i] . " from ";

			$i++;
		}

		$select= $select . $we[$i];
		$select=substr($select,7);
		$total_select=strlen($select) + 7;

		//--------------------------------------------------------------
		//--------------------- extraer limit -------------------------

		$we=explode("limit",$query_x);
		$totalw=count($we)-1;
		$cierra=explode(")",$we[$totalw]);
		$abre=explode("(",$we[$totalw]);
		$total_limite=0;

		if($totalw > 0){
			if(count($abre) < count($cierra)){
				$limit="";
			}else{
				$limit=$we[$totalw];
				$total_limite=strlen($limit) + strlen("limit");
			}
		}else{
			$limit="";
		}

		//-------------------------------------------------------------
		//------------------- extraer order by -------------------------

		$we=explode("order by",$query_x);
		$totalw=count($we)-1;
		$cierra=explode(")",$we[$totalw]);
		$abre=explode("(",$we[$totalw]);
		$total_order=0;

		if($totalw > 0){
			if(count($abre) < count($cierra)){
				$order="";
			}else{
				$order=substr($we[$totalw],0,(strlen($we[$totalw]) - $total_limite));
				$total_order=strlen($order) + strlen("order by");
			}
		}else{
			$order="";
		}

		//------------------------------------------------------
		//----------------- extraer group by -----------------------

		$we=explode("group by",$query_x);
		$totalw=count($we)-1;
		$cierra=explode(")",$we[$totalw]);
		$abre=explode("(",$we[$totalw]);
		$total_group=0;
		if($totalw > 0 ) {
			if(count($abre) < count($cierra)){
				$group="";
			}else{
				$group=substr($we[$totalw],0,(strlen($we[$totalw]) - $total_limite - $total_order));
				$total_group=strlen($group) + strlen("group by");
			}
		}else{
			$group="";
		}
		//--------------------------------------------------------
		//------------------- extraer where ----------------------
		$we=explode("where",$query_x);
		$totalw=count($we)-1;
		$ed="false";
		$total_where=0;

		while($totalw > 0){
			$where=$we[$totalw] . $where;
			$cierra=explode(")",$where);
			$abre=explode("(",$where);
			if(count($abre) < count($cierra)){
				$where="where " . $we[$totalw] ;
			}else{
				$where=substr($where,0,(strlen($where) - $total_limite - $total_order - $total_group ));
				$ed="true";
				$total_where=strlen($where) + strlen("where");
				break;
			}
			$totalw--;
		}
		if($ed=="false") $where="";
		//---------------------------------------------------------
		//------------------- extraer from -------------------------
		//echo $query_x . "<br>";
		$restante= strlen($query_x) - ($total_limite + $total_order + $total_group + $total_where + $total_select) ;
		$from = substr($query_x,$total_select,$restante);
		$from = substr($from,5);
		//-------------------------------------------------------
		$consulta[0]=$select;
		$consulta[1]=$from;
		$consulta[2]=$where;
		$consulta[3]=$group;
		$consulta[4]=$order;
		$consulta[5]=$limit;
		return $consulta;
	}#end function
	#==================================================================================
	function obtener_campos_2($select){
		$we=explode(",",$select);
		$totalw=count($we);
		$i=0;
		$n=0;

		if($totalw == 0){
			$campos[0][0]=$select;
			$campos[0][1]='';
		}else{
			while($i < $totalw){
				$s= $s . $we[$i] ;

				$cierra=explode(")",$s);
				$abre=explode("(",$s);

				if(count($abre) == count($cierra)){
					$wp=explode("as",$s);
					$tt=count($wp) - 1;

					if($tt > 0){
						$campos[$n][0]=substr($s,0,strlen($s) - strlen($wp[$tt]) - 3 );
						$campos[$n][1]=$wp[$tt];
					}else{
						$campos[$n][0]= $s;
						$campos[$n][1]= '';
					}
					$s="";
					$n++;
				}else{
					$s = $s . ",";
				}
				$i++;
			}
		}
		return $campos;
	}

}// end class
?>
