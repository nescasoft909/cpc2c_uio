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
  $Id: index.php,v 1.1 2012-06-17 12:06:01 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoClientesCampania.class.php";

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
	case 'agendar':
	    $content = agendarEnCampania($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf);
	    break;
	case 'actualizar':
	    agendarAgenteCliente($_POST, $pDB);
        default:
            $content = reportClientesCampania($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function agendarAgenteCliente($DATA, $pDB)
{
    $pClienteCampania = new paloSantoClientesCampania($pDB);
    $pClienteCampania->agendarAgenteCliente($DATA);  
    
}

function agendarEnCampania($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{

    $pClienteCampania = new paloSantoClientesCampania($pDB);

    $arrClienteCampania = $pClienteCampania->getInfo(getParameter('id_campania_cliente'));

    $script="<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"libs/js/jscalendar/calendar-win2k-2.css\" />
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/lang/calendar-en.js\"></script>
    <script type=\"text/javascript\" src=\"libs/js/jscalendar/calendar-setup.js\"></script>";
    $smarty->assign("HEADER", $script);

    $smarty->assign("TITULO","Agendar cliente en campaña");

    $smarty->assign("CAMPANIA",$arrClienteCampania['campania']);
    $smarty->assign("ci",$arrClienteCampania['ci']);
    $smarty->assign("id_campania_cliente",$arrClienteCampania['id_campania_cliente']);
    $smarty->assign("CLIENTE",$arrClienteCampania['cliente']);
    $smarty->assign("AGENTES",$arrClienteCampania['agentes']);

    $smarty->assign("CALENDARIO", calendario("fecha","btn_fecha"));
    $smarty->assign("SELECT_HORAS", getSelectHorasMinutos());

    $content = $smarty->fetch("/var/www/html/modules/$module_name/themes/default/agendar.tpl");
    return $content;
}


/*
function reasignarAgenteCliente($DATA,$pDB)
{
    $pClientesagendados = new paloSantoClientesCampania($pDB);

    foreach($DATA as $k => $valor){ // Todo esto es para que funcione en Internet Explorer
	$arrTmp = explode("-",$k);
	// $arrTmp[0] = "CampaniaClienteAgente";
	// $arrTmp[1] = "nuevo/actual";
	// $arrTmp[2] = "36/45/78"; // Indice de la campania cliente
	$arrTmp[3] = $valor;
	if($arrTmp[0] == "CampaniaClienteAgente"){
	    $arrCampaniaClienteAgente[$arrTmp[2]][$arrTmp[1]] = $arrTmp[3];
	}
    }
    foreach($arrCampaniaClienteAgente as $k=>$regCampaniaAgente){
	if($regCampaniaAgente['nuevo']!=$regCampaniaAgente['actual']){
	    $pClientesagendados->reasignarAgenteAgendado($k,$regCampaniaAgente['nuevo']);
	}
    }
}
*/

function reportClientesCampania($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pClientesagendados = new paloSantoClientesCampania($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);

    $oGrid->setTplFile("/var/www/html/modules/$module_name/themes/default/_list.tpl");

    $oGrid->setTitle(_tr("Clientes por campaña"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Clientes por campaña"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value);
    $oGrid->setURL($url);

    $arrColumns = array(_tr("CI"),_tr("Cliente"),_tr("Campaña"),_tr("Agendamiento"),_tr("Agendado a"),_tr("Acción"));

    $oGrid->setColumns($arrColumns);

    $total   = $pClientesagendados->getNumClientesCampania($filter_field, $filter_value);
    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }
    else{
        $limit  = 100;
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }

    $arrResult =$pClientesagendados->getClientesCampania($limit, $offset, $filter_field, $filter_value);

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0] = $value['ci'];
	    $arrTmp[1] = $value['cliente'];
	    $arrTmp[2] = $value['campania'];	 
	    $arrTmp[3] = $value['fecha_agendamiento'];	 
	    $arrTmp[4] = $value['agente_agendado'];	 
	    //$arrTmp[3] = getFormSelectAgentesCampania($pClientesagendados,$value['id_campania'],$value['id_campania_cliente'],$value['agente_agendado']);
	    $arrTmp[5] = "<a href='index.php?menu=$module_name&id_campania_cliente=$value[id_campania_cliente]&action=agendar'>Agendar</a>/".
                         "<a href='index.php?menu=hispana_clientes_datosbasicos&ci=".$value['ci']."'>Editar</a>";
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
	    "cliente" => _tr("Cliente"),
	    "campania" => _tr("Campaña")	    
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
    else if(getParameter("action")=="agendar")
        return "agendar";
    else if(getParameter("action")=="actualizar")
        return "actualizar";
    else if(getParameter("action")=="Reasignar")
        return "reasignar";
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

function getFormSelectAgentesCampania($pClientesagendados,$id_campania,$id_campania_cliente,$agente_agendado)
{
    $arrAgentes = $pClientesagendados->getAgentesCampania($id_campania);
    $arrAgentes['CAMPAÑA'] = "CAMPAÑA";

    if(is_array($arrAgentes)){
	// $select = "<select name=campania_cliente_agente[" . $id_campania_cliente ."][nuevo]>\n"; // No funciona en Internet Explorer
	$select = "<select name=CampaniaClienteAgente-nuevo-" . $id_campania_cliente . ">\n";
	foreach($arrAgentes as $k => $agente){
	    $selected = "";
	    if($agente == $agente_agendado){
		$selected = " selected";
	    }
	    $select .= "<option id=\"$agente\" $selected>$agente</option>\n";	
	    $selected = "";
	}
	// $select .= "</select><input type=hidden name=campania_cliente_agente[" . $id_campania_cliente . "][actual] value=$agente_agendado>\n"; // No funciona en Internet Explorer
	$select .= "</select><input type=hidden name=CampaniaClienteAgente-actual-" . $id_campania_cliente . " value=$agente_agendado>\n";
    }
    return $select;
}

function _pre($array)
{
echo "<pre>";
print_r($array);
echo "</pre>";
}
?>