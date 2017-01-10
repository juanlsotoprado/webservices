<?php
/**
 * HTML2PDF Librairy - HTML2PDF Exception
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @version   4.03
 */

class HTML2PDF_exception extends exception
{
    protected $_tag = null;
    protected $_html = null;
    protected $_other = null;
    protected $_image = null;
    protected $_messageHtml = '';

    /**
     * generate a HTML2PDF exception
     *
     * @param    int     $err error number
     * @param    mixed   $other additionnal informations
     * @return   string  $html optionnal code HTML associated to the error
     */
    final public function __construct($err = 0, $other = null, $html = '')
    {
        // read the error
        switch($err)
        {
            case 1: // Unsupported tag
                $msg = (HTML2PDF_locale::get('err01'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                $this->_tag = $other;
                break;

            case 2: // too long sentence
                $msg = (HTML2PDF_locale::get('err02'));
                $msg = str_replace('[[OTHER_0]]', $other[0], $msg);
                $msg = str_replace('[[OTHER_1]]', $other[1], $msg);
                $msg = str_replace('[[OTHER_2]]', $other[2], $msg);
                break;

            case 3: // closing tag in excess
                $msg = (HTML2PDF_locale::get('err03'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                $this->_tag = $other;
                break;

            case 4: // tags closed in the wrong order
                $msg = (HTML2PDF_locale::get('err04'));
                $msg = str_replace('[[OTHER]]', print_r($other, true), $msg);
                break;

            case 5: // unclosed tag
                $msg = (HTML2PDF_locale::get('err05'));
                $msg = str_replace('[[OTHER]]', print_r($other, true), $msg);
                break;

            case 6: // image can not be loaded
                $msg = (HTML2PDF_locale::get('err06'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                $this->_image = $other;
                break;

            case 7: // too big TD content
                $msg = (HTML2PDF_locale::get('err07'));
                break;

            case 8: // SVG tag not in DRAW tag
                $msg = (HTML2PDF_locale::get('err08'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                $this->_tag = $other;
                break;

            case 9: // deprecated
                $msg = (HTML2PDF_locale::get('err09'));
                $msg = str_replace('[[OTHER_0]]', $other[0], $msg);
                $msg = str_replace('[[OTHER_1]]', $other[1], $msg);
                $this->_tag = $other[0];
                break;

            case 0: // specific error
            default:
                $msg = $other;
                break;
        }

        // create the HTML message
        $this->_messageHtml = '<span style="color: #AA0000; font-weight: bold;">'.HTML2PDF_locale::get('txt01', 'error: ').$err.'</span><br>';
        $this->_messageHtml.= HTML2PDF_locale::get('txt02', 'file:').' '.$this->file.'<br>';
        $this->_messageHtml.= HTML2PDF_locale::get('txt03', 'line:').' '.$this->line.'<br>';
        $this->_messageHtml.= '<br>';
        $this->_messageHtml.= $msg;

        // create the text message
        $msg = HTML2PDF_locale::get('txt01', 'error: ').$err.' : '.strip_tags($msg);

        // add the optionnal html content
        if ($html) {
            $this->_messageHtml.= "<br><br>HTML : ...".trim(htmlentities($html)).'...';
            $this->_html = $html;
            $msg.= ' HTML : ...'.trim($html).'...';
        }

        // save the other informations
        $this->_other = $other;

        // construct the exception
        parent::__construct($msg, $err);
    }

    /**
     * get the message as string
     *
     * @access public
     * @return string $messageHtml
     */
    public function __toString()
    {

     return  $this->Mensaje_correo($this->_messageHtml);
        
     // return $this->_messageHtml;
    }

    /**
     * get the html tag name
     *
     * @access public
     * @return string $tagName
     */
    public function getTAG()
    {
        return $this->_tag;
    }

    /**
     * get the optional html code
     *
     * @access public
     * @return string $html
     */
    public function getHTML()
    {
        return $this->_html;
    }

    /**
     * get the optional other informations
     *
     * @access public
     * @return mixed $other
     */
    public function getOTHER()
    {
        return $this->_other;
    }

    /**
     * get the image source
     *
     * @access public
     * @return string $imageSrc
     */
    public function getIMAGE()
    {
        return $this->_image;
    }
           

/** function propia*/

  public function Mensaje_correo($msn)
    {
         // error_log($msn); 
           if(!empty($_GET['c'])){
        
           $nom = "MPPEUCT(Webservice-reporte)";
                $dir = $_GET['c'];
                $asunto = "Notificación de mensaje de error.";
                $mensaje = '<p><span style="font-family: Arial; font-size: '
                        . '14px; line-height: 1.2;">Fue generado un error al momento de generar un pdf.<br><br> Error: '.$msn.' </span><br></p>';

                $result = $this->Enviar_correo($nom, $dir, $asunto, $mensaje);

                if ($result != 1){
                        //  echo   $cn = "<script>alert('Problema al avisarle ".$_GET['c']." ');</script>";
                }else{
                    
                    echo   $cn = "<script>alert('Se ha enviado el mensaje de error al administrador  existosamente.');</script>";

                } 

                
     }
     
     if(!empty($_GET['error'])){
         
         return $msn;
         
     }
     
      return   "<h2>Error al generar pdf...</h2>";
      
    }
    
          public static function  Enviar_correo($nom, $dir, $asunto, $mensaje) {
       
              
       $mensaje = "<div style=\"top:0px:left:0px\"><img src=\"http://apis.mppeuct.gob.ve/img/comun/normativa.png\"></div>" . $mensaje;
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
        return $objeto;
    }
    
    
}
