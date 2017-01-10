<?php
ini_set("soap.wsdl_cache_enabled", "0");
class Funciones {

    public static function Extraer_correos_masivos($limite) {
        $conexion = new Conexion();
        $fecha = date('Y-m-d');
        $rs = array();
        $query = "select c.*,
                         m.mensaje,
                         m.asunto
            
            
                    from correos c
                    inner join mensaje m on(c.id_mensaje = m.id)
                    
                where 
                    c.fecha_envio <= '" . $fecha . "' and
                    c.fecha_enviado is null and

                    c.estatus = true 
                    
                    
                 limit $limite";

        //error_log(print_r($query, true));
        //return $query;
        $resultado = $conexion->ejecutarConsulta($query, Conexion::$MM);
        $rs = array();
        //return $query;

        while ($row = $conexion->descomponerFilaAssoc($resultado)) {
            $rs[$row['id']] = $row;
        }


        return $rs;
    }

    public static function Extraer_sms_masivos($limite) {
        $conexion = new Conexion();
        $fecha = date('Y-m-d');
        $rs = array();
        $query = "select s.*,
                         m.mensaje,
                         m.asunto
            
                    from sms s
                    inner join mensaje m on(s.id_mensaje = m.id)
                    
                where 
                    s.fecha_envio <= '" . $fecha . "' and
                    s.fecha_enviado is null and
                    s.estatus = true 
                    
                    
                 limit $limite";

       // error_log(print_r($query, true));
        //return $query;
        $resultado = $conexion->ejecutarConsulta($query, Conexion::$MM);
        $rs = array();
        //return $query;

        while ($row = $conexion->descomponerFilaAssoc($resultado)) {
            $rs[$row['id']] = $row;
        }


        return $rs;
    }
    

    public static function enviar_correo_masivo($nom, $dir, $asunto, $mensaje, $id) {
        $conexion = new Conexion();

        $mensaje = "<div style=\"top:0px:left:0px;text-align: center\"><img height=\"60px\" width=\"100%\" src=\"http://apis.mppeuct.gob.ve/img/comun/normativa.png\"></div>" . $mensaje;
        $params = array(
            'nombre' => $nom,
            'correo_remitente' => "no-responder@mppeuct.gob.ve",
            'correo_destinatario' => $dir,
            'asunto' => $asunto,
            'mensaje' => $mensaje,
            'HTML' => $mensaje
        );




        $client = new SoapClient("http://webservices.mppeuct.gob.ve/correo/correo.wsdl", array());
        $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/correo/schema.xsd");
        $objeto = $client->enviarCorreo(new SoapParam($soapstruct, "message"));

        if ($objeto == 1) {

            $query = "update correos set fecha_enviado=now() where id=" . $id;

          //  error_log($query);
            
            $resultado = $conexion->ejecutarConsulta($query, Conexion::$MM);


            return true;
        } else {

            return false;
        };
    }

    public static function enviar_sms_masivo($numero, $mensaje,$id) {
        $conexion = new Conexion();

        try {

            $options = array();
#(numero,mensaje,origen)
            $params = array('numero' => $numero, 'mensaje' => $mensaje, 'origen' => '', 'uid' => '');
            $client = new SoapClient("http://webservices.mppeuct.gob.ve/sms/sms.wsdl", $options);
            $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/sms/schema.xsd");
            $objeto = $client->enviarSms(new SoapParam($soapstruct, "message"));

            if ($objeto == 1) {

                $query = "update sms set fecha_enviado=now() where id=" . $id;

                $resultado = $conexion->ejecutarConsulta($query, Conexion::$MM);


                return true;
            } else {

                return false;
            };
        } catch (SoapFault $e) {
            error_log($e->getMessage());
            return 0;
        }
    }



}
