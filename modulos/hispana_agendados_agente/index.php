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
  $Id: index.php,v 1.1 2012-07-05 10:07:39 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoAgendadosAgente.class.php";

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
            $content = reportReportedeCalltypes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function reportReportedeCalltypes($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pReportedeCalltypes = new paloSantoAgendadosAgente($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");
    $aditional_key = getParameter("aditional_key");
    $aditional_value = getParameter("aditional_value");
    $mostrar_adicionales = getParameter("mostrar_adicionales");
    $calltype_list = getParameter("calltype_list");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Agendados de Agente"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Agendados de Agente"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value,
        "aditional_value" =>  $aditional_value,
        "aditional_key" =>  $aditional_key,
        "mostrar_adicionales" => $mostrar_adicionales,
        );
    $oGrid->setURL($url);

    $arrColumns = array(_tr("Campaña"),_tr("Origen"),_tr("Agendamiento"),_tr("Cliente"),_tr("Teléfono"),/*_tr("Agente"),*/_tr("Mejor calltype"),_tr("Observación"),_tr("Acción"));
    //Datos Adicionales
    $arrColumnasAdicionales = $pReportedeCalltypes->getColumnasAdicionales();  
    if($mostrar_adicionales=="on"){       
        $arrColumns = array_merge($arrColumns,$arrColumnasAdicionales);
    }
    //fin datos adicionales
    $oGrid->setColumns($arrColumns);
    
    $total   = $pReportedeCalltypes->getNumReporteAgendados($filter_field, $filter_value, $_SESSION['elastix_user'],$aditional_key,$aditional_value,$calltype_list);
    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }
    else{
        $limit  = 200; // default 20 (muy poco)
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

    $arrResult =$pReportedeCalltypes->getReporteAgendados($limit, $offset, $filter_field, $filter_value, $_SESSION['elastix_user'],$aditional_key,$aditional_value,$calltype_list);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){	    
	    $arrTmp[0] = $value['campania'];
	    $arrTmp[1] = $value['origen'];
	    $arrTmp[2] = $value['fecha_agendamiento'];
	    $arrTmp[3] = $value['cliente'];
	    $arrTmp[4] = $value['telefono'];
	    $arrTmp[5] = $value['contactabilidad'] . " - " . $value['mejor_calltype'];
	    $arrTmp[6] = $value['observacion'];
            if (empty($value[id_campania_cliente])){
                $arrTmp[7] = "<a href=index.php?menu=hispana_interfaz_agente&action=gestionar&id_campania_cliente_recargable=$value[id_campania_recargable_cliente]>Gestionar</a>";
            }else{
                $arrTmp[7] = "<a href=index.php?menu=hispana_interfaz_agente&action=gestionar&id_campania_cliente=$value[id_campania_cliente]>Gestionar</a>";
            }
            if($mostrar_adicionales=="on"){
                if (empty($value[id_campania_cliente])){
                    $arrDatosAdicionales = $pReportedeCalltypes->getDatosAdicionalesRecargable($value["id_campania_recargable_cliente"]);
                }else{
                    $arrDatosAdicionales = $pReportedeCalltypes->getDatosAdicionales($value["ci"]);
                }
                $i = 8;
                foreach($arrColumnasAdicionales as $k => $columnaAdicional){
                    $arrTmp[$i] = $arrDatosAdicionales[$columnaAdicional];    		
                    $i++;	
                }
                unset($arrDatosAdicionales);
            }
            
            $arrData[] = $arrTmp;
        }
    }
    $oGrid->setData($arrData);  

/* Para colocar en $arrTmp 

*/
    //begin section filter
    $arrFormElements= createFieldFilter();
    //if($mostrar_adicionales=="on"){
        foreach($arrColumnasAdicionales as $key=>$value){
            $arrAdicionales[$value]=$value;
        }
        
        $arrCalltypes=$pReportedeCalltypes->getCalltypes();
        
        $arrFormElements["calltype_list"] = array("LABEL"     => _tr("Calltype"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "SELECT",
                                    "INPUT_EXTRA_PARAM"      => $arrCalltypes,
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => "");
        
        $arrFormElements["aditional_key"] = array("LABEL"     => _tr("Campo Adicional"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "SELECT",
                                    "INPUT_EXTRA_PARAM"      => $arrAdicionales,
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => "");
        $arrFormElements["aditional_value"] = array("LABEL"     => _tr("Valor"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "TEXT",
                                    "INPUT_EXTRA_PARAM"      => "",
                                    "VALIDATION_TYPE"        => "text",
                                    "VALIDATION_EXTRA_PARAM" => "");
    //}
    $oFilterForm = new paloForm($smarty, $arrFormElements);
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
	    "e.nombre" => _tr("Campaña"),
	    "concat(`a`.`nombre`, _latin1' ', `a`.`apellido`)" => _tr("Cliente"),
	    "a.ci" => _tr("CI"),
	    "b.fecha_agendamiento" => _tr("Fecha de agendamiento"),
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
            "mostrar_adicionales" => array("LABEL"           => _tr("Mostrar Datos Adicionales"),
                                    "REQUIRED"               => "no",
                                    "INPUT_TYPE"             => "CHECKBOX",
                                    "INPUT_EXTRA_PARAM"      => "",
                                    "VALIDATION_TYPE"        => "",
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