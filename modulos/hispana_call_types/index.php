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
  $Id: index.php,v 1.1 2012-03-30 04:03:53 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoCalltypesporcampania.class.php";

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
        case "save_new":
            $content = saveNewCalltypesporcampaña($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
        default: // view_form
            $content = viewFormCalltypesporcampaña($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function viewFormCalltypesporcampaña($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pCalltypesporcampaña = new paloSantoCalltypesporcampania($pDB);
    $arrFormCalltypesporcampaña = createFieldForm($pCalltypesporcampaña);
    $oForm = new paloForm($smarty,$arrFormCalltypesporcampaña);

    //begin, Form data persistence to errors and other events.
    $_DATA  = $_POST;
    $action = getParameter("action");
    $id     = getParameter("id");
    $id_campania     = getParameter("id_campania");
    $smarty->assign("ID", $id); //persistence id with input hidden in tpl
    $smarty->assign("ID_CAMPANIA", $id_campania); //persistence id with input hidden in tpl

    if($action=="view")
        $oForm->setViewMode();
    else if($action=="view_edit" || getParameter("save_edit"))
        $oForm->setEditMode();
    //end, Form data persistence to errors and other events.

    if($action=="view" || $action=="view_edit"){ // the action is to view or view_edit.
        $dataCalltypesporcampaña = $pCalltypesporcampaña->getCalltypesporcampaniaById($id,$id_campania);

	$smarty->assign("action", "view_edit");

        if(is_array($dataCalltypesporcampaña) & count($dataCalltypesporcampaña)>0){
            $_DATA = $dataCalltypesporcampaña;
	    $smarty->assign("info", $_DATA);
	}else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pCalltypesporcampaña->errMsg);
        }
    }
    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");
    
    $content="<script>
    function nuevo_calltypeDisable(){
        if(document.getElementById('nuevo_calltype').checked==true){
            document.getElementById('calltypes').disabled=true;
            document.getElementById('descripcion').disabled=false;
            document.getElementById('definicion').disabled=false;
        }else{
            document.getElementById('calltypes').disabled=false;
            document.getElementById('descripcion').disabled=true;
            document.getElementById('definicion').disabled=true;
        }
    }
    </script>";
    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Crear call type"), $_DATA); 
    $content .= "<form  method='POST' style='margin-bottom:0;' action='?menu=$module_name'>".$htmlForm."</form>";

    return $content;
}

function saveNewCalltypesporcampaña($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pCalltypesporcampaña = new paloSantoCalltypesporcampania($pDB);
    $arrFormCalltypesporcampaña = createFieldForm($pCalltypesporcampaña);
    $oForm = new paloForm($smarty,$arrFormCalltypesporcampaña);

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
        $content = viewFormCalltypesporcampaña($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
    }
    else{
	$action = getAction();
	if($action == "save_edit"){
	    $pCalltypesporcampaña->updateCallType($_POST);
	}elseif($action == "save_new"){
	    $pCalltypesporcampaña->saveCallType($_POST);
	}
	Header("Location: index.php?menu=hispana_listado_call_types");
    }
    return $content;
}

function createFieldForm($pCalltypesporcampaña)
{
    $arrTmp = $pCalltypesporcampaña->getCampaigns();
    $arrTmpCalltypes = $pCalltypesporcampaña->getCalltypes();

    foreach($arrTmp as $v){
	$arrCampaigns[$v['id']] = $v['nombre'];
    }
    foreach($arrTmpCalltypes as $v){
	$arrCalltypes[$v['id']] = $v['descripcion'];
    }

    $arrClaseCallType = array('Contactado' => 'Contactado', 
			      'No contactado' => 'No contactado',
			      'Agendado' => 'Agendado');

    $arrFields = array(
            "campania"   => array(      "LABEL"                  => _tr("Campaña"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrCampaigns,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),
            "clase"   => array(      "LABEL"                  => _tr("Clase de Call Type"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrClaseCallType,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),
            "descripcion"   => array(      "LABEL"                  => _tr("Descripción"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => array("id"=>"descripcion","disabled"=>"disabled"),
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "definicion"   => array(      "LABEL"                  => _tr("Definición"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXTAREA",
                                            "INPUT_EXTRA_PARAM"      => array("id"=>"definicion","disabled"=>"disabled"),
                                            "VALIDATION_TYPE"        => "text",
                                            "EDITABLE"               => "si",
                                            "COLS"                   => "50",
                                            "ROWS"                   => "4",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
            "peso"   => array(      "LABEL"                  => _tr("Peso"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "TEXT",
                                            "INPUT_EXTRA_PARAM"      => "",
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => ""
                                            ),
         "calltypes"   => array(      "LABEL"                  => _tr("Calltypes"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "SELECT",
                                            "INPUT_EXTRA_PARAM"      => $arrCalltypes,
                                            "VALIDATION_TYPE"        => "text",
                                            "VALIDATION_EXTRA_PARAM" => "",
                                            "EDITABLE"               => "si",
                                            ),
        "nuevo_calltype"   => array(      "LABEL"                  => _tr("Nuevo Calltype"),
                                            "REQUIRED"               => "no",
                                            "INPUT_TYPE"             => "CHECKBOX",
                                            "VALIDATION_TYPE"        => "",
                                            "VALIDATION_EXTRA_PARAM" => "",
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
        return "view_edit";
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
