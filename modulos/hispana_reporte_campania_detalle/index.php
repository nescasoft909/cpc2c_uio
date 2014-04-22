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
  $Id: index.php,v 1.1 2012-04-12 01:04:03 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoReportedetalladodegestion.class.php";

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
        default:
            $content = reportReportedetalladodegestión($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function reportReportedetalladodegestión($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pReportedetallado = new paloSantoReportedetalladodegestión($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Reporte detallado de gestión"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Reporte detallado de gestión"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value);
    $oGrid->setURL($url);

    $arrColumns = array(_tr("Campaña"),_tr("Fecha"),_tr("Cliente"),_tr("CI"),_tr("Teléfono"),_tr("Agente"),_tr("Calltype"));
    $numColumnasFijas = sizeof($arrColumns);

    if(isset($filter_field) && is_numeric($filter_field)){ // si está seteado el número de campaña, busca los campos del form de dicha campaña
	$arrTmp = $pReportedetallado->getFormFields($filter_field);
	// _pre($arrTmp);
	$i=0;
	foreach($arrTmp as $formField){
	    $arrOrden[] = $formField['id'];
	    $arrColumnsAdicionales[$i] = $formField['etiqueta'];
	    $i++;
	}
	$arrColumns = array_merge($arrColumns,$arrColumnsAdicionales);
    }
    // _pre($arrOrden);
    // _pre($arrColumns);
    unset($arrTmp);
    $oGrid->setColumns($arrColumns);

    $total   = $pReportedetallado->getNumReportedetalladodegestion($filter_field); // $filter_field sólo contien el id de la campaña

    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }
    else{
        $limit  = 200;
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

//    $arrResult =$pReportedetallado->getReportedetalladodegestion($limit, $offset, $filter_field); // $filter_field sólo contien el id de la campaña

    $arrResult =$pReportedetallado->getReportedeCalltypes($limit, $offset, $filter_field, $filter_value);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0] = $value['campania'];
	    $arrTmp[1] = $value['campania'];
	    $arrTmp[2] = $value['cliente'];
	    $arrTmp[3] = $value['ci'];
	    $arrTmp[4] = $value['telefono'];
	    $arrTmp[5] = $value['agente'];
	    $arrTmp[6] = $value['mejor_calltype'];
	    // $arrFormValues = $pReportedetallado->getFormValues($value['id_gestion_campania']);
	    $arrFormValues = $pReportedetallado->getFormValues($value['id_gestion_mejor_calltype']);

	    // array_search retorna el key dado el valor a buscar
	    foreach($arrFormValues as $formValue){
		// echo $formValue['valor'] . " " . array_search($formValue['id_form_field'],$arrOrden) . "<br>";
		$ubicacionReal = array_search($formValue['id_form_field'],$arrOrden)+$numColumnasFijas;
		$arrTmp[$ubicacionReal] = $formValue['valor'];
	    }
            $arrData[] = $arrTmp;
        }
    }
    $oGrid->setData($arrData);

    //begin section filter
    $oFilterForm = new paloForm($smarty, createFieldFilter($pReportedetallado));
    $smarty->assign("SHOW", _tr("Show"));
    $htmlFilter  = $oFilterForm->fetchForm("$local_templates_dir/filter.tpl","",$_POST);
    //end section filter

    $oGrid->showFilter(trim($htmlFilter));
    $content = $oGrid->fetchGrid();
    //end grid parameters

    return $content;
}


function createFieldFilter($pReporteGestion){
    $arrCampaigns = $pReporteGestion->getCampaigns();

    foreach($arrCampaigns as $campania){
	$arrFilter[$campania['id']] = $campania['nombre'];
    }

    $arrFormElements = array(
            "filter_field" => array("LABEL"                  => _tr("Escoger"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "SELECT",
                                    "INPUT_EXTRA_PARAM"      => $arrFilter,
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => "")
                    );
    return $arrFormElements;
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