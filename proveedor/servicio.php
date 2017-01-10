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

    public function consultarProveedor($arr) {
        
        //  error_log(print_r("54654564654654654987ggggggggggggg", true));
        
        $arrays = array();

         //   return array('error' => 1, 'respuesta' => $arrays);
        
       //  return array('error' => 1, 'respuesta' => $arrays);
        // error_log(print_r("llego", true));

        //local
      /*  $User = 'u_mct';
        $Passwd = '123456';
        $Db = 'sigesp';
        $Port = '5432';
        $Host = '172.17.90.49';*/
        
        //produccion
   
        
        $User = 'sigesp_consulta';
        $Passwd = 'c0n6509&';
        $Db = 'db_mppeuct_2016';
//        $Port = '5432';
        $Host = '10.0.0.16';
        
        
        $Str_conn = "host=$Host dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

      


        $query = "SELECT cod_pro,
       nompro,
       nacpro,
       rifpro,
       dirpro,
       telpro
FROM 
      rpc_proveedor
      where cod_pro <> '----------'";
     
        error_log($query);

        $result = pg_query($Dbconn, $query);
        
       
        if ($result) {
            while ($row = pg_fetch_row($result)) {
 
                //  error_log(print_r($row, true));

             
                $arrays[$row[0]] = array(
                    "cod_pro" => $row[0],
                    "nompro" => utf8_encode($row[1]),
                   "nacpro" => $row[2],
                    "rifpro" => $row[3],
                    "dirpro" => utf8_encode($row[4]),
                   "telpro" => $row[5]
                );
                
               // $arrays[$row[0]] = $row;
            }
            
            //$arrays = array();

            return array('error' => 1, 'respuesta' => $arrays);
            //return $arr;
        } else {
            return 0;
        }
        
        
    }

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$url_wsdl = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . "/";
$wsdl = "proveedor.wsdl";
$soap_server = new SoapServer($url_wsdl . $wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();
?>

