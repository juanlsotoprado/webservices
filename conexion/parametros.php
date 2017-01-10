<?php
/*AUTOR: MARIO JOSÉ DÍAZ AMARISCUA
*FECHA: 6 de Septiembre del 2013
*DESCRIPCIÓN: Clase que recibe los parametros de conexión a la(s) Base de Datos que utiliza el proyecto.
* */
class Parametro{
	//conexiones a las bases de datos...
	public static $CITAVIRTUAL=0;
	public static $LOEU=1;
	public static $SAS_DESARROLLO=2;
	public static $GLOBAL_DESARROLLO=3;
	public static $GAMMU=4;
        public static  $MM = 5;
	//public static $SAIME=5;
	//
	public $host=array();
	public $user=array();
	public $password=array();
	public $db=array();
	public $port=array();
	function Parametro(){
		/*
		EN ESTE ESPACIO SE CONFIGURAN LAS DISTINTAS CONEXIONES A LAS BASES DE DATOS DE LOS SERVICIOS QUE UTILIZA EL SISTEMA.
		*/
		//CONEXION PARA LA BASE DE DATOS CITAVIRTUAL
		$this->host[Parametro::$CITAVIRTUAL]='172.17.9.17';
		$this->user[Parametro::$CITAVIRTUAL]='u_mct';
		$this->password[Parametro::$CITAVIRTUAL]='u_mct123';
		$this->db[Parametro::$CITAVIRTUAL]='citavirtual';
		$this->port[Parametro::$CITAVIRTUAL]='5432';
		//FIN DE LOS PARAMETROS DE CONEXION DE SIGESP
		
		//CONEXION PARA LA BASE DE DATOS DEL LOEU
		$this->host[Parametro::$LOEU]='192.168.52.28';
		$this->user[Parametro::$LOEU]='usrc_loeu';
		$this->password[Parametro::$LOEU]='8A0BA4064896E708705686622F3CB4DC';
		$this->db[Parametro::$LOEU]='loe_nuevo_pro';
		$this->port[Parametro::$LOEU]='80';
		//

		//CONEXION PARA LA BASE DE DATOS DEL SAS (siscamhcm)
		$this->host[Parametro::$SAS_DESARROLLO]='172.17.9.17';
		$this->user[Parametro::$SAS_DESARROLLO]='u_mct';
		$this->password[Parametro::$SAS_DESARROLLO]='u_mct123';
		$this->db[Parametro::$SAS_DESARROLLO]='siscamhcm';
		$this->port[Parametro::$SAS_DESARROLLO]='5432';
		//

		//CONEXION PARA LA BASE DE DATOS GLOBAL 
		$this->host[Parametro::$GLOBAL_DESARROLLO]='172.17.9.17';
		$this->user[Parametro::$GLOBAL_DESARROLLO]='u_mct';
		$this->password[Parametro::$GLOBAL_DESARROLLO]='u_mct123';
		$this->db[Parametro::$GLOBAL_DESARROLLO]='global';
		$this->port[Parametro::$GLOBAL_DESARROLLO]='5432';
		//

		//CONEXION PARA LA BASE DE DATOS GAMMU
		$this->host[Parametro::$GAMMU]='172.17.9.17';
		$this->user[Parametro::$GAMMU]='sms_mail';
		$this->password[Parametro::$GAMMU]='sms_mail2015';
		$this->db[Parametro::$GAMMU]='smsgammu';
		$this->port[Parametro::$GAMMU]='5432';
		//

        
             //CONEXION PARA LA BASE DE DATOS NOTIFICACIONES
		$this->host[Parametro::$MM]='localhost';
		$this->user[Parametro::$MM]='postgres';
		$this->password[Parametro::$MM]='123456';
		$this->db[Parametro::$MM]='notificaciones';
		$this->port[Parametro::$MM]='5432';


	}

}
?>
