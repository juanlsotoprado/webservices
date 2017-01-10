   public static function Get_proveedor_ws() {

        ini_set("soap.wsdl_cache_enabled", "0");

        try {

            $params = array('cod' => 0);
//    $client = new SoapClient("http://webservices.mppeuct.gob.ve/personal/personal.wsdl", array());
//    $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/personal/schema.xsd");

            $client = new SoapClient("http://webservices.mppeuct.gob.ve/proveedor/proveedor.wsdl", array());
            $soapstruct = new SoapVar($params, SOAP_ENC_OBJECT, "params", "http://webservices.mppeuct.gob.ve/proveedor/schema.xsd");


            $objeto = $client->consultarProveedor(new SoapParam($soapstruct, "message"));


            // error_log(print_r($objeto, true));

            $objeto['respuesta']["99"]['cod_pro'] = "99";
            $objeto['respuesta']["99"]['nompro'] = "XXX";

            // error_log(print_r($objeto, true));

            return $objeto;
        } catch (SoapFault $exp) {

            // error_log(print_r($exp->getMessage()). " error al conectarse");
            return FALSE;
        }
    }

