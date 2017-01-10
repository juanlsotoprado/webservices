var cn_ajax = null;

function crearXMLHttpRequest(){
	var xmlHttp=null;
	if (window.ActiveXObject) 
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	else 
	if (window.XMLHttpRequest) 
		xmlHttp = new XMLHttpRequest();
	return xmlHttp;
}//end function

function getXMLHTTPRequest() {
	try {
		req = new crearXMLHttpRequest();
	} catch(err1) {
		try {
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (err2) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (err3) {
				req = false;
			}
		}
	}
	return req;
}

function limitText(limitField, limitNum) {
	var limitCount = document.getElementById('countdown');
	//alert(limitField.value.length)
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.innerHTML = limitNum - limitField.value.length;
	}
}
function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}
function mueveReloj(){
    momentoActual = new Date()
    hora = momentoActual.getHours()
    minuto = momentoActual.getMinutes()
    horaImprimible = pad(hora,2) + " : " + pad(minuto,2);
    document.getElementById('reloj').innerHTML = horaImprimible
}

var ctr_act = new Array();
	
function solo_enteros(e,obj,tit,msj){
	var keynum = window.event ? window.event.keyCode : e.which;

	if((keynum < 48) || (keynum > 57)){
		//obj.className = 'obligatorio';
		mostrar_popover(obj.id,tit,msj);
	}else{
		//obj.className = '';
		ocultar_popover(obj.id);
	}
	if (keynum == 0){
		ocultar_popover(obj.id);
		return true;	
	}
	if (keynum == 8){
		ocultar_popover(obj.id);
		return true;	
	}
	return /\d/.test(String.fromCharCode(keynum));
}
function mostrar_popover(id,titulo,mensaje){
	$('#'+id).popover({
		title : titulo,
		content: mensaje,
		placement: 'right',
	});	
	$('#'+id).popover('show');
}

function ocultar_popover(id){
	$('#'+id).popover('destroy');
	//$('#'+id).removeClass();
}
function ocultar_popover(id){
	$('#'+id).popover('destroy');
	//$('#'+id).removeClass();
}
function enviar_mensaje(){
	var numero = $('#num').val();
	if($('#num').val()==''){
		$(".modal-title").html('<b>Envío de mensaje</b>');
		$(".modal-body").html('<div class="alert alert-warning"><strong>Debe ingresar el número del móvil</strong></div>');
		$("#myModal").modal();
		return false;
	}
	if($('#texto').val()==''){
		$(".modal-title").html('<b>Envío de mensaje</b>');
		$(".modal-body").html('<div class="alert alert-warning"><strong>Debe ingresar el mensaje</strong></div>');
		$("#myModal").modal();
		return false;
	}
	var res = numero.substring(0, 4);
	if(res!='0412' && res!='0414' && res!='0424' && res!='0426' && res!='0416'){
		alert('Sólo está permitido el ingreso de números móviles (0412,0414,0416,0424,0426)')
		return false;
	}
	var cad_x = 'num='+encodeURIComponent($('#num').val())+'&msj='+encodeURIComponent($('#texto').val());
	cn_ajax= crearXMLHttpRequest();
	cn_ajax.onreadystatechange = procesar_envio;
	cn_ajax.open("POST","ctrl.php",true);
	cn_ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	cn_ajax.send(cad_x);
}
function procesar_envio(){
	if(cn_ajax.readyState == 4){
		if(cn_ajax.responseText==1){
			$(".modal-title").html('<b>Envío de mensaje</b>');
			$(".modal-body").html('<div class="alert alert-success"><strong>Mensaje enviado con éxito</strong></div>');
			$("#myModal").modal();
			$('#num').val('');
			$('#texto').val('');
			$('#countdown').html('140');
		}else{

		}
	}else{

	}//end if
}//end function
