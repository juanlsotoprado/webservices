<?php

error_reporting(E_ALL);
ini_set("display_errors", "Off");


$url = $_GET['x'];

if (!empty($_GET['a'])) {
    
    $a = $_GET['a'];
}else{
    
    $a = "A4"; 
    
}
if (!empty($_GET['al'])) {
    
    $al = $_GET['al'];
}else{
    
    $al = "P"; 
    
}

if (!empty($_GET['x'])) {
    $lugar = "";
    if (!empty($_GET['l'])) {

        $url = $url . ".html";

        switch ($_GET['l']) {
            case 'fti':

                break;
            case 'sed':
                
               if (!empty($_GET['dir'])) {
                   
                 $lugar = $_GET['dir']."/includes/docs_pdf/doc/";
                   
               }else{
                   
                   $lugar = "http://sed.mppeuct.gob.ve/includes/docs_pdf/doc/";

               }
               
                //correos a los que les llegara el error;
                $_GET['c'] = "jsoto@mppeuct.gob.ve";

                break;
            case 'prueba':
                $lugar = "http://172.17.90.49/proyectoreporte/generarReporte/doc/";

                break;
        }
    }

//error_log($lugar.$url);
    

    if ($homepage = file_get_contents($lugar . $url)) {
        ob_start();
        echo $homepage;

        $content = ob_get_clean();

        require_once('lib/html2pdf/html2pdf.class.php');
        try {

            $html2pdf = new HTML2PDF($al, $a, 'es', true, 'UTF-8', 0);
            $html2pdf->writeHTML($content);
            $html2pdf->Output('doc.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    } else {

        echo "<h1>No se encontraron documentros.....</h1>";
    };
} else {


    echo "<h1>No se encontraron documentros.....</h1>";
    die;
}
?>
