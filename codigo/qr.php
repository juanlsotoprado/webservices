<?php
include_once '../lib/phpqrcode/qrlib.php';
if(array_key_exists('c', $_GET)){
	if(trim($_GET['c'])!=''){
		QRcode::png($_GET['c']);
	}
}
