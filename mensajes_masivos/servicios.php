<?php

require_once("../conexion/conexion.class.php");
require_once("funciones.class.php");
require_once('../cls/cls_funciones.php');

class Servicios extends Conexion {

    private $_SOAPSERVER = NULL;
    private $_HEADERVARS = "";
    private $_params = array();

    public function __construct($soapServer_resource) {
        $funciones = new cls_funciones();
        $this->_SOAPSERVER = $soapServer_resource;
    }

    function mensajesMasivosCorreo($parametros) {
        $limite = $parametros->limite;
        if ($limite != '') {//validación de parametro no vacío
            if (is_numeric($limite)) {//validación de parametro númerico
                try {
                    $respuesta = Funciones::Extraer_correos_masivos($limite);

                    if ($respuesta) {
                        $enviados = 0;
                        $errores = 0;
                        $email_completos = "";
                        $id_actualizados = array();
                        $id_no_actualizados = array();

                        foreach ($respuesta as $key => $value) {

                            $resp = Funciones::enviar_correo_masivo("MPPEUCT(notificación)", $value['correo'], $value['asunto'], $value['mensaje'], $key);

                            if ($resp) {
                                $id_actualizados[] = $key;
                            } else {

                                $id_no_actualizados[] = $key;
                            }
                        }

                        //Funciones::registrar_correo_enviado($enviados, $errores);
                        // $respuesta='[EXITO]:	NOTIFICACIONES ENVIADAS: '.($enviados).' <br> '.$email_completos;
                    } else {
                        return array('error' => 5, 'respuesta' => array()); //no hay notificaciones que enviar
                    }
                } catch (Exception $e) {
                    return array('error' => 3, 'respuesta' => array()); //error de procesamiento_interno
                }
            } else {
                return array('error' => 2, 'respuesta' => array()); //los valores no se recibieron correctamente
            }
        } else {
            return array('error' => 1, 'respuesta' => array()); //no se esta pasando el parametro requerido
        }

        return array('error' => 0, 'respuesta' => array('id_correo_actualizado' => $id_actualizados, 'id_correo_no_actualizado' => $id_no_actualizados));
    }

    function mensajesMasivosSms($parametros) {

        error_log(print_r("llego", true));
        $limite = $parametros->limite;
        if ($limite != '') {//validación de parametro no vacío
            if (is_numeric($limite)) {//validación de parametro númerico
                try {
                    $respuesta = Funciones::Extraer_sms_masivos($limite);

                    // error_log(print_r($respuesta,true));
                    if ($respuesta) {
                        $enviados = 0;
                        $errores = 0;
                        $email_completos = "";
                        $id_actualizados = array();
                        $id_no_actualizados = array();

                        foreach ($respuesta as $key => $value) {


                            $resp = Funciones::enviar_sms_masivo("0" . $value['tlf'], $value['mensaje'], $key);

                            if ($resp) {
                                $id_actualizados[] = $key;
                            } else {

                                $id_no_actualizados[] = $key;
                            }
                        }

                        //Funciones::registrar_correo_enviado($enviados, $errores);
                        // $respuesta='[EXITO]:	NOTIFICACIONES ENVIADAS: '.($enviados).' <br> '.$email_completos;
                    } else {
                        return array('error' => 5, 'respuesta' => array()); //no hay notificaciones que enviar
                    }
                } catch (Exception $e) {
                    return array('error' => 3, 'respuesta' => array()); //error de procesamiento_interno
                }
            } else {
                return array('error' => 2, 'respuesta' => array()); //los valores no se recibieron correctamente
            }
        } else {
            return array('error' => 1, 'respuesta' => array()); //no se esta pasando el parametro requerido
        }

        return array('error' => 0, 'respuesta' => array('id_correo_actualizado' => $id_actualizados, 'id_correo_no_actualizado' => $id_no_actualizados));
        //     return array('error' => 0, 'respuesta' => $respuesta);
    }

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer('http://172.17.90.49/webservices/mensajes_masivos/mensajes_masivos.wsdl');
$server->setClass('Servicios');
$server->handle();
?>
