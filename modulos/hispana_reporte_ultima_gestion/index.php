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
ini_set('max_execution_time', 300);

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoReportedeCalltypes.class.php";

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

    // Lo primero que hace es actualizar la tabla campania_cliente
    if($arrConf['actualizarCampaniaCliente']){
	
	actualizarCampaniaCliente($pDB);
    }        

    switch($action){
        case "generaOffline":
            generaReporteOffline($module_name);
        default:
            $content = reportReportedeCalltypes($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    ini_set('max_execution_time', 30);
    return $content;
}

function actualizarCampaniaCliente($pDB)
{
    $pReportedeCalltypes = new paloSantoReportedeCalltypes($pDB);
    $pReportedeCalltypes->actualizarCampaniaCliente();
}

function generaReporteOffline($module_name){
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");
    $id_campania = getParameter("id_campania");
    $tiempo_unix = time();
    exec("/usr/bin/nohup /usr/bin/php /var/www/html/modules/$module_name/reporte_offline.php $id_campania $tiempo_unix $filter_field $filter_value> /dev/null & echo $!", $output, $ret_var);
    header("location: ?menu=hispana_listado_reporte_offline");
    exit(1);
}

function reportReportedeCalltypes($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pReportedeCalltypes = new paloSantoReportedeCalltypes($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");
    $id_campania = getParameter("id_campania");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Reporte de Ultimo Calltype"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Reporte de Ultimo Calltype"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value,
	"id_campania"  =>  $id_campania);
    $oGrid->setURL($url);

    // Columnas base
    $arrColumns = array(_tr("Fecha"),_tr("Campaña"),_tr("Base"),_tr("Cliente"),_tr("CI"),_tr("Teléfono"),_tr("Agente"),_tr("Contactabilidad"),_tr("Ultimo calltype"),_tr("Formulario"),);

    // Otras columnas
    if($id_campania != ""){
	// Columnas adicionales
	$arrColumnasAdicionales = $pReportedeCalltypes->getColumnasAdicionales($id_campania);  
	
	$arrColumns = array_merge($arrColumns,$arrColumnasAdicionales);
	
	// Columnas de gestión
	$arrTmp = $pReportedeCalltypes->getFormFields($id_campania);
	
	$i=0;
	foreach($arrTmp as $formField){
	    $arrOrden[] = $formField['id'];
	    $arrColumnsAdicionales[$i] = $formField['etiqueta'];
	    $i++;
	}
	$arrColumns = array_merge($arrColumns,$arrColumnsAdicionales);
    }
    
    $oGrid->setColumns($arrColumns);

    $total   = $pReportedeCalltypes->getNumReportedeCalltypes($filter_field, $filter_value, $id_campania);

    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }else{
        $limit  = 20; // default 20
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

    $arrResult =$pReportedeCalltypes->getReportedeCalltypes($limit, $offset, $filter_field, $filter_value, $id_campania);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0] = $value['fecha'];
	    $arrTmp[1] = $value['base'];
            $arrTmp[2] = $value['campania'];
	    $arrTmp[3] = $value['cliente'];
	    $arrTmp[4] = $value['ci'];
	    $arrTmp[5] = $value['telefono'];
	    $arrTmp[6] = $value['agente'];
	    $arrTmp[7] = $value['contactabilidad'];
	    $arrTmp[8] = $value['mejor_calltype'];
	    // $arrTmp[8] = $value['formulario'];

	    // Así lo requiere RUBENING
	    /*$arrTmp[9] = "<a href=modules/$module_name/gestion_info.php?id_gestion=$value[id_gestion_mejor_calltype] target=\"_blank\" onClick=\"window.open(this.href, this.target, 'width=600,height=400'); return false;\">Ver gestión</a>".
                         "<br>".
                        "<a href=modules/$module_name/gestion_edit.php?id_gestion=$value[id_gestion_mejor_calltype]&user=".$_SESSION["elastix_user"]." target=\"_blank\" onClick=\"window.open(this.href, this.target, 'width=600,height=400'); return false;\">Editar</a>"*/;
            $arrTmp[9] = "<a href=modules/$module_name/gestion_info.php?id_gestion=$value[ultimo_calltype] target=\"_blank\" onClick=\"window.open(this.href, this.target, 'width=600,height=400'); return false;\">Ver gestión</a>";
	    // Datos adicionales
	    $arrDatosAdicionales = $pReportedeCalltypes->getDatosAdicionales($value['ci'],$value['id_base']);
	    $i = 10;
	    foreach($arrColumnasAdicionales as $k => $columnaAdicional){
		$arrTmp[$i] = $arrDatosAdicionales[$columnaAdicional];    		
		$i++;	
	    }
	    unset($arrDatosAdicionales);

	    $numColumnasFijas = $i;

	    // Datos de gestión
	    $arrFormValues = $pReportedeCalltypes->getFormValues($value['ultimo_calltype']);

	    // array_search retorna el key dado el valor a buscar
	    foreach($arrFormValues as $formValue){
		// echo $formValue['valor'] . " " . array_search($formValue['id_form_field'],$arrOrden) . "<br>";
		$ubicacionReal = array_search($formValue['id_form_field'],$arrOrden)+$numColumnasFijas;
		if(is_array($formValue['valor'])){
		    $formValue['valor'] = print_r($formValue['valor'],true);
		}
		$arrTmp[$ubicacionReal] = $formValue['valor'];
	    }


            $arrData[] = $arrTmp;
	    unset($arrTmp);
        }
    }

    $oGrid->setData($arrData);

/* Para colocar en $arrTmp 

*/
    //begin section filter
    $oFilterForm = new paloForm($smarty, createFieldFilter());

    $smarty->assign("SHOW", _tr("Show"));
    $smarty->assign("filter_campaign", filter_campaign($pDB, $id_campania));
    $smarty->assign("id_campania", $id_campania);
    $smarty->assign("filter_field_url", $filter_field);
    $smarty->assign("filter_value_url", $filter_value);
    
    $htmlFilter  = $oFilterForm->fetchForm("$local_templates_dir/filter.tpl","",$_POST);
    //end section filter

    $oGrid->showFilter(trim($htmlFilter));
    $content = $oGrid->fetchGrid();
    //end grid parameters

    return $content;
}


function createFieldFilter(){
    $arrFilter = array(
	    "b.fecha" => _tr("Fecha"),
	    "concat(e.nombre,' ',e.apellido)" => _tr("Cliente"),
	    "a.ci" => _tr("CI"),
            "f.nombre" => _tr("Base"),
	    "b.telefono" => _tr("Telefono"),
	    "b.agente" => _tr("Agente"),
	    "c.clase" => _tr("Contactabilidad"),
	    "c.descripcion" => _tr("Mejor Calltype")
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

function filter_campaign($pDB, $id_campania)
{
    $pReportedeCalltypes = new paloSantoReportedeCalltypes($pDB);
    $arrCampanias = $pReportedeCalltypes->getCampaniasActivas();
    $selected = "";  
    $select = "<select name='id_campania'>";
    foreach($arrCampanias as $id => $campania){	
	if($id == $id_campania)
	    $selected = "selected";
	$select .= "<option value='" . $id . "' $selected>" . $campania . "</option>";
	$selected = "";
	
    }
    $select .= "</select>";
    return $select;
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
    else if(getParameter("action")=="generaOffline")
        return "generaOffline";
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