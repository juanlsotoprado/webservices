<?php
print_r($_SERVER);

class x{
	function obtener_ip(){
		if($_SERVER['HTTP_X_FORWARDED_FOR'] != ""){
			
			$this->ip_proxy = $_SERVER['REMOTE_ADDR'];
			$cliente_ip = $this->ip_publica = $_SERVER['HTTP_X_FORWARDED_FOR']; 
			//(!empty($_SERVER['REMOTE_ADDR']))?($_SERVER['REMOTE_ADDR']):((!empty($_ENV['REMOTE_ADDR']))?($_ENV['REMOTE_ADDR']):"Desconocida");
			$entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
			reset($entries);
			while (list(, $entry) = each($entries)){
				echo "<br>->".$entry = trim($entry);
				if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
					$private_ip = array('/^0\./','/^127\.0\.0\.1/','/^192\.168\..*/','/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/','/^10\..*/');
					$found_ip = preg_replace($private_ip, $cliente_ip, $ip_list[1]);
					if ($cliente_ip != $found_ip){
						$cliente_ip = $found_ip;
						break;
					}//end if
				}//end if
			}//end while
		}else{
			$this->ip_publica = $cliente_ip = (!empty($_SERVER['REMOTE_ADDR']))?($_SERVER['REMOTE_ADDR']):((!empty($_ENV['REMOTE_ADDR']))?($_ENV['REMOTE_ADDR']):"Desconocida");
		}//end if
		$this->ip_cliente =  $cliente_ip;
		return $cliente_ip;
	}//end Function
	
	    function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
           
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
       
        return $_SERVER['REMOTE_ADDR'];
    }
    
	function get_real_ip(){
 
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
        {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
        {
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"]))
        {
            return $_SERVER["HTTP_FORWARDED"];
        }
        else
        {
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    
}
$ip = new x();
echo $ip->obtener_ip()."->".$x->ip_publica;
echo "<br>".$ip->getRealIP();
echo "<br>".$ip->get_real_ip();
echo "<br>->".$_SERVER['REMOTE_USER'];
?>
