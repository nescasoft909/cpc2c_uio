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
  $Id: index.php,v 1.1 2012-03-21 06:03:22 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoClientesManuales.class.php";

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

    // echo $action;
    switch($action){	
	case 'save':
	    if(!guardarAgendarCampaniaCliente($pDB,$_POST,$smarty)){
		$smarty->assign("mb_title", _tr("Error"));     
		$smarty->assign("mb_message","Es posible que el cliente haya sido agregado o gestionado en la campaña previamente.");
	    }
	case 'agregar':
	    $content = agregarACampania($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
	    break;
        default:
            $content = reportListadodebases($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function guardarAgendarCampaniaCliente($pDB, $DATA, $smarty)
{
    $pClientesManuales = new paloSantoClientesManuales($pDB);
    if($pClientesManuales->getTipoCampania($DATA['campania'])=="RECARGABLE"){
        $result = $pClientesManuales->guardarAgendarCampaniaClienteRecargable($DATA); 
    }else{
        $result = $pClientesManuales->guardarAgendarCampaniaCliente($DATA); 
    }
    
    if(!$result) return false;
    else return true;
}

function agregarACampania($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pClientesManuales = new paloSantoClientesManuales($pDB);
    $arrCampanias = $pClientesManuales->getCampaniasActivas();
    $arrAgendamientos = $pClientesManuales->getAgendamientosCliente(getParameter('ci'));
    $script="<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"libs/js/jscalendar/calendar-win2k-2.css\" />
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/lang/calendar-en.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar-setup.js\"></script>";
    $smarty->assign("HEADER", $script);

    $smarty->assign("TITULO","Agregar cliente a campaña");
    $smarty->assign("CAMPANIAS_OPTIONS",$arrCampanias);
    $smarty->assign("ci",getParameter('ci'));
    $smarty->assign("CLIENTE",$pClientesManuales->getNombreApellido(getParameter('ci')));
    $smarty->assign("CALENDARIO",calendario("fecha","btn_fecha"));
    $smarty->assign("SELECT_HORAS",getSelectHorasMinutos());

    if(sizeof($arrAgendamientos)>0) $smarty->assign("arrAgendamientos",$arrAgendamientos);

    $content = $smarty->fetch("/var/www/html/modules/hispana_clientes_manuales/themes/default/agregar.tpl");
    return $content;
}

function reportListadodebases($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pListadodebases = new paloSantoClientesManuales($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Clientes manuales"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Clientes manuales"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value);
    $oGrid->setURL($url);

    $arrColumns = array(_tr("CI"),_tr("Nombre"),_tr("Apellido"),_tr("Provincia"),_tr("Ciudad"),_tr("Nacimiento"),_tr("Correo personal"),_tr("Correo trabajo"),_tr("Estado civil"),_tr("Origen"),_tr("Acción"));
    $oGrid->setColumns($arrColumns);

    $total   = $pListadodebases->getNumListadodebases($filter_field, $filter_value);
    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }else{
        //$limit  = 20;
	$limit  = 200;
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

    $arrResult =$pListadodebases->getListadodebases($limit, $offset, $filter_field, $filter_value);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0]  = $value['ci'];
	    $arrTmp[1]  = $value['nombre'];
	    $arrTmp[2]  = $value['apellido'];
	    $arrTmp[3]  = $value['provincia'];
	    $arrTmp[4]  = $value['ciudad'];
	    $arrTmp[5]  = $value['nacimiento'];
	    $arrTmp[6]  = $value['correo_personal'];
	    $arrTmp[7]  = $value['correo_trabajo'];
	    $arrTmp[8]  = $value['estado_civil'];
	    $arrTmp[9]  = $value['origen'];
	    $arrTmp[10]  = "<a href=index.php?menu=$module_name&action=agregar&ci=$value[ci]>Agregar a campaña</a><br><a href='?menu=hispana_clientes_datosbasicos&ci=".$value['ci']."'>Editar</a>";
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
	    "ci" => _tr("CI"),
	    "nombre" => _tr("Nombre"),
	    "apellido" => _tr("Apellido"),
	    "provincia" => _tr("Provincia"),
	    "ciudad" => _tr("Ciudad"),
	    "nacimiento" => _tr("Nacimiento"),
	    "correo_personal" => _tr("Correo Personal"),
	    "correo_trabajo" => _tr("Correo Trabajo"),
	    "estado_civil" => _tr("Estado civil"),
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
    else if(getParameter("action")=="agregar")      //Get parameter by GET (command pattern, links)
        return "agregar";
    else if(getParameter("action")=="save")      //Get parameter by GET (command pattern, links)
        return "save";
    else if(getParameter("action")=="view")      //Get parameter by GET (command pattern, links)
        return "view_form";
    else if(getParameter("action")=="view_edit")
        return "view_form";
    else
        return "report"; //cancel
}

function getSelectHorasMinutos()
{
    $select_horas = "<select name='horas'>";

    for($i=6;$i<=23;$i++){
	$hora = $i;
	if($i<10){
	    $hora = "0" . $i;
	}
	$select_horas .= "<option value=$hora>$hora</option>";
    }

    $select_horas .= "</select> ";

    $select_horas .= "<select name='minutos'>";

    for($i=0;$i<=55;$i=$i+5){
	$minutos = $i;
	if($i<10){
	    $minutos = "0" . $i;
	}
	$select_horas .= "<option value=$minutos>$minutos</option>";
    }

    $select_horas .= "</select>";
    return $select_horas;
}

function calendario($id_txt,$btn_txt,$valorGestionAnterior=NULL) {
    // antes del input name va un <td> luego del </a> va un </td> 
    
    return 
    "
    <input name='$id_txt' id='$id_txt' type='text' value='$valorGestionAnterior'
            style='width: 10em; color: #840; background-color: #fafafa; border: 1px solid #999999; text-align: center'/>
            <a href='#' id='$btn_txt'>
                <img align='middle' border='0' src='/libs/js/jscalendar/img.gif' alt='' />
            </a>


    <script type='text/javascript'>
        Calendar.setup(
            {
                'ifFormat':'%Y-%m-%d',
                'daFormat':'%Y-%m-%d',
                'firstDay':1,
                'showsTime':true,
                'showOthers':true,
                'timeFormat':24,
                'inputField':'$id_txt',
                'button':'$btn_txt'
            }
        );
    </script> 
    
    " ;
    
}

function _pre($array)
{
echo "<pre>";
print_r($array);
echo "</pre>";
}
?>