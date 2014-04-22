<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Elastix version 2.2.0-25                                               |
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
  $Id: index.php,v 1.1 2012-04-28 10:04:22 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";
include_once "/var/www/html/libs/paloSantoACL.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoDatosbasicos.class.php";

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

    //conexion resource
    $pDB = new paloDB($arrConf['dsn_conn_database']);
    
    //actions
    $action = getAction();
    $content = "";
    
    if (getParameter("msg")=="ok"){
        $smarty->assign("mb_message", "Actualización exitosa");
    }

    if(isset($_POST['new'])){
	unset($_SESSION['ci']);
	unset($_POST);
    }

    switch($action){
        case "save_new":
	    guardarDatosCliente($_POST,$_SESSION['elastix_user'],$pDB);
            // $content = saveNewDatosbasicos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
	    $content = viewFormDatosbasicos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;

	case "save_edit":
	    if(!actualizarDatosCliente($_POST,$_SESSION['elastix_user'],$pDB))
            {
                $smarty->assign("mb_message", "Error, Ya existe un cliente con esa cedula registrado en el sistema.");
            }else{
                if($_POST["id_cliente"]){
                    header('Location: index.php?menu=hispana_clientes_campania_r'); 
                    
                }
                $smarty->assign("mb_message", "Actualización exitosa");
            }
            
            $content = viewFormDatosbasicos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
	    break;

        default: // view_form
            $content = viewFormDatosbasicos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function guardarDatosCliente($DATA,$user,$pDB)
{
    $pDatosbasicos = new paloSantoDatosbasicos($pDB);

    if($pDatosbasicos->guardarDatosCliente($DATA,$user)){
	$_SESSION['ci'] = $DATA['ci_input'];
    }
}

function actualizarDatosCliente($DATA,$user,$pDB)
{
    $pDatosbasicos = new paloSantoDatosbasicos($pDB);
    if ((isset($DATA["id_campania_cliente_recargable"])&&!empty($DATA["id_campania_cliente_recargable"]))
       ||(isset($DATA["id_cliente"])&&!empty($DATA["id_cliente"]))){
        if($pDatosbasicos->actualizarDatosClienteRecargable($DATA,$user)){
            return true;
        }else{
            return false;
        }
    }elseif (isset($DATA["cedula"])){
        if($pDatosbasicos->actualizarDatosClienteFull($DATA,$user)){
            return true;
        }else{
            return false;
        }
    }else{
        $pDatosbasicos->actualizarDatosCliente($DATA,$user);
    }
    return true;
}



function viewFormDatosbasicos($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pDatosbasicos = new paloSantoDatosbasicos($pDB);
    $arrFormDatosbasicos = createFieldForm();
    $oForm = new paloForm($smarty,$arrFormDatosbasicos);

    $pDBSqlite = new paloDB("sqlite3:////var/www/db/acl.db");
    $pACL = new paloACL($pDBSqlite);

    if($pACL->isUserAdministratorGroup($_SESSION['elastix_user'])){
	$smarty->assign("esAdmin", true);
    }else{
	$smarty->assign("esAdmin", false);
    }

    //begin, Form data persistence to errors and other events.
    $_DATA  = $_POST;
    $action = getParameter("action");
    if(isset($_SESSION['ci'])){ // si esta seteado la cedula en la session, procedo a editar
	$action = "view_edit";
    }

    $id     = getParameter("id");
    $smarty->assign("ID", $id); //persistence id with input hidden in tpl
    if($_GET["ci"]){
        $smarty->assign("CI", getParameter("ci")); //persistence ci with input hidden in tpl
        $smarty->assign("action_edit", "yes"); //persistence ci with input hidden in tpl
        $_SESSION["ci"]=getParameter("ci");
    }else{
        $smarty->assign("CI", $_SESSION['ci']); //persistence ci with input hidden in tpl
    }
    
    //Clientes Recargables
    if($_GET["id_cliente"]){
        $dataDatosbasicos = $pDatosbasicos->getDatosbasicosByIdCliente(getParameter("id_cliente")); 
        $smarty->assign("CI", $dataDatosbasicos["ci"]); //persistence ci with input hidden in tpl
        $smarty->assign("action_edit", "yes"); //persistence ci with input hidden in tpl
        $_SESSION["id_cliente"]=getParameter("id_cliente");
        $_SESSION["ci"]= $dataDatosbasicos["ci"];
    }
    
    
    if($action=="view")
        $oForm->setViewMode();
    else if($action=="view_edit" || getParameter("save_edit"))
        $oForm->setEditMode();
    //end, Form data persistence to errors and other events.
    if($action=="view" || $action=="view_edit"){ // the action is to view or view_edit.
        // $dataDatosbasicos = $pDatosbasicos->getDatosbasicosById($id); // Cambiado para editar al cliente de la Sesión
        //if(!empty($_SESSION["id_campania_cliente_recargable"])){
        //    $dataDatosbasicos = $pDatosbasicos->getDatosbasicosByIdCampaniaRecargable($_SESSION['id_campania_cliente_recargable']); 
        //}else{
            $dataDatosbasicos = $pDatosbasicos->getDatosbasicosByCI($_SESSION['ci']); 
        //}
            
            if($_GET["id_cliente"]){
                $dataDatosbasicos = $pDatosbasicos->getDatosbasicosByIdCliente($_SESSION['id_cliente']); 
                $dataDatosbasicos["cedula"]=$dataDatosbasicos["ci"];
            }
        if(is_array($dataDatosbasicos) & count($dataDatosbasicos)>0)
            $_DATA = $dataDatosbasicos;	    
        else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pDatosbasicos->errMsg);
        }
    }
    if(empty($_DATA["cedula"]))
