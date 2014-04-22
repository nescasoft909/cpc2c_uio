<?php
include_once "/var/www/html/libs/paloSantoDB.class.php";
include_once "/var/www/html/modules/hispana_campania_derivada/configs/default.conf.php";
include_once "/var/www/html/modules/hispana_campania_derivada/libs/paloSantoCampaniaDerivada.class.php";
include_once "/var/www/html/modules/hispana_campania_derivada/libs/AppLogger.class.php";

$_log = new AppLogger();
$_log->open("/var/www/html/modules/hispana_campania_derivada/_log.log");
$_log->prefijo("ajax_process");
$_log->output("POST:" . print_r($_POST,true));

if(isset($_POST['action'])) {
$pDB = new paloDB($arrConfModule['dsn_conn_database']);
$pCampaniaDerivada = new paloSantoCampaniaDerivada($pDB);

    switch($_POST['action']) {
	case 'getBases':	    

	    $arrBases = $pCampaniaDerivada->getBases($_POST['campania']);			
	    // $_log->output("Bases:\n" . print_r($arrBases,true));

	    if(sizeof($arrBases)>0){
		$content = crearOption($arrBases);
		$_log->output("Content: " . $content);
	    }else{		
		$content = '<option id=0>No hay bases</option>.';
	    }	    
	    break;

	case 'getCalltypes':	    
	    $arrCalltypes = $pCampaniaDerivada->getCalltypes($_POST['campania']);				    

	    if(sizeof($arrCalltypes)>0){
		$content = crearOption($arrCalltypes);
		// $_log->output("Content: " . $content);
	    }else{
		$content = '<option id=0>No hay calltypes</option>.';
	    }
	    break;

	case 'getData':	    
	    $numClientes = $pCampaniaDerivada->obtenerClientes($_POST['campania'], $_POST['values_calltypes'], $_POST['values_bases'], true);				    
	    $_log->output("Clientes: " . print_r($numClientes,true));
	    $content = $numClientes;
	    break;
	}
	echo $content;
	return $content;
}

/**
Úses para llenar select input
*/
function crearOption($arrOptions)
{
    $html_output = "";
    if(is_array($arrOptions)){
	foreach($arrOptions as $key=>$value){
	    $html_output .= "<option value='{$key}'>{$value}</option>\n";
	    }
    }
    return $html_output;
}

/**
Úsese sólo para llenar textarea.
*/
function createTextList($array)
{
  $data = "";
  foreach($array as $val){
      $data .= $val . "\n";
  }
  return $data;
}


?>
