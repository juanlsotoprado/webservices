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

    public function consultarPersonas($parametros) {


//        $User = 'u_mct';
//        $Passwd = '123456';
//        $Db = 'segefirrhh310316';
//        $Port = '5432';
//        $Host = '172.17.90.49';

        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';

        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

        $personas = $parametros->personas;
        return $personas;

        $expresion = "/^([0-9]+)$/i";
        if (!preg_match($expresion, $vec[0])) {
            return 1;
        }

        $query = "SELECT
                    P .cedula,
                    P .primer_nombre,
                    P .segundo_nombre,
                    P .primer_apellido,
                    P .segundo_apellido,
                    c.descripcion_cargo,
                    T .fecha_antiguedad
            FROM
                    personal P
            INNER JOIN trabajador T ON P .id_personal = T .id_personal
            INNER JOIN cargo c on t.id_cargo = c.id_cargo
            WHERE
                    P .cedula = '" . $vec[0] . "'";

        $result = pg_query($Dbconn, $query);
        if ($result) {
            while ($row = pg_fetch_row($result)) {
                $arr = array(
                    "cedula" => $row[0],
                    "primer_nombre" => utf8_encode($row[1]),
                    "segundo_nombre" => utf8_encode($row[2]),
                    "primer_apellido" => utf8_encode($row[3]),
                    "segundo_apellido" => utf8_encode($row[4]),
                    "cargo" => utf8_encode($row[5]),
                    "fecha_ingreso" => $row[6]
                );
            }

            return $arr;
        } else {
            return 0;
        }
    }

    public function consultarPersona($arr) {
        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';
        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

        foreach ($arr as $key => $value) {
            $vec[$i] = $value;
            $i++;
        }

        $expresion = "/^([0-9]+)$/i";
        if (!preg_match($expresion, $vec[0])) {
            return 1;
        }

        $query = "SELECT
                    P .cedula,
                    P .primer_nombre,
                    P .segundo_nombre,
                    P .primer_apellido,
                    P .segundo_apellido,
                    c.descripcion_cargo,
                    T .fecha_antiguedad,
                    P.id_personal
            FROM
                    personal P
            INNER JOIN trabajador T ON P .id_personal = T .id_personal
            INNER JOIN cargo c on t.id_cargo = c.id_cargo
            WHERE
                    P .cedula = '" . $vec[0] . "'";

        $result = pg_query($Dbconn, $query);
        if ($result) {
            while ($row = pg_fetch_row($result)) {
                $arr = array(
                    "cedula" => $row[0],
                    "primer_nombre" => utf8_encode($row[1]),
                    "segundo_nombre" => utf8_encode($row[2]),
                    "primer_apellido" => utf8_encode($row[3]),
                    "segundo_apellido" => utf8_encode($row[4]),
                    "cargo" => utf8_encode($row[5]),
                    "fecha_ingreso" => $row[6],
                    "id_personal" => $row[7]
                );
            }



            //  error_log(print_r($arr, true));

            return $arr;
        } else {
            return 0;
        }
    }

    public function consultarDependencias($arr) {
        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';
        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

        foreach ($arr as $key => $value) {
            $vec[$i] = $value;
            $i++;
        }


        $query = " SELECT
                    id_dependencia,
                    cod_dependencia,
                    nombre
                 FROM
                    dependencia   
                ";


        $result = pg_query($Dbconn, $query);
        if ($result) {

            while ($row = pg_fetch_row($result)) {
                $result_array[$row[0]] = $arr = array(
                    0 => $row[0],
                    1 => $row[1],
                    2 => utf8_encode($row[2])
                );
            }

            //  error_log(print_r($result_array, true));
            //  $result_array = array();
            return array('error' => 0, 'respuesta' => $result_array);
        } else {
            return 0;
        }
    }

    public function consultarPersonaDependencia($arr) {
        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';
        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

        foreach ($arr as $key => $value) {
            $vec[$i] = $value;
            $i++;
        }

        $expresion = "/^([0-9]+)$/i";
        if (!preg_match($expresion, $vec[0])) {
            return 1;
        }

        $query = "SELECT
                    p.id_personal,
                    t.cedula,
                    p.primer_nombre,
                    p.segundo_nombre,
                    p.primer_apellido,
                    p.segundo_apellido,
                    t.id_cargo,
                    c.descripcion_cargo as cargo,
                    d.nombre as oficina          
                 FROM
                    trabajador t  
                 INNER JOIN
                    personal p 
                       ON p.id_personal = t.id_personal  
                 INNER JOIN
                    cargo c 
                       on t.id_cargo = c.id_cargo  
                 INNER JOIN
                    dependencia d 
                       on d.id_dependencia = t.id_dependencia_real    
                 WHERE
                    t.id_dependencia_real = '" . $arr->cod_dependencia . "' 
                    AND  t.estatus = 'A'  
                 ORDER BY
                    t.cedula ASC";




        $result = pg_query($Dbconn, $query);
        if ($result) {
            while ($row = pg_fetch_row($result)) {
                $result_array[$row[1]] = $arr = array(
                    0 => utf8_encode($row[0]),
                    1 => utf8_encode($row[1]),
                    2 => utf8_encode($row[2]),
                    3 => utf8_encode($row[3]),
                    4 => utf8_encode($row[4]),
                    5 => utf8_encode($row[5]),
                    6 => utf8_encode($row[6]),
                    7 => utf8_encode($row[7]),
                    8 => utf8_encode($row[8]),
                );
            }


            return array('error' => 1, 'respuesta' => $result_array);
        } else {
            return 0;
        }
    }

    public function consultarPersonaDependenciaCargos($arr) {
        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';
        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;

        foreach ($arr as $key => $value) {
            $vec[$i] = $value;
            $i++;
        }

        $expresion = "/^([0-9]+)$/i";
        if (!preg_match($expresion, $vec[0])) {
            return 1;
        }

        $query = "SELECT
                    p.id_personal,
                    t.cedula,
                    p.primer_nombre,
                    p.segundo_nombre,
                    p.primer_apellido,
                    p.segundo_apellido,
                    t.id_cargo,
                    c.descripcion_cargo as cargo,
                    d.nombre as oficina          
                 FROM
                    trabajador t  
                 INNER JOIN
                    personal p 
                       ON p.id_personal = t.id_personal  
                 INNER JOIN
                    cargo c 
                       on t.id_cargo = c.id_cargo  
                 INNER JOIN
                    dependencia d 
                       on d.id_dependencia = t.id_dependencia_real    
                 WHERE
                    t.id_dependencia_real = '" . $arr->cod_dependencia . "' 
                    AND  t.estatus = 'A' AND
                    t.id_cargo in (13,16,17,57,58,15,12,56,14)
                 ORDER BY
                    t.cedula ASC";




        $result = pg_query($Dbconn, $query);
        if ($result) {
            while ($row = pg_fetch_row($result)) {
                $result_array[$row[1]] = $arr = array(
                    0 => utf8_encode($row[0]),
                    1 => utf8_encode($row[1]),
                    2 => utf8_encode($row[2]),
                    3 => utf8_encode($row[3]),
                    4 => utf8_encode($row[4]),
                    5 => utf8_encode($row[5]),
                    6 => utf8_encode($row[6]),
                    7 => utf8_encode($row[7]),
                    8 => utf8_encode($row[8]),
                );
            }


            return array('error' => 1, 'respuesta' => $result_array);
        } else {
            return 0;
        }
    }

    public function consultarPersonaIdPersonal($arr) {
        $User = 'consulta';
        $Passwd = 'consulta_mppeuct';
        $Db = 'sigefirrhh';
        $Port = '5432';
        $Host = '192.168.52.51';
        $Str_conn = "host=$Host port=$Port dbname=$Db user=$User password=$Passwd";
        $Dbconn = pg_connect($Str_conn) or die("Error de conexion. " . pg_last_error());
        $i = 0;


        foreach ($arr->cod_personal as $key => $value) {
            $arr->cod_personal[$key] = "'" . $value . "'";
        }

        $query = "SELECT
                    p.id_personal,
                    t.cedula,
                    p.primer_nombre,
                    p.segundo_nombre,
                    p.primer_apellido,
                    p.segundo_apellido,
                    t.id_cargo,
                    c.descripcion_cargo as cargo,
                    d.nombre as oficina          
                 FROM
                    trabajador t  
                 INNER JOIN
                    personal p 
                       ON p.id_personal = t.id_personal  
                 INNER JOIN
                    cargo c 
                       on t.id_cargo = c.id_cargo  
                 INNER JOIN
                    dependencia d 
                       on d.id_dependencia = t.id_dependencia_real    
                 WHERE
                    p.id_personal  IN (" . implode(',', $arr->cod_personal) . ")
                    AND  t.estatus = 'A'  
                 ORDER BY
                    t.cedula";

        $result = pg_query($Dbconn, $query);
        if ($result) {
            while ($row = pg_fetch_row($result)) {

                $result_array[$row[0]] = $arr = array(
                    0 => utf8_encode($row[0]),
                    1 => utf8_encode($row[1]),
                    2 => utf8_encode($row[2]),
                    3 => utf8_encode($row[3]),
                    4 => utf8_encode($row[4]),
                    5 => utf8_encode($row[5]),
                    6 => utf8_encode($row[6]),
                    7 => utf8_encode($row[7]),
                    8 => utf8_encode($row[8]),
                );
            }

            // error_log(print_r($result_array,true));

            return array('error' => 0, 'respuesta' => $result_array);
        } else {
            return 0;
        }
    }

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$url_wsdl = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . "/";
$wsdl = "personal.wsdl";
$soap_server = new SoapServer($url_wsdl . $wsdl);
$soap_server->setClass("Servicios", $soap_server);
$soap_server->handle();

#97
?>

