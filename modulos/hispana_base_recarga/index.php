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
  $Id: index.php,v 1.1 2012-03-19 08:03:31 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoIngresodeBasedeClientes.class.php";

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

    switch($action){
        case "save_edit":
            $content = saveEditBaseDeClientes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
        case "save_new":
            $content = saveNewIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
        default: // view_form
            $content = viewFormIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function viewFormIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pIngresodeBasedeClientes = new paloSantoIngresodeBasedeClientes($pDB);
    $arrFormIngresodeBasedeClientes = createFieldForm($pDB);
    if(!is_array($arrFormIngresodeBasedeClientes)){
        return $arrFormIngresodeBasedeClientes;
    }
    $oForm = new paloForm($smarty,$arrFormIngresodeBasedeClientes);

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
        $dataIngresodeBasedeClientes = $pIngresodeBasedeClientes->getIngresodeBasedeClientesById($id);
        if(is_array($dataIngresodeBasedeClientes) & count($dataIngresodeBasedeClientes)>0)
            $_DATA = $dataIngresodeBasedeClientes;
        else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pIngresodeBasedeClientes->errMsg);
        }
    }

    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");

    
    //Editar base
    $edit_base=  getParameter("edit_base");
    $id_base=  getParameter("id_base");
    $id_campania=  getParameter("id_campania");
    if ($edit_base=="si"){
        $base=$pIngresodeBasedeClientes->getDatosBase($id_base,$id_campania);
        $_DATA=$base;
        $smarty->assign("view_edit", "si");
        $smarty->assign("id_campania", $id_campania);
        $smarty->assign("id_base", $id_base);
    }
    
    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Ingreso de Base de Clientes"), $_DATA);
    $content = "<form method='POST' enctype='multipart/form-data' style='margin-bottom:0;' action='?menu=$module_name'>".$htmlForm."</form>";

    return $content;
}

function saveNewIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pIngresodeBasedeClientes = new paloSantoIngresodeBasedeClientes($pDB);
    $arrFormIngresodeBasedeClientes = createFieldForm($pDB);
    $oForm = new paloForm($smarty,$arrFormIngresodeBasedeClientes);

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
        $content = viewFormIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
    }
    else{
	if(isset($_FILES)){
	    $arrResult = $pIngresodeBasedeClientes->guardarActualizar($_POST,$_FILES);
	}
        $content = $pIngresodeBasedeClientes->resultadoTemplate($arrResult, $_FILES);
    }
    return $content;
}
//saveEditBaseDeClientes
function saveEditBaseDeClientes($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pIngresodeBasedeClientes = new paloSantoIngresodeBasedeClientes($pDB);
    $arrFormIngresodeBasedeClientes = createFieldForm($pDB);
    $oForm = new paloForm($smarty,$arrFormIngresodeBasedeClientes);

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
        $content = viewFormIngresodeBasedeClientes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
    }
    else{
        $arrResult = $pIngresodeBasedeClientes->actualizarCampaniaRecarga($_POST);
        header("location: ?menu=hispana_listado_recarga");
        
    }
    return $content;
}
function createFieldForm($pDB)
{
    $arrOptions = array('val1' => 'Value 1', 'val2' => 'Value 2', 'val3' => 'Value 3');
    $arrTmp=null;
    $pIngresodeBasedeClientes = new paloSantoIngresodeBasedeClientes($pDB);
    $arrTmp=$pIngresodeBasedeClientes->getCampanias();
    
    if(!empty($arrTmp)){
        if(count($arrTmp)>0){
            foreach($arrTmp as $form){
                $arrCampOptions[$form['id']] = $form['nombre'];
            }
        }else{
	     return "No existen campañas recargables.";
        }
    }else{
	return "No existen campañas recargables.";
    }
    $arrFields = array(
           "nombre_base"   => array(      "LABEL"                  => _tr("Nombre de la BD"),
                                            "REQUIRED"               => "yes",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "archivo_de_clientes"   => array(      "LABEL"                  => _tr("Archivo de clientes"),
                                            "REQUIRED"               => "yes",
                                            "INPUT_TYPE"             => "FILE",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
        "fecha_inicio"   => array(      "LABEL"                  => _tr("Fecha Inicio"),
					    "REQUIRED"               => "yes",
					    "INPUT_TYPE"             => "DATE",
					    "INPUT_EXTRA_PARAM"      => array("TIME" => false, "FORMAT" => "%Y-%m-%d"),
					    "VALIDATION_TYPE"        => 'text',
/*
					    "VALIDATION_TYPE"        => 'ereg',
					    "VALIDATION_EXTRA_PARAM" => '^[[:digit:]]{2}[[:space:]]+[[:alpha:]]{3}[[:space:]]+[[:digit:]]{4}$'
*/
                                            ),
            "fecha_fin"   => array(         "LABEL"                  => _tr("Fecha Fin"),
                                            "REQUIRED"               => "no",
					    "REQUIRED"               => "yes",
					    "INPUT_TYPE"             => "DATE",
					    "INPUT_EXTRA_PARAM"      => array("TIME" => false, "FORMAT" => "%Y-%m-%d"),
					    "VALIDATION_TYPE"        => 'text',
/*
					    "VALIDATION_TYPE"        => 'ereg',
					    "VALIDATION_EXTRA_PARAM" => '^[[:digit:]]{2}[[:space:]]+[[:alpha:]]{3}[[:space:]]+[[:digit:]]{4}$'
*/
                                            ),
           "campania"   => array(      "LABEL"                  => _tr("Campaña Recargable"),
                                            "REQUIRED"               => "yes",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrCampOptions,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),

            );
    
    //Editar base
    $edit_base=  getParameter("edit_base");
    $id_base=  getParameter("id_base");
    $id_campania=  getParameter("id_campania");
    if ($edit_base=="si"){
        $base=$pIngresodeBasedeClientes->getDatosBase($id_base,$id_campania);
    }
    
    return $arrFields;
}

function getAction()
{
    if(getParameter("save_edit")=="si")
        return "save_edit";
    else if(getParameter("save_new")) //Get parameter by POST (submit)
        return "save_new";
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

function _pre($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

?>