<?php
define ("C_ERROR_RANGO_F","La fecha \"Desde\" debe ser menor o igual a la fecha \"Hasta\"");
class cls_funciones{	
	var $buscar;							#La palabra q se desea buscar con el buscador
	var $error;								#Variable de Errores
	var $onclick_buscador="";			#Evento Onclick del Buscador
	var $keypress_buscador = "";		#Evento OnKeyPress del textbox Buscador
	var $final_resumen = " (...)";
	##############################
	#Propiedades para metodo control_imagen()	
	var $titulo_imagen="";				#Titulo en la Imagenes
	var $ruta_imagen="";					#Ruta de la Imagen
	var $onclick_imagen="";				#Evento Onclick de la Imagen
	var $onmouseover_imagen="";
	var $onmouseout_imagen="";
	var $align_imagen = "";
	var $class_imagen="";	
	var $id_imagen="";
	var $alt_imagen = "";
	##############################
	#Propiedades para metodo malla	
	var $num_registros = 0;
	var $columnas = 3;
	var $ar_contenidos = array();
	var $align = "center";
	var $width = "100%";
	var $class_malla = "";
	var $valign_malla = "top";
	var $align_malla = "";
	var $titulo_malla = "";
	var $cellpadding_malla = 0;
	var $bgcolor_malla = "#FFFFFF";
	##############################
	#Propiedades para Optener IP
	var $ip_publica = "";
	var $ip_proxy = "";
	var $ambiente_pruebas = "172.17.2.185";
	var $id_buscador = "";
	#Propiedades para envio de correo
	var $html= false;								#Para mandar el mensaje en formato HTML
	var $cc = "";									#Con Copia
	var $bcc = "";									#Con Copia Oculta
	//================================================================================================
	function obtener_resumen($cadena_x, $cantidad_x="200"){
		$cadena = substr($cadena_x,0,$cantidad_x); //extraigo los primeros 200 caracteres
		$cad = explode(' ',$cadena);
		for($i=0;$i<count($cad)-1;$i++){
			$sep = ($i==0)?(""):(" ");
			$resumen_x .= $sep.$cad[$i];
		}
		return $resumen_x.$this->final_resumen;
	}//end function
	//================================================================================================
	function validar_rango_fecha($fecha_x,$fecha_y){
		$fecha_x = substr($fecha_x,0,10);
		$fecha_y = substr($fecha_y,0,10);
		$f = explode("/",$fecha_x);
		$g = explode("/",$fecha_y);
		if((count($f)>1 and $f[2] != "0000") and (count($g)>1 and $g[2] != "0000")){
			if($f[2] <= $g[2]){
				if($f[1] <= $g[1]){
					if($f[0] <= $g[0]){
						return true;
					}else{
						$this->error = C_ERROR_RANGO_F;
						return false;
					}//end if
				}else{
						$this->error = C_ERROR_RANGO_F;
						return false;
				}//end if
			}else{
					$this->error = C_ERROR_RANGO_F;
					return false;
			}//end if
		}else{
			return false;
		}//end if
	}//end function
	//================================================================================================
	function extraer_var($cadena_x=""){
		if($cadena_x==""){ 
			return false;
		}// end if
		$gpo = explode(C_SEP_L,$cadena_x);
		return $gpo;
	}// end function
	//================================================================================================
	function formato_fecha($fecha_x){
		$f = explode("-",$fecha_x);
		if(count($f)>1)
		return $f[2]."/".$f[1]."/".$f[0];
		else
			return "";
	}// end function

