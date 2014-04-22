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
  $Id: index.php,v 1.1 2012-08-07 08:08:16 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoReporteEfectividad.class.php";
    include_once "modules/hispana_reporte_calltype/libs/paloSantoReportedeCalltypes.class.php";


    //include file language agree to elastix configuration
    //if file language not exists, then include language by default (en)
    $lang = get_language();
    $base_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $lang_file = "modules/$module_name/lang/$lang.lang";
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
    $templates_dir = (isset($arrConf['templates_dir']))?$arrConf['templates_dir']:'themes';
    $local_templates_dir = "$base_dir/modules/$module_name/".$templates_dir.'/'.$arrConf['theme'];

    //conexion resource
    $pDB = new paloDB($arrConf['dsn_conn_database']);

    //actions
    $action = getAction();
    $content = "";

    

    switch($action){
        case 'efectividad':
            $content = Efectividad($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;

        default:
	    actualizarCampaniaCliente($pDB);
            $content = reportReporteEfectividad($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function actualizarCampaniaCliente($pDB)
{
    // Crea un objeto de otra clase.  
    $pReportedeCalltypes = new paloSantoReportedeCalltypes($pDB);
    $pReportedeCalltypes->actualizarCampaniaCliente();
}

function Efectividad($smarty, $module_name, $local_templates_dir, $pDB, $arrConf)
{
    $pReporteEfectividad = new paloSantoReporteEfectividad($pDB);

    $idBase = getParameter('base');
    $idCampania = getParameter('id');
    $arrBases = $pReporteEfectividad->getBasesCampania($idCampania);
    $NombreCampania = $pReporteEfectividad->getNombreCampania($idCampania);
    $arrBases[0] = "--TODAS--";
    $smarty->assign("CAMPANIA",$NombreCampania);    
    $smarty->assign("TITULO","REPORTE DE EFECTIVIDAD POR CAMPAÑA");
    $smarty->assign('BASE_ID', $idBase);
    $smarty->assign('BASE_OPTIONS', $arrBases);

    $registrosCargados = $pReporteEfectividad->getRegistrosCargados($idCampania,$idBase);
    $registrosBarridos = $pReporteEfectividad->getRegistrosBarridos($idCampania,$idBase);
    $registrosContactados = $pReporteEfectividad->getClaseContactados($idCampania,$idBase,"Contactado");
    $arrConversion = $pReporteEfectividad->getConversion($idCampania,$idBase);

    $smarty->assign("CONTACTOS_CARGADOS", $registrosCargados);    
    $smarty->assign("CONTACTOS_BARRIDOS", $registrosBarridos);    
    $smarty->assign("PORCENTAJE_BARRIDOS", round($registrosBarridos/$registrosCargados,4)*100);    
    $smarty->assign("CONTACTOS_NO_BARRIDOS", $registrosCargados-$registrosBarridos);    
    $smarty->assign("PORCENTAJE_NO_BARRIDOS", round(($registrosCargados-$registrosBarridos)/$registrosCargados,4)*100);    
    $smarty->assign("CONTACTADOS", $registrosContactados);
    $smarty->assign("NO_CONTACTADOS", $registrosBarridos-$registrosContactados);
    $smarty->assign("PORCENTAJE_PENETRACION", round($registrosContactados/$registrosBarridos,4)*100);        
    $smarty->assign("CONVERTIDOS", $arrConversion['cont']);
    $smarty->assign("PORCENTAJE_CONVERSION", round($arrConversion['cont']/$registrosContactados,4)*100);
    $smarty->assign("MEJOR_CALLTYPE", $arrConversion['calltype']);
    $smarty->assign("PESO_MEJOR_CALLTYPE", $arrConversion['peso']);

    $content = $smarty->fetch("/var/www/html/modules/$module_name/themes/default/reporte_efectividad.tpl");
    return $content;
}

function reportReporteEfectividad($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pReporteEfectividad = new paloSantoReporteEfectividad($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Reporte de efectividad"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Reporte de efectividad"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value);
    $oGrid->setURL($url);

    $arrColumns = array(_tr("Campaña"),_tr("Acción"));
    $oGrid->setColumns($arrColumns);

    $total   = $pReporteEfectividad->getNumReporteEfectividad($filter_field, $filter_value);
    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }else{
        $limit  = 20;
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

    $arrResult =$pReporteEfectividad->getReporteEfectividad($limit, $offset, $filter_field, $filter_value);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0] = $value['nombre'];
	    $arrTmp[1] = "<a href=index.php?menu=$module_name&id=$value[id]&action=efectividad>Ver reporte de efectividad</a>";
            $arrData[] = $arrTmp;
        }
    }
    $oGrid->setData($arrData);

    //begin section filter
    $oFilterForm = new paloForm($smarty, createFieldFilter());
    $smarty->assign("SHOW", _tr("Show"));
    $htmlFilter  = $oFilterForm->fetchForm("$local_templates_dir/filter.tpl","",$_POST);
    //end section filter

    $oGrid->showFilter(trim($htmlFilter));
    $content = $oGrid->fetchGrid();
    //end grid parameters

    return $content;
}


function createFieldFilter(){
    $arrFilter = array(
	    "campania" => _tr("Campania"),
	    "accion" => _tr("Accion"),
                    );

    $arrFormElements = array(
            "filter_field" => array("LABEL"                  => _tr("Search"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "SELECT",
                                    "INPUT_EXTRA_PARAM"      => $arrFilter,
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => ""),
            "filter_value" => array("LABEL"                  => "",
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "TEXT",
                                    "INPUT_EXTRA_PARAM"      => "",
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => ""),
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
    else if(getParameter("action")=="efectividad")
        return "efectividad";
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