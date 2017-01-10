<?php

class Servicios {

    private $_SOAPSERVER = NULL;
    private $_HEADERVARS = "";
    private $_params = array();
    private $_USER = "";
    private $_PASSWORD = "";

    public function __construct($soapServer_resource) {
        $this->_SOAPSERVER = $soapServer_resource;
        $this->_USER = $_SERVER['PHP_AUTH_USER'];
        $this->_PASSWORD = $_SERVER['PHP_AUTH_PW'];
    }

    public function consultarExpediente($arr) {

       //  error_log(print_r($arr, true));

        $User = 'ssd_consulta';
        $Passwd = 'ssdd#6509&';
        $Db = 'ssd';
        $Host = '172.17.9.17';
        $Str_conn = "host=$Host dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
//        
//        
//        $User = 'u_mct';
//        $Passwd = 'u_mct123';
//        $Db = 'ssd';
//        $Port = '5432';
//        $Host = '172.17.105.111';
//        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
//        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());

        $query = "select 
       ud.usua_cedula as cedula,
       d.id_documento ,
       d.nombre as documento , 
       ud.nombre_doc as url
      
            from 
                   sgd_usua_documento ud
                   inner join sgd_documento d on (ud.id_documento = d.id_documento)

            where 
       ud.usua_registro_cedula = '".$arr->cedula."' 
       ";
        
        
        if($arr->id_documento){
            
           $query .= "and ud.id_documento =".$arr->id_documento ;   
            
        }

        $result = pg_query($Dbconn, $query);
        
        
       // error_log(print_r($query, true));
        if ($result) {
            
            while ($row = pg_fetch_assoc($result)) {
            //   error_log(print_r($row, true));

              $params[$row['cedula']][$row['id_documento']]['documento'] = $row['documento'];
             // $url =  'http://ssd.prueba.mppeuct.gob.ve/includes/upload/'.dechex($row['cedula']).'/'.$row['url'];
              $url =  'http://expedientedigital.mppeuct.gob.ve/includes/upload/'.dechex($row['cedula']).'/'.$row['url']; 

              $params[$row['cedula']][$row['id_documento']]['url'] = $url;
              

            }
            
          //  error_log(print_r($params, true));

            return array('error' => 0, 'respuesta' => $params);
            //return $arr;
        } else {
               return array('error' => 1, 'respuesta' => array());

        }
    }

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$url_wsdl = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . "/";
$wsdl = "expediente.wsdl";
$soap_server = new SoapServer($url_wsdl . $wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();
?>
