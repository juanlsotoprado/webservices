<?php
ini_set("soap.wsdl_cache_enabled", "0");
class Funciones {
	//la primera funcion..



	public static function enviar_correo_ieu($limite){
		$conexion=new Conexion();

		$query = "select nd.id as id, nm.texto_correo as cuerpo, nd.email as email,nd.intentos_correo as intentos  from  notificaciones.destinatarios as nd inner join notificaciones.mensajes as nm on nm.id=nd.mensaje_id where nd.email is not null AND nd.fecha_envio_correo is null AND nd.error is null AND nd.intentos_correo<4 limit $limite";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		//return $resultado;
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}

		if(count($rs)>0){
			for($i=0; $i<count($rs); $i++){


				if($rs[$i]['email']!=null){
						
					$params     = array(
							'nombre'=>'MPPEUCT-OPSU',
							'correo_remitente' => "no-responder@mppeuct.gob.ve",
							'correo_destinatario' => $rs[$i]['email'],
							'asunto' => "[MPPEUCT] - NOTIFICACIÓN OPSU",
							'mensaje' => $rs[$i]['cuerpo'].'
							
Esta es una cuenta de correo no monitoreada. Por favor, no responda ni reenvie mensajes a esta cuenta.

***REGULACION DEL USO DE CORREO ELECTRÓNICO DEL MPPEUCT HACIA INTERNET***

La información contenida en este correo electrónico y cualquier anexo puede ser de carácter confidencial y es propiedad del Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología (MPPEUCT). Sólo está permitido su uso, copia, transmisión, recepción o distribución a personas debidamente autorizadas. Si usted recibió este correo por error por favor destrúyalo y/o elimine cualquier copia guardada en su sistema y notifique inmediatamente al remitente o a la dirección de correo electrónico soporteweb@mppeuct.gob.ve. Usted no debe utilizar la información aquí contenida para ningún propósito ni compartirla con otras personas.
							'
					);
					
					$client = new SoapClient("http://webservices.mppeuct.gob.ve/correo/correo.wsdl",array('encoding'=>'UTF-8'));
					$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/correo/schema.xsd");
					$objeto = $client->enviarCorreo(new SoapParam($soapstruct, "message"));
					
					
					if($objeto==1){
						$query = "update notificaciones.destinatarios set fecha_envio_correo=now() where id=".$rs[$i]['id'];
						//return "-->".$query."<--";
						$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);

					}else{
						$query = "update notificaciones.destinatarios  set intentos_correo=".($rs[$i]['intentos']+1)." where id=".$rs[$i]['id'];
						$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
					}
				}else{
					$query = "update notificaciones.destinatarios  set error='Correo Inválido' where id=".$rs[$i]['id'];
					$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
				}

			}
		}else{
			return false;
		}
		return true;
	}




	public static function enviar_sms_ieu($limite){
		$conexion=new Conexion();

		$query = "select nd.id as id, nm.texto_sms as cuerpo, nd.telefono as telefono,nd.intentos_sms as intentos  from  notificaciones.destinatarios as nd inner join notificaciones.mensajes as nm on nm.id=nd.mensaje_id where nd.telefono<>'0' AND nd.fecha_envio_sms is null AND nd.error is null AND nd.intentos_sms<4 limit $limite";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		//return $resultado;
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		//return $rs;
		if(count($rs)>0){
			for($i=0; $i<count($rs); $i++){

				if(strlen($rs[$i]['telefono'])>=10){
						
					$params	 =array(
							'numero'  =>str_pad($rs[$i]['telefono'], 11, "0", STR_PAD_LEFT),
							'mensaje' => $rs[$i]['cuerpo'],
							'origen'  => 'Envio Notificaciones',
							'uid'     => 'mjdiaz'
					);
					
					$client = new SoapClient("http://webservices.mppeuct.gob.ve/sms/sms.wsdl",array('encoding'=>'UTF-8'));
					$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/sms/schema.xsd");
						
					$objeto = $client->enviarSms(new SoapParam($soapstruct, "message"));
					
					if($objeto==1){
						$query = "update notificaciones.destinatarios set fecha_envio_sms=now() where id=".$rs[$i]['id'];
						$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);

					}else{
						$query = "update notificaciones.destinatarios set intentos_sms=".($rs[$i]['intentos']+1)." where id=".$rs[$i]['id'];
						$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
					}
				}else{
					$query = "update notificaciones.destinatarios  set error='Numero de Telefono Inválido' where id=".$rs[$i]['id'];
					$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
				}

			}
		}else{
			return false;
		}
		return true;
	}


	public static function enviar_solicitud_actualizacion($destinatario) {
		$respuesta_actualizado=false;
		$mensaje="
				Estimado usuario,

				Como parte del reimpulso del Sistema de Gestión de Tramites Universitarios (GTU) , se ha diseñado un nuevo formato para la generación del tramite y en razón de lo cual se requiere actualizar los datos de su registro a fin de poder dar continuidad efectiva a su solicitud.

				NOTA IMPORTANTE: Si al momento de su cita no ha actualizado sus datos en sistema, la misma quedará anulada automáticamente.

				Esta es una cuenta de correo no monitoreada. Por favor, no responda ni reenvie mensajes a esta cuenta.

				***REGULACION DEL USO DE CORREO ELECTRÓNICO DEL MPPEUCT HACIA INTERNET***
				La información contenida en este correo electrónico y cualquier anexo puede ser de carácter confidencial y es propiedad del Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología (MPPEUCT). Solo esta permitido su uso, copia, transmisión, recepción o distribución a personas debidamente autorizadas. Si usted recibio este correo por error por favor destrúyalo y/o elimine cualquier copia guardada en su sistema y notifique inmediatamente al remitente o a la dirección de correo electrónico soporteweb@mppeuct.gob.ve. Usted no debe utilizar la información aqui contenida para ningún propósito ni compartirla con otras personas.";
		//return $mensaje;
		//$destinatario
		try {
			$params     = array(
					'nombre'=>'GTU',
					'correo_remitente' => "no-responder@mppeuct.gob.ve",
					'correo_destinatario' => $destinatario,
					'asunto' => "[MPPEUCT] - Actualización de documentos",
					'mensaje' => $mensaje
			);
			//return $params;
			$client = new SoapClient("http://webservices.mppeuct.gob.ve/correo/correo.wsdl",array('encoding'=>'UTF-8'));
			$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/correo/schema.xsd");
			$objeto = $client->enviarCorreo(new SoapParam($soapstruct, "message"));
		} catch (SoapFault $exp) {
			print_r($exp->getMessage());
			return false;
		}

		if($objeto=='1'){
			//$respuesta_actualizado=self::marcarNotificado($id);
			//$respuesta_actualizado=true;
		}
		//se valida y se marca notificado
		//return $id;
		return $respuesta_actualizado;
	}


	public static function solicitarActualizacion2($limite, $desde, $inicio, $final){
		$conexion=new Conexion();
		$query = "select u.usuario from cita c
		inner join persona p on p.id=c.persona_id
		inner join usuario u on u.cedula=p.cedula
		where  c.estatus_id='1' AND c.fechacita>'$desde'  Order By c.id, c.fechacita ASC offset $inicio limit $limite";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}
	public static function enviarSolicitudConfirmacion2($mensaje, $destinatario, $id) {
		$respuesta_actualizado=true;
		try {
			$params     = array(
					'nombre'=>'GTU',
					'correo_remitente' => "no-responder@mppeuct.gob.ve",
					'correo_destinatario' => $destinatario,
					'asunto' => "[MPPEUCT] - Solicitud de Confirmación de Asistencia",
					'mensaje' => $mensaje
			);
			$client = new SoapClient("http://webservices.mppeuct.gob.ve/correo/correo.wsdl",array('encoding'=>'UTF-8'));
			$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/correo/schema.xsd");
			$objeto = $client->enviarCorreo(new SoapParam($soapstruct, "message"));

			if($bjeto==1){
				$conexion=new Conexion();
				$fecha=date('Y-m-d');
				$query = "UPDATE cita SET fecha_solicitud_confirmacion='$fecha' where id='$id'";
				$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
				if(!$resultado){
					$respuesta_actualizado=false;
				}
			}else{
				$respuesta_actualizado=false;
			}

		} catch (SoapFault $exp) {
			print_r($exp->getMessage());
			return false;
		}
		return $respuesta_actualizado;
	}

	public static function enviarSolicitudConfirmacion($fecha, $limite, $offset, $mensaje, $destinatario){
		$conexion=new Conexion();
		//$fecha_minima=date('Y-m-d h:i:s', (strtotime ('-29 minute')));
		$query = "select * from control_correo where id='$id'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}


	public static function extraer_minimo_maximo($id){
		$conexion=new Conexion();
		//$fecha_minima=date('Y-m-d h:i:s', (strtotime ('-29 minute')));
		$query = "select * from control_correo where id='$id'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}

	public static function actualizar_minmax($inicio, $final, $id){
		$conexion=new Conexion();
		//$fecha_minima=date('Y-m-d h:i:s', (strtotime ('-29 minute')));
		$query = "update control_correo set inicio='$inicio', final='$final' where id='$id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}


	public static function validar_registros(){
		$conexion=new Conexion();
		//$fecha_minima=date('Y-m-d h:i:s', (strtotime ('-29 minute')));
		$query = "select max(fecha_hora) as maxima from control_notificaciones";

		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}

	public static function registrar_actividad($exitos, $fracasos){
		$conexion=new Conexion();
		$fecha=date('Y-m-d h:i:s');
		$query="insert into control_notificaciones (fecha_hora, enviados, errores) values ('$fecha', '$exitos', '$fracasos')";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		return true;
	}

	public static function validar_limite_tiempo_minimo(){
		$conexion=new Conexion();
		$fecha_minima=date('Y-m-d h:i:s', (strtotime ('-3 minute')));
		$query = "select fecha_hora from control_notificaciones
		where '$fecha_minima' > (select max(fecha_hora) from control_notificaciones)
		ORDER BY fecha_hora DESC limit 1";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}

	public static function obtener_requisitos($proceso_id) {
		$conexion=new Conexion();
		$query = "SELECT id, descripcion FROM requisitoproceso WHERE procesodepartamento_id = '$proceso_id'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}


	public static function enviar_correo($id, $nombre, $wtramite, $destinatario, $dia, $hora, $correlativo) {
		$respuesta_actualizado=false;
		$mensaje="
				Estimado usuario del Sistema Gestión de Tramites Universitarios GTU,

				El presente es para informarle que la fecha de RETIRO DE DOCUMENTOS para el mes de MAYO fue reprogramada para el mes de JUNIO del presente año (2016) de la siguiente forma:


				Documentos con fecha de Retiro del 02 al 09 de Mayo: Retirar los documentos los días Miércoles 15 / 22 / 29 de Junio

				Documentos con fecha de Retiro del 17 al 31 de Mayo: Retirar los documentos los días Jueves 16 / 23 / 30 de Junio

				Documentos con fecha de Retiro del 11 al 16 de Mayo: Retirar los documentos los días Viernes 17 / 24 de Junio ó los Sábados 18 / 25 de Junio ó el Viernes 01 de Julio


				Si usted no asistió a la cita y/o ya retiro su documento haga caso omiso a este correo.

				Saludos!

				Esta es una cuenta de correo no monitoreada. Por favor, no responda ni reenvie mensajes a esta cuenta.

				***REGULACION DEL USO DE CORREO ELECTRÓNICO DEL MPPEUCT HACIA INTERNET***
				La información contenida en este correo electrónico y cualquier anexo puede ser de carácter confidencial y es propiedad del Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología (MPPEUCT). Solo esta permitido su uso, copia, transmisión, recepción o distribución a personas debidamente autorizadas. Si usted recibio este correo por error por favor destrúyalo y/o elimine cualquier copia guardada en su sistema y notifique inmediatamente al remitente o a la dirección de correo electrónico soporteweb@mppeuct.gob.ve. Usted no debe utilizar la información aqui contenida para ningún propósito ni compartirla con otras personas.";
		//return $mensaje;
		//$destinatario
		try {
			$params     = array(
					'nombre'=>'GTU',
					'correo_remitente' => "no-responder@mppeuct.gob.ve",
					'correo_destinatario' => $destinatario,
					'asunto' => "[MPPEUCT] - Retiro de Documentos",
					'mensaje' => $mensaje
			);
			//return $params;
			$client = new SoapClient("http://webservices.mppeuct.gob.ve/correo/correo.wsdl",array('encoding'=>'UTF-8'));
			$soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/correo/schema.xsd");
			$objeto = $client->enviarCorreo(new SoapParam($soapstruct, "message"));
		} catch (SoapFault $exp) {
			print_r($exp->getMessage());
			return false;
		}

		if($objeto=='1'){
			//$respuesta_actualizado=self::marcarNotificado($id);
			//$respuesta_actualizado=true;
		}
		//se valida y se marca notificado
		//return $id;
		return $respuesta_actualizado;
	}

	public static function marcarNotificado($id){
		$conexion=new Conexion();
		$fecha=date('Y-m-d h:i:s');
		$query="update cita set correo_enviado='1', updated_at='$fecha' where id='$id'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		return true;
	}

	public static function notificarGTU($limite, $desde, $hasta, $offset, $limit){
		$conexion=new Conexion();
		//$fecha=date('Y-m-d');
		$fecha=date('Y-m-d');
		$rs=array();
		$query="select c.id, p.cedula, p.nombre1||' '||p.nombre2||' '||p.apellido1||' '||p.apellido2 as nombre,
		pd.descripcion, c.fechacita, c.horacita, c.seguridad, c.email, c.correlativo, c.ubicacion, c.correo_enviado
		from cita c
		inner join persona p on c.persona_id=p.id
		inner join procesodepartamento pd on c.proceso_id=pd.id
		where
		fechacita between '$desde' AND '$hasta' AND (c.estatus_id='1' OR c.estatus_id='2')
		order by c.id ASC offset $offset limit $limite";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$CITAVIRTUAL);
		$rs = array();
		//return $query;
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerEstructuraPorTipo($tipo){
		$conexion=new Conexion();
		$rs=array();
		if($tipo=='1'){
			$query = "select id, descrpcion, tipo_oficina, id_padre from estructura where tipo_oficina='1' order by id;";
		}else{
			$query = "select id, descrpcion, tipo_oficina, id_padre from estructura where tipo_oficina!='1' order by id;";
		}
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[$row['id_padre']][$row['id']]=$row;
		}
		return $rs;

	}

	public static function extraerEstructuraPorNivelCompleto($nivel){
		$conexion=new Conexion();
		$rs=array();
		if($nivel=='1'){
			$query = "select  *, 'true' as continua from estructura where nivel_1='0' AND nivel_2='0' AND nivel_3='0'";
		}else if($nivel=='2'){
			$query = "SELECT *,
					(CASE nivel_2
					WHEN '0' THEN 'false'
					ELSE 'true'
					END) AS continua
					FROM estructura
					WHERE (nivel_1>'0'
					AND nivel_3='0')
					OR (nivel_1>'0'
					AND nivel_2='0')";
		}else if($nivel=='3'){
			$query = "SELECT *,
					'false' AS continua
					FROM estructura WHERE nivel_1 > '0'
					AND nivel_2 > '0'
					AND nivel_3 > '0'";
		}else{
			return $rs;
		}
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}

	public static function extraerEstructuraPorNivel($padre_id, $nivel){
		$conexion=new Conexion();
		$rs=array();
		if($nivel=='1'){
			$query = "select  *, 'true' as continua from estructura where nivel_1='0' AND nivel_2='0' AND nivel_3='0'";
		}else if($nivel=='2'){
			$query = "select *, (CASE nivel_2 WHEN '0' THEN 'false' ELSE 'true' END) as continua from estructura where (nivel_1='".$padre_id."' AND nivel_3='0') OR (nivel_1='".$padre_id."' AND nivel_2='0')";
		}else if($nivel=='3'){
			$query = "select *, 'false' as continua from estructura where nivel_2='".$padre_id."' ";
		}else{
			return $rs;
		}
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetalleEnte($ente_id){
		$conexion=new Conexion();
		$query = "SELECT id, nombre FROM ente where id='$ente_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetalleOficinaPorId($oficina_id){
		$conexion=new Conexion();
		$query = "SELECT id, codigo_estructura, descrpcion, id_dependencia_sigefirrhh nombre FROM estructura where id='$oficina_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}



	public static function extraerDirecciones(){
		$conexion=new Conexion();
		$query = "select id, codigo_estructura, descrpcion, id_dependencia_sigefirrhh from estructura where nivel_1='0' AND nivel_2='0' AND nivel_3='0'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}


	public static function 	extraerDireccionesLineaPorDireccionId($direccion_id){
		$conexion=new Conexion();
		$query = "select id, codigo_estructura, descrpcion, id_dependencia_sigefirrhh from estructura where nivel_1='$direccion_id' AND nivel_3='0'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerCoordinacionesPorDireccionLineaId($direccion_linea_id){
		$conexion=new Conexion();
		$query = "select id, codigo_estructura, descrpcion, id_dependencia_sigefirrhh from estructura where nivel_2='$direccion_linea_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}



	public static function extraerDetalleEstado($estado_id){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion FROM estado where id='$estado_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetalleMunicipio($municipio_id){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion, estado_id FROM municipio where id='$municipio_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetalleParroquia($parroquia_id){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion, municipio_id FROM parroquia where id='$parroquia_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}



	public static function extraerDetalleDiscapacidad($discapacidad_id){
		$conexion=new Conexion();
		$query = "SELECT id_discapacidad as id, descripcion FROM discapacidad where id_discapacidad='$discapacidad_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetalleEtnia($etnia_id){
		$conexion=new Conexion();
		$query = "SELECT id_etnia as id, descripcion, codigo_activacion FROM etnia where id_etnia='$etnia_id'";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDiscapacidades(){
		$conexion=new Conexion();

		$query = "SELECT * FROM discapacidad order by descripcion";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerPaises(){
		$conexion=new Conexion();

		$query = "SELECT id_pais as id, abreviatura, cod_pais as codigo_pais, nombre FROM pais order by nombre";

		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDetallePais($pais_id){
		$conexion=new Conexion();
		$query = "SELECT id_pais as id, abreviatura, cod_pais as codigo_pais, nombre FROM pais where id_pais='$pais_id' order by nombre";
		//return $query;
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerEtnias($filtros){
		$conexion=new Conexion();
		$where="";
		if(array_key_exists('estatus', $filtros)){
			if($where!=""){
				$and=" AND ";
			}else{
				$and=" WHERE ";
			}
			$where=$where.$and." codigo_activacion='".$filtros['estatus']."'";
		}
		$query = "SELECT id_etnia, descripcion FROM etnia $where order by descripcion";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerEstructuraEnte($ente_id){
		$conexion=new Conexion();
		$query = "WITH RECURSIVE path(nombre, path, parent, id, parent_id, nivel) AS (
		SELECT nombre, '/', null, id, id_padre, 1 FROM ente WHERE id = $ente_id
		UNION
		SELECT
		ente.nombre,
		parentpath.path || CASE parentpath.path WHEN '/' THEN'' ELSE '/' END || ente.nombre,
		parentpath.path, ente.id, ente.id_padre, nivel+1
		FROM ente, path as parentpath
		WHERE ente.id_padre = parentpath.id
		)
		SELECT * FROM path;";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerDependenciasEnte($ente_id){
		$conexion=new Conexion();
		$query = "select * from ente where id_padre='$ente_id'";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerEstados(){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion FROM estado order by descripcion";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;

	}

	public static function extraerMunicipios($estado_id){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion FROM municipio WHERE estado_id='$estado_id' order by descripcion";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}

	public static function extraerParroquias($municipio_id){
		$conexion=new Conexion();
		$query = "SELECT id, descripcion FROM parroquia WHERE municipio_id='$municipio_id' order by descripcion";
		$resultado=$conexion->ejecutarConsulta($query, Conexion::$GLOBAL_DESARROLLO);
		$rs = array();
		while($row=$conexion->descomponerFila($resultado)){
			$rs[]=$row;
		}
		return $rs;
	}




}
