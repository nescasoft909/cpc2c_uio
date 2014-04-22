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
  $Id: index.php,v 1.1 2012-11-11 10:11:52 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoCampaniaDerivada.class.php";

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

    if(!is_object($pDB->conn)){	
	$smarty->assign("mb_title", _tr("Error"));
	$smarty->assign("mb_message", "No puede conectarse a la base de datos.");
    }

    //actions
    $action = getAction();
    $content = "";

    switch($action){
        case "save_new":
            $content = saveNewCampaniaDerivada($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
        default: // view_form
            $content = viewFormCampaniaDerivada($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function viewFormCampaniaDerivada($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{    
    $pCampaniaDerivada = new paloSantoCampaniaDerivada($pDB);
    $arrFormDynamicAgents = createFieldForm($pDB);
    $oForm = new paloForm($smarty,$arrFormDynamicAgents);

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
        $dataDynamicAgents = $pCampaniaDerivada->getDynamicAgentsById($id);
        if(is_array($dataDynamicAgents) & count($dataDynamicAgents)>0)
            $_DATA = $dataDynamicAgents;
        else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pCampaniaDerivada->errMsg);
        }
    }

    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");
    $smarty->assign("CAMPANIA_OPTIONS", $pCampaniaDerivada->getCampanias());
    $smarty->assign("CALLTYPES", _tr("Calltypes"));

    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Campaña derivada"), $_DATA);
    $content = "<form  method='POST' style='margin-bottom:0;' action='?menu=$module_name'>".$htmlForm."</form>";

    return $content;
}

function saveNewCampaniaDerivada($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{ 
    $pCampaniaDerivada = new paloSantoCampaniaDerivada($pDB);
    $arrFormDynamicAgents = createFieldForm($pDB);
    $oForm = new paloForm($smarty,$arrFormDynamicAgents);

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
    }else{
	if(isset($_POST['nombre_campania'])){
	    if($pCampaniaDerivada->crearCampaniaDerivada($_POST)){
		Header("Location: index.php?menu=hispana_listado_campanias");
	    }else{
		$smarty->assign("mb_title", _tr("Error"));		
		$smarty->assign("mb_message", "No pudeo crearse la campaña derivada.");
	    }
	}
	$content = viewFormCampaniaDerivada($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
    }
    return $content;
}

function createFieldForm($pDB)
{
    $pCampaniaDerivada = new paloSantoCampaniaDerivada($pDB);
    $arrCampanias = $pCampaniaDerivada->getCampanias();        
    $arrOptions = array();

    $arrFields = array(
	    "nombre_campania"   => array(      "LABEL"                  => _tr("Nombre:"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),

	    "campania"   => array(      "LABEL"                  => _tr("Campaña"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrCampanias,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),
            "active_bases"   => array(      "LABEL"                  => _tr("Activas"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrOptions,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",					    
					    "MULTIPLE"               => true,
                                            "EDITABLE"               => "si",
                                            ),
            "inactive_bases"   => array(      "LABEL"                  => _tr("Inactivas"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrOptions,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
					    "MULTIPLE"               => true,
                                            "EDITABLE"               => "si",
                                            ),
	    "active_calltypes"   => array(      "LABEL"                  => _tr("Activos"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrOptions,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",					    
					    "MULTIPLE"               => true,
                                            "EDITABLE"               => "si",
                                            ),
            "inactive_calltypes"   => array(      "LABEL"                  => _tr("Inactivos"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrOptions,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
					    "MULTIPLE"               => true,
                                            "EDITABLE"               => "si",
                                            ),

            );
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

function _pre($array)
{
echo "<pre>";
print_r($array);
echo "</pre>";
}
?>