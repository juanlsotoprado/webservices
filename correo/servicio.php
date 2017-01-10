<?php

require_once('../lib/PHPMailer/class.phpmailer.php');

class Servicios{
	
	var $SMTPAuth   = false;								#Habilitar la autenticacion de SMTP
	var $Host       = "smtp.mppeuct.gob.ve";						#Dominio del servidor SMTP
	var $Port       = "";									#sConfiguracion del puerto SMTP
	var $SMTPSecure = "";									#Seguridad de la conexion
	var $Username   = "";									#Usuario de la cuenta SMTP
	var $Password   = "";									#Contraseña de la cuenta sSMTP
	var $IsSMTP = false;									#Llamar a la clase SMTP
	var $CharSet = "UTF-8";									#Codificacion de caracteres
	var $nombre_remitente = "";	
	var $ErrorInfo = "";

	function enviarCorreo($arr){
		
		//arr
		//[0]Nombre de remitente
		//[1]remitente
		//[3]destinatario
		//[4]asunto
		//[5]mensaje
		//[6]html

		$i = 0;
		foreach($arr as $key => $value){
			$vec[$i] = $value;
			$i++;
		}

		$this->nombre_remitente = $vec[0]; 
		$remitente = $vec[1];
		$destinatario = $vec[2];
		$asunto = $vec[3];
		$mensaje = $vec[4];
		$html = $vec[5];

		$mail = new PHPMailer();

		if($this->IsSMTP){
			$mail->IsSMTP();	
		}

		$mail->SMTPAuth   = $this->SMTPAuth;
		$mail->Host       = $this->Host;
		$mail->Port       = $this->Port;
		$mail->SMTPSecure = $this->SMTPSecure;
		$mail->Username   = $this->Username;
		$mail->Password   = $this->Password;

		$mail->SetFrom($remitente, $this->nombre_remitente);
		
		$mail->AddReplyTo($remitente,$this->nombre_remitente);

		$mail->Subject    = $asunto;
		if(!empty($html)){
			$mail->MsgHTML($html);
		}
		$mail->Body	= $mensaje;
		$mail->CharSet = $this->CharSet;
		$mail->AddAddress($destinatario, $this->nombre_remitente);

		if(!$mail->Send()) {
		  echo "Error en el mensaje: " . $mail->ErrorInfo;
			$m = "0"; // ERROR
		} else {
			$m = "1"; //CORRECTO
		}
		return $m.$mail->ErrorInfo;
	}
}# end class

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$server = new SoapServer('http://webservices.mppeuct.gob.ve/correo/correo.wsdl');
$server->setClass('Servicios');
$server->handle();

/*
$params	 = array(
	'nombre'=>"Richard Serrano GMAIL.",
	'correo_remitente' => "intensivo_info@mppeuct.gob.ve",
	'correo_destinatario' => "rserrano@mppeuct.gob.ve",
	'asunto' => "El asunto",
	'mensaje' => "El mensaje del correo",
	'mensaje_html' => ""
);
$mail = new Servicios();
echo $m = $mail->enviarCorreo($params);*/
?>