$_DATA["cedula"]=getParameter("ci");
    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");
    $smarty->assign("IMG", "images/list.png");

    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Datos básicos"), $_DATA);
    $content = "<form  method='POST' style='margin-bottom:0;' action='?menu=$module_name'>".$htmlForm."</form>";

    return $content;
}

function saveNewDatosbasicos($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pDatosbasicos = new paloSantoDatosbasicos($pDB);
    $arrFormDatosbasicos = createFieldForm();
    $oForm = new paloForm($smarty,$arrFormDatosbasicos);

    if(!$oForm->validateForm($_POST)){
        // Validation basic, not empty and VALIDATION_TYPE 
        $smarty->assign("mb_title", _tr("Validation Error"));
        $arrErrores = $oForm->arrErroresValidacion;
        $strErrorMsg = "<b>"._tr("The following fields contain errors").":</b><br/>";
        if(is_array($arrErrores) && count($arrErrores) > 0){
            foreach($arrErrores as $k=>$v)
                $strErrorMsg .= "$k, ";
        }
        $smarty->assign("mb_message", $strErrorMsg);
        $content = viewFormDatosbasicos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
    }
    else{
        //NO ERROR, HERE IMPLEMENTATION OF SAVE
        $content = "Code to save yet undefined.";
    }
    return $content;
}

function createFieldForm()
{
    $arrOrigen = array(	'1800' => '1800', 
			'WEB' => 'Web',
			'PBX' => 'PBX',
			'RFC' => 'Referido por cliente',
		      );

    $arrFields = array(
            "ci_input"   => array(      "LABEL"                  => _tr("Cédula"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),

            "nombre"   => array(      "LABEL"                  => _tr("Nombre"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "apellido"   => array(      "LABEL"                  => _tr("Apellido"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "provincia"   => array(      "LABEL"                  => _tr("Provincia"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "ciudad"   => array(      "LABEL"                  => _tr("Ciudad"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "correo_personal"   => array(      "LABEL"                  => _tr("Correo personal"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "correo_trabajo"   => array(      "LABEL"                  => _tr("Correo del trabajo"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "estado_civil"   => array(      "LABEL"                  => _tr("Estado civil"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "nacimiento"   => array(      "LABEL"                  => _tr("Fecha de Nacimiento"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "DATE",
					    "INPUT_EXTRA_PARAM"      => array("TIME" => false, "FORMAT" => "%Y-%m-%d"),
                                            "VALIDATION_TYPE"        => "",
                                            "EDITABLE"               => "si",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "origen"   => array(      "LABEL"                  => _tr("Origen"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrOrigen,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),


            );
    if (getParameter("ci")){
        $arrFields["cedula"]=array(      "LABEL"                  => _tr("Cedula"),
                                            "REQUIRED"               => "yes",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            );
    }else{
        $arrFields["cedula"]=array(      "LABEL"                  => _tr("Cedula"),
                                            "REQUIRED"               => "yes",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "no",
                                            );
    }
    return $arrFields;
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
    else
        return "report"; //cancel
}

function _pre($Array)
{
    echo "<pre>";
    print_r($Array);
    echo "</pre>";
}
?>