<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Elastix version 2.2.0-25                                             |
  | http://www.elastix.org                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
  +----------------------------------------------------------------------+
  | Cdla. Nueva Kennedy Calle E 222 y 9na. Este                          |
  | Telfs. 2283-268, 2294-440, 2284-356                                  |
  | Guayaquil - Ecuador                                                  |
  | http://www.palosanto.com                                             |
  +----------------------------------------------------------------------+
  | The contents of this file are subject to the General Public License  |
  | (GPL) Version 2 (the "License"); you may not use this file except in |
  | compliance with the License. You may obtain a copy of the License at |
  | http://www.opensource.org/licenses/gpl-license.php                   |
  |                                                                      |
  | Software distributed under the License is distributed on an "AS IS"  |
  | basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See  |
  | the License for the specific language governing rights and           |
  | limitations under the License.                                       |
  +----------------------------------------------------------------------+
  | The Original Code is: Elastix Open Source.                           |
  | The Initial Developer of the Original Code is PaloSanto Solutions    |
  +----------------------------------------------------------------------+
  $Id: index.php,v 1.1 2012-03-22 02:03:45 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";
require_once "libs/xajax/xajax.inc.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoInterfazdeAgente.class.php";
    require_once "modules/$module_name/libs/paloSantoDataFormList.class.php";

    //include file language agree to elastix configuration
    //if file language not exists, then include language by default (en)
    $lang=get_language();
    $base_dir=dirname($_SERVER['SCRIPT_FILENAME']);
    $lang_file="modules/$module_name/lang/$lang.lang";
    if (file_exists("$base_dir/$lang_file")) include_once "$lang_file";
    else include_once "modules/$module_name/lang/en.lang";

    //global variables
    global $arrConf;
    global $arrConfModule;
    global $arrLang;
    global $arrLangModule;
    $arrConf = array_merge($arrConf,$arrConfModule);
    $arrLang = array_merge($arrLang,$arrLangModule);

    //folder path for custom templates
    $templates_dir=(isset($arrConf['templates_dir']))?$arrConf['templates_dir']:'themes';
    $local_templates_dir="$base_dir/modules/$module_name/".$templates_dir.'/'.$arrConf['theme'];

    $script="<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"libs/js/jscalendar/calendar-win2k-2.css\" />
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/lang/calendar-en.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar-setup.js\"></script>";
    $smarty->assign("HEADER", $script);

    //conexion resource
    $pDB = new paloDB($arrConf['dsn_conn_database']);

    //actions
    $action = getAction();
    $content = "";

    if($action=='sacarpausa'){
	sacarPausa($_SESSION['elastix_user'],$pDB);
	$action = 'info_cliente';
        header("location: index.php?menu=hispana_interfaz_agente");
    }    

  /** 
    * CORRECTO:Primero valido si el usuario tiene campañas disponibles.
    */
    if(!validarUsuario($_SESSION['elastix_user'], $pDB)) $action = "deny";

    //Se añade para no pausar gestion con break id 6 (gestion de agendados)
    if(usuarioEnGestionAgendados($_SESSION['elastix_user'],$pDB)){
        $gestion_agendados="gestionando";
    }else
    if(usuarioEnPausa($_SESSION['elastix_user'], $pDB)) $action = "pausado";

    // si siguiente es si, procedo con el siguiente cliente
    // Al hacer unset, buscará otro cliente asociado a campaña.
    if(isset($_GET['siguiente']) && $_GET['siguiente']=="si") {
        unset($_SESSION['id_campania_cliente']);
        unset($_SESSION['id_campania_cliente_recargable']);
    }
    

    switch($action){
	case 'pausar':
	    pausar($_POST['id_break'],$_SESSION['elastix_user'],$pDB);	    
	case 'pausado':
	    $content = "El usuario <b>$_SESSION[elastix_user]</b> se encuentra en pausa, para regresar haga click <a href=index.php?menu=hispana_interfaz_agente&action=sacarpausa>aqui</a>.";	    
	    break;
	case 'gestionar':
	    $_SESSION['id_campania_cliente'] = $_GET['id_campania_cliente'];
            $_SESSION['id_campania_cliente_recargable'] = $_GET['id_campania_cliente_recargable'];
            $content = viewFormInterfazdeAgente($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
	    break;
	case 'deny': 
	    $content = "El usuario <b>$_SESSION[elastix_user]</b> no tiene campañas asignadas para hoy.";
	    break;
	case "llamar":
	    $_SESSION['action'] = 'llamar';
	    // Ver si se puede mejorar el IF a continuación
	    if(isset($_SESSION['ci']) && isset($_GET['id_campania']) && isset($_GET['id_campania_cliente']) && isset($_GET['telefono'])){
		$_SESSION['id_campania'] = $_GET['id_campania'];
		$_SESSION['id_campania_cliente'] = $_GET['id_campania_cliente'];
		$_SESSION['telefono'] = $_GET['telefono'];
	    }
	    
	    if(isset($_SESSION['id_campania_cliente'])){
		$arrValoresGestionAnterior = verificarGestionAnterior($_SESSION['id_campania_cliente'],$pDB);
	    }
            if(isset($_SESSION['id_campania_cliente_recargable'])){
		$arrValoresGestionAnterior = verificarGestionRecargableAnterior($_SESSION['id_campania_cliente_recargable'],$pDB);
	    }
	    /** Verificar si esto es útil. */
	    $formCampos = array(
		'form_nombre'    =>    array(
		    "LABEL"                => _tr("Form Name"),
		    "REQUIRED"               => "yes",
		    "INPUT_TYPE"             => "TEXT",
		    "INPUT_EXTRA_PARAM"      => array("size" => "40"),
		    "VALIDATION_TYPE"        => "text",
		    "VALIDATION_EXTRA_PARAM" => "",
		),	
		'form_description'    =>    array(
		    "LABEL"                => _tr("Form Description"),
		    "REQUIRED"               => "no",
		    "INPUT_TYPE"             => "TEXTAREA",
		    "INPUT_EXTRA_PARAM"      => "",
		    "VALIDATION_TYPE"        => "text",
		    "VALIDATION_EXTRA_PARAM" => "",
		    "COLS"                   => "33",
		    "ROWS"                   => "2",
		), 
	    );
	    $smarty->assign("type",_tr('Type'));    
	    $smarty->assign("select_type","type"); 
	    $smarty->assign("option_type",
	    array(
	    "VALUE" => array (
			"LABEL",
			"TEXT",
			"LIST",
			"DATE",
			"TEXTAREA"),
	    "NAME"  => array (
			_tr("Type Label"),
			_tr("Type Text"),
			_tr("Type List"),
			_tr("Type Date"),
			_tr("Type Text Area")),
	    "SELECTED" => "Text",     
		)
	    ); 
	    $smarty->assign("item_list",_tr('List Item'));    
	    $oForm = new paloForm($smarty, $formCampos); 
            $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
            if(isset($_SESSION['id_campania_cliente_recargable']))
                $arrInfoCliente = $pInterfazdeAgente->obtenerClientePorIdCampaniaClienteRecargable($_SESSION['id_campania_cliente_recargable']);
            else
                $arrInfoCliente = $pInterfazdeAgente->obtenerClientePorIdCampaniaCliente($_SESSION['id_campania_cliente']);
            //print_r($arrInfoCliente);
            $arrGestionesPrevias = $pInterfazdeAgente->getGestionesPrevias($_SESSION['ci'],$arrInfoCliente['id_campania_consolidada'],$arrInfoCliente['id_campania']);
            if(sizeof($arrGestionesPrevias)>0){
                    $smarty->assign("tieneGestionesPrevias", "si");
                    $smarty->assign("arrGestionesPrevias", $arrGestionesPrevias);
                }
	    $content = preview_form($pDB, $smarty, $module_name, $local_templates_dir, $formCampos, $oForm, $arrConf, $arrValoresGestionAnterior); 
	    break;
	case 'guardar':
	    if(isset($_POST) && $_POST['calltype']!=0){
		// Aqui mismo se guarda y agenda la gestión.
		guardarGestion($_POST,$pDB);
		// Se llama asi mismo.  
		// Header("Location: index.php?menu=hispana_interfaz_agente");
                //unset($_SESSION["id_campania_cliente_recargable"]);
                //unset($_SESSION["id_campania_cliente"]);
	    }else{
		$smarty->assign("mb_title", _tr("Error"));
		$smarty->assign("mb_message", _tr("Se ha intentado guardar gestión sin Call Type."));
	    }
	    /** No se pone break, porque despues de guardar debe mostrarse la info del cliente. */
	case 'info_cliente': // Obtiene y muestra información de un cliente (igual que el default)
	    /** No se pone break */
        default:
	    $_SESSION['action'] = 'info_cliente';
            $smarty->assign("gestion_agendados", $gestion_agendados);//Se añade para no pausar gestion en break "gestion de agendados"
            $smarty->assign("usuario_gestionados_hoy", usuarioGestionadosHoy($_SESSION['elastix_user'], $pDB));
            
            $content = viewFormInterfazdeAgente($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }

    /*
    echo "\$action:<br>";
    _pre($action);

    echo "\$_SESSION:<br>";
    _pre($_SESSION);
    */
    return $content;
}

function usuarioEnPausa($user, $pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    return $pInterfazdeAgente->usuarioEnPausa($user);
}
function usuarioGestionadosHoy($user, $pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    return $pInterfazdeAgente->usuarioGestionadosHoy($user);
}

function usuarioEnGestionAgendados($user, $pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    return $pInterfazdeAgente->usuarioEnGestionAgendados($user);
}

function sacarPausa($user, $pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $pInterfazdeAgente->sacarPausa($user);
}

function pausar($id_break,$user,$pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $pInterfazdeAgente->pausar($id_break,$user);
    header("location: index.php?menu=hispana_interfaz_agente");
}

function guardarGestion($DATA,$pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $pInterfazdeAgente->guardarGestion($DATA,$_SESSION['elastix_user']);
}

function verificarGestionAnterior($id_campania_cliente,$pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $arrValoresGestionAnterior = $pInterfazdeAgente->verificarGestionAnterior($id_campania_cliente);
    return $arrValoresGestionAnterior;
}

function verificarGestionRecargableAnterior($id_campania_cliente,$pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $arrValoresGestionAnterior = $pInterfazdeAgente->verificarGestionRecargableAnterior($id_campania_cliente);
    return $arrValoresGestionAnterior;
}

function preview_form($pDB, $smarty, $module_name, $local_templates_dir, $formCampos, $oForm, $arrConf, $arrValoresGestionAnterior) 
{
    require_once "/var/lib/asterisk/agi-bin/phpagi-asmanager.php";
    $oForm->setViewMode(); // Esto es para activar el modo "preview"

    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    
    $iNumForm = $pInterfazdeAgente->getForm($_SESSION['id_campania']);
    
    $pDBsqlite  = new paloDB("sqlite3:////var/www/db/acl.db");
    $pInterfazdeAgenteSqlite = new paloSantoInterfazdeAgente($pDBsqlite);
    $extensionAgente = $pInterfazdeAgenteSqlite->getExtensionAgente($_SESSION['elastix_user']);
    
    if(isset($_GET['telefono'])){ // si el telefono viene en la barra de direccion
	$destinatario = $_GET['telefono'];
    }

    if (!isset($iNumForm) || !is_numeric($iNumForm)) {
        return false;
    }
    $oDataForm = new paloSantoDataForm($pDB);
    $arrDataForm = $oDataForm->getFormularios($iNumForm);
    if(is_array($arrValoresGestionAnterior)){
	$arrFieldForm = $oDataForm->obtener_campos_formulario($iNumForm,NULL,$_SESSION['telefono'],$_SESSION, $arrValoresGestionAnterior); // envío arreglo $_GET
    }else{
	$arrFieldForm = $oDataForm->obtener_campos_formulario($iNumForm,NULL,$_SESSION['telefono'],$_SESSION); 
    }

    $smarty->assign("id_formulario_actual", $iNumForm);
    $smarty->assign("style_field","style='display:none;'");
    $smarty->assign("formulario",$arrFieldForm);

    if(isset($destinatario) && $destinatario !=""){ // si hay telefono seteado llamar al numero
	$astman = new AGI_AsteriskManager();
	if (!$astman->connect($arrConf['asterisk_host'],$arrConf['asterisk_user'], $arrConf['asterisk_pass'])) {
	    $smarty->assign("mb_title", _tr("Error"));
	    $smarty->assign("mb_message", _tr("No es posible conectarse a la PBX."));
	} else{
	    $result = $astman->Originate("Local/{$extensionAgente}@from-internal", $extensionAgente, "hispana-callcenter", 1, null, null, null, null, "DEST=$destinatario", null);
	}
    }
    
    $contenidoModulo=$oForm->fetchForm("$local_templates_dir/preview.tpl", _tr('Gestión de clientes')); // hay que pasar el arreglo    
    return $contenidoModulo;
}


function validarUsuario($usuario,&$pDB)
{
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    return $pInterfazdeAgente->validarUsuario($usuario);
}

function viewFormInterfazdeAgente($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{    
    $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
    $oForm = new paloForm($smarty,$arrFormInterfazdeAgente);

    //begin, Form data persistence to errors and other events.
    $_DATA  = $_POST;
    $action = getParameter("action");
    $id     = getParameter("id");
    $smarty->assign("ID", $id); //persistence id with input hidden in tpl

    if($action=="view")
        $oForm->setViewMode();
    else if($action=="view_edit" || getParameter("save_edit"))
        $oForm->setEditMode();
    //end, Form data persistence to errors and other events.

    if($action=="view" || $action=="view_edit"){ // the action is to view or view_edit.
        $dataInterfazdeAgente = $pInterfazdeAgente->getInterfazdeAgenteById($id);
        if(is_array($dataInterfazdeAgente) & count($dataInterfazdeAgente)>0)
            $_DATA = $dataInterfazdeAgente;
        else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pInterfazdeAgente->errMsg);
        }
    }

    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");
 
    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Información del cliente"), $_DATA);
    $content = mostrarDatosClienteHTML($smarty, $pInterfazdeAgente);
    return $content;
}

function getAction()
{
    if(getParameter("save_new")) //Get parameter by POST (submit)
        return "save_new";
    else if(getParameter("save_edit"))
        return "save_edit";
    else if(getParameter("delete")) 
        return "delete";
    else if(getParameter("new_open")) 
	return "view_form";
    else if(getParameter("action")=="view")      //Get parameter by GET (command pattern, links)
        return "view_form";
    else if(getParameter("action")=="view_edit")
        return "view_form";
    else if(getParameter("action")=="sacarpausa") 
	return "sacarpausa";
    else if(getParameter("action")=="Pausar") // Prioridad: Pausar
	return "pausar";
    else if(getParameter("action")=="guardar") // Prioridad: Guardar sobre llamar
	return "guardar";
    else if(getParameter("action")=="llamar" || $_SESSION['action']=='llamar')
	return "llamar";
    else if(getParameter("action")=="gestionar")
	return "gestionar";
    else if(getParameter("action")=="verDetalleGestion")
	return "verDetalleGestion";
    else
        return "info_cliente"; //cancel
}

function mostrarDatosClienteHTML($smarty, $pInterfazdeAgente)
{
    // liberar Clientes con status Display superior a los 15 minutos
    $pInterfazdeAgente->liberarClientesColgados(15);

    /*
    if(getParameter("action")=='gestionar'){
	$_SESSION['ci'] = getParameter('ci');
    }*/
    
    // El bug está cerca de arreglarse
    if((!isset($_SESSION['id_campania_cliente'])|| empty($_SESSION['id_campania_cliente']))&&(!isset($_SESSION['id_campania_cliente_recargable'])|| empty($_SESSION['id_campania_cliente_recargable']))){ // si no esta seteada busca otro cliente
	// echo "obtenerCliente<br>";
	$arrInfoCliente = $pInterfazdeAgente->obtenerCliente($_SESSION['elastix_user']);
	if(isset($arrInfoCliente['id_campania_cliente'])){
	    $_SESSION['id_campania_cliente'] = $arrInfoCliente['id_campania_cliente'];
	}elseif(isset($arrInfoCliente['id_campania_cliente_recargable'])){
	    $_SESSION['id_campania_cliente_recargable'] = $arrInfoCliente['id_campania_cliente_recargable'];
	}
    }elseif(isset($_SESSION['id_campania_cliente']) && $_SESSION['id_campania_cliente']!=""){
	// echo "obtenerClientePorIdCampaniaCliente<br>";
	$arrInfoCliente = $pInterfazdeAgente->obtenerClientePorIdCampaniaCliente($_SESSION['id_campania_cliente']);
        
    }elseif(isset($_SESSION['id_campania_cliente_recargable']) && $_SESSION['id_campania_cliente_recargable']!=""){
	// echo "obtenerClientePorIdCampaniaClienteRecargable<br>";
        //print_r($_SESSION);
	$arrInfoCliente = $pInterfazdeAgente->obtenerClientePorIdCampaniaClienteRecargable($_SESSION['id_campania_cliente_recargable']);
        
    }

    $numClientesAgendados = $pInterfazdeAgente->getNumClientesAgendados($_SESSION['elastix_user'], $arrInfoCliente['id_campania']);
    // Las gestiones previas deben ser de la campaña consolidada en general.
    $arrGestionesPrevias = $pInterfazdeAgente->getGestionesPrevias($arrInfoCliente['ci'],$arrInfoCliente['id_campania_consolidada'],$arrInfoCliente['id_campania']);
    
    if(!$arrInfoCliente){ // No hay datos de cliente
	return "<b>No hay clientes disponibles para hacer gestión.<br>Consulte al supervisor.</b>";
    }else{ // Si hay datos del cliente .
	if(count($arrInfoCliente['telefono']>0)){
	    $tieneTelefonos = true;
	}elseif(count($arrInfoCliente['telefono']==0)){
	    $tieneTelefonos = false;
	}
	$_SESSION['ci'] = $arrInfoCliente['ci'];
	$smarty->assign("arrInfoCliente",    $arrInfoCliente);
	$smarty->assign("cantidadTelefonos", sizeof($arrInfoCliente['telefono']));
	$smarty->assign("tieneTelefonos",    $tieneTelefonos);
	$smarty->assign("clientesAgendados", $numClientesAgendados);
	$smarty->assign("LISTA_BREAKS", $pInterfazdeAgente->listarBreaks());

	if(sizeof($arrGestionesPrevias)>0){
	    $smarty->assign("tieneGestionesPrevias", "si");
	    $smarty->assign("arrGestionesPrevias", $arrGestionesPrevias);
	}

	// _pre($arrInfoCliente);
	if($arrInfoCliente['segundos_faltantes']<0){ // estos son los segundos faltantes para agendamiento.
	    $smarty->assign("fecha_agendamiento", $arrInfoCliente['fecha_agendamiento']);
	}else{ // si no esta agendado, podria eventualmente ver otro cliente.
	    if($pInterfazdeAgente->verOtroCliente($arrInfoCliente)){
		$smarty->assign("siguiente", "si");
	    }else{
		$smarty->assign("siguiente", "no");
	    }
	}

	$content = $smarty->fetch("file:/var/www/html/modules/hispana_interfaz_agente/themes/default/interfaz_agente_inicio.tpl");
	return $content;
    }    
}

function _pre($Array)
{
  echo "<pre>";
  print_r($Array);
  echo "</pre>";
}
?>