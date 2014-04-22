<?php
include_once "/var/www/html/libs/paloSantoDB.class.php";
include_once "/var/www/html/modules/hispana_clientes_manuales/configs/default.conf.php";
include_once "/var/www/html/modules/hispana_clientes_manuales/libs/paloSantoClientesManuales.class.php";

/* Just for test
$_POST['action'] = 'mostrarAgentes';
$_POST['clase'] = 1;*/

if(isset($_POST['action'])) {
$pDB = new paloDB($arrConfModule['dsn_conn_database']);
$pClientes = new paloSantoClientesManuales($pDB);

	switch($_POST['action']) {
		case 'mostrarAgentes':
			if(!isset($_POST['clase']) || empty($_POST['clase'])) {
				$content = "<option value=0>Seleccione una campaña</option>";
				break;
			}

			$arrOptions = $pClientes->getAgentesCampania($_POST['clase']);
			
			if(sizeof($arrOptions)>0)
			    $content = crearOption($arrOptions);
			else
			    $content = "";
			break;
		default:
			$content = 'vacio';
			break;
	}
	echo $content;
	return $content;
}

function crearOption($arrOptions){
	$html_output = "";
	if(is_array($arrOptions)){
		foreach($arrOptions as $key=>$value){
			$html_output .= "<option value='{$key}'>{$value}</option>\n";
		}
	}
	return $html_output;
}
?>