	function caracter_especial($cadena_x="",$caracter_x="",$modo_x="0"){
		if($cadena_x != "" and $caracter_x != ""){
			if($modo_x!="0"){
				$cad = explode("\\",$cadena_x);
				for($i=0;$i<count($cad);$i++){
					$cad_x .= $cad[$i]; 
				}//next
				$cadena_x = $cad_x;
			}//end if
			$c = $caracter_x;
			$r = "\\".$c;
			$cadena_x = str_replace($c,$r,$cadena_x);
		}//end if
		//echo "<br>->".$cadena_x;
		return $cadena_x;
	}//end if
	//================================================================================================
	function retornar_anios($inicio_x="2000",$final_x="",$modo_x="0"){
		$x=0;
		if($final_x==""){
			$final_x = date(Y);
		}//end if
		for($i=$final_x;$i>=$inicio_x;$i--){
			$arreglo_anios[$x] = $i;
			$x++;
		}//next
		if($modo_x!="0"){
			for($i=0;$i<count($arreglo_anios);$i++){
				$sep = ";";
				if($i == 0){
					$sep = "";
				}//end if
				$arreglo .= $sep.$arreglo_anios[$i].":".$arreglo_anios[$i];
			}//next
			 $arreglo_anios = $arreglo;
		}//end if
		return $arreglo_anios;
	}//end function
	//================================================================================================
	function fecha_bd($fecha_x,$modo="0"){
		$fecha_x = explode("'",$fecha_x);
		if(count($fecha_x)>1){
			$fecha_x = substr($fecha_x[1],0,10);		
		}else{
			$fecha_x = substr($fecha_x[0],0,10);
		}#end if
		$f = explode("/",$fecha_x);
		if(count($f)>1 and $f[2] != "0000"){
			if($modo==0){
				return "'".$f[2]."-".$f[1]."-".$f[0]."'";
			}else{
			return $f[2]."-".$f[1]."-".$f[0];
			}
		}else{
			return "''";
		}//end if
	}//end function
	//==========================================================================================================================
	function bd_fecha2($fecha_x){
		if($fecha_x!=""){
			$this->fecha_bd = $fecha_x;
		}
		$f = explode("-",$this->fecha_bd);
		if(count($f)>1 and $f[2] != "0000"){
			$this->fecha_bd = $f[2]."/".$f[1]."/".$f[0];
			return $this->fecha_bd;
		}else{
			return "";
		}//end if
	}// end function
	//==========================================================================================================================
	function bd_fecha($fecha_x){
		if($fecha_x!=""){
			$this->fecha_bd = $fecha_x;
		}
		$f1 = explode(" ",$this->fecha_bd);
		$f2 = explode("-",$f1[0]);
		$this->fecha_bd = $f2[2]."/".$f2[1]."/".$f2[0];
		if($f1[1]!=""){
			$this->fecha_bd .= " ".$f1[1];
		}
		return $this->fecha_bd;
	}// end function

	#==========================================================================================================================
	#Obtener la Ruta a partir de la ubicacion en el servidor
	function obtener_ruta($url_x=""){
		//echo "<br>Ruta: ".$url_x." ".$_SERVER["REQUEST_URI"];		
		$url = explode("/",$url_x);
		$cad =  "../";
		$cad_x = "";
		if(count($url)>1){
			for($i=1;$i<count($url)-1;$i++){
				$cad_x .= $cad; 
			}//next
		}else{
			$cad_x = "";		
		}//end if
		return $cad_x;
	}//end fucntion
	#============================================================================================================================
	#Obtener grupos en los cuales pertenece un usuario
	function	verificar_grupo($sesion="",$grupo=""){
		if($_SESSION["sesion"]!=""){
			$sesion = $_SESSION["sesion"];
		}#end if
		$sv = new cls_servidores(20);
		$cn = new cls_conexion();
		$cn->query = "Select cfg_usuarios.inicio_sesion, cfg_grupos.id_grupo, cfg_grupos.descripcion From administracion.cfg_usuarios Inner Join administracion.cfg_usuario_grupo ON cfg_usuarios.id_usuario = cfg_usuario_grupo.id_usuario Inner Join administracion.cfg_grupos ON cfg_grupos.id_grupo = cfg_usuario_grupo.id_grupo Where cfg_usuario_grupo.id_grupo = '".$grupo."' and inicio_sesion = '".$sesion."'";
		$cn->ejecutar();
		/*select u.id_usuario, u.inicio_sesion, ug.id_grupo from cfg_usuarios as u 
		inner join cfg_usuario_grupo as ug on u.id_usuario = ug.id_usuario
		where inicio_sesion='rserrano' 
		group by ug.id_grupo having ug.id_grupo = '6'*/
		return $cn->reg_total;
	}#end function
	#==========================================================================================================================
	function obtener_ip(){
		if($_SERVER['HTTP_X_FORWARDED_FOR'] != ""){
			$this->ip_publica = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$this->ip_proxy = $_SERVER['REMOTE_ADDR'];
			$cliente_ip = (!empty($_SERVER['REMOTE_ADDR']))?($_SERVER['REMOTE_ADDR']):((!empty($_ENV['REMOTE_ADDR']))?($_ENV['REMOTE_ADDR']):"Desconocida");
			$entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
			reset($entries);
			while (list(, $entry) = each($entries)){
				$entry = trim($entry);
				if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
					$private_ip = array('/^0\./','/^127\.0\.0\.1/','/^192\.168\..*/','/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/','/^10\..*/');
					$found_ip = preg_replace($private_ip, $cliente_ip, $ip_list[1]);
					if ($cliente_ip != $found_ip){
						$cliente_ip = $found_ip;
						break;
					}//end if
				}//end if
			}//end while
		}else{
			$this->ip_publica = $cliente_ip = (!empty($_SERVER['REMOTE_ADDR']))?($_SERVER['REMOTE_ADDR']):((!empty($_ENV['REMOTE_ADDR']))?($_ENV['REMOTE_ADDR']):"Desconocida");
		}//end if
		$this->ip_cliente =  $cliente_ip;
		return $cliente_ip;
	}//end Function
}//end class
?>