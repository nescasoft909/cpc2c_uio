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
  $Id: index.php,v 1.1 2012-04-29 12:04:36 Juan Pablo Romero jromero@palosanto.com Exp $ */
//include elastix framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoOtrosdatos.class.php";

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

    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    if(isset($_POST['ingresar']) && $_POST['ingresar']=="Ingresar"){
	$action = "view_form";
    }
    if(isset($_POST['save_edit']) && $_POST['save_edit']=="Editar"){
	actualizarDatoComplementario($_POST['tabla'],$_POST['id'],$_POST['dato1'],$_POST['dato2'],$pDB);
	$_POST['filter_field']=$_POST['tabla'];
    }
    

    switch($action){
	case "view_edit":
	case "view_form": // no necesariamente sólo viene por $_POST, tambien viene por $_GET
	    if($filter_field=="cliente_telefono" || $filter_field=="cliente_direccion" || $filter_field=="cliente_adicional"){
		    $content = viewFormIngresodeotrosdatos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
	    }
	    break;

	case "delete":
	    if(isset($_GET['filter_field']) && isset($_GET['id'])){
		desactivarDatoComplementario($filter_field,$_GET['id'],$pDB);
		// $_GET['filter_field'] = $_GET['table']; // para no borrar el listado
	    }
	case "save_new":
	    if(isset($_POST['dato1']) && $_POST['dato1']!=""){
		guardarDatoComplementario($_POST['tabla'],$_POST['dato1'],$_POST['dato2'],$pDB);
		$_GET['filter_field'] = $_POST['tabla']; // para no borrar el listado
	    }

        default:
            $content = reportOtrosdatos($smarty, $module_name, $local_templates_dir, $pDB, $arrConf);
            break;
    }
    return $content;
}

function guardarDatoComplementario($tabla,$dato,$descripcion,$pDB)
{
    $pOtrosdatos = new paloSantoOtrosdatos($pDB);
    if (!empty($_SESSION["id_campania_cliente_recargable"])){
        $pOtrosdatos->guardarDatoComplementarioRecargable($tabla,$dato,$descripcion,$_SESSION["id_campania_cliente_recargable"]);
    }elseif(!empty($_SESSION["id_cliente"])){
        $pOtrosdatos->guardarDatoComplementarioRecargableId($tabla,$dato,$descripcion,$_SESSION['id_cliente']);
    }else{
        $pOtrosdatos->guardarDatoComplementario($tabla,$dato,$descripcion,$_SESSION['ci']);
    }
    
}

function actualizarDatoComplementario($tabla,$id,$dato,$descripcion,$pDB)
{
    $pOtrosdatos = new paloSantoOtrosdatos($pDB);
    if (!empty($_SESSION["id_campania_cliente_recargable"])){
        $pOtrosdatos->actualizarDatoComplementarioRecargable($tabla,$id,$dato,$descripcion);
    }else{
        $pOtrosdatos->actualizarDatoComplementario($tabla,$id,$dato,$descripcion);
    }
    
}

function desactivarDatoComplementario($tabla,$id,$pDB)
{
    $pOtrosdatos = new paloSantoOtrosdatos($pDB);
    if (!empty($_SESSION["id_campania_cliente_recargable"])){
        $pOtrosdatos->desactivarDatoComplementarioRecargable($tipo, $id); 
    }else{
        $pOtrosdatos->desactivarDatoComplementario($tabla, $id); 
    }
    
}

function viewFormIngresodeotrosdatos($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pOtrosdatos = new paloSantoOtrosdatos($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    $arrFormIngresodeotrosdatos = createFieldForm($filter_field); // antes estaba como $_POST['filter_field']

    $oForm = new paloForm($smarty,$arrFormIngresodeotrosdatos);

    //begin, Form data persistence to errors and other events.
    $_DATA  = $_POST;
    $action = getParameter("action");
    $id     = getParameter("id");
    $smarty->assign("ID", $id); //persistence id with input hidden in tpl
    $smarty->assign("tabla", $filter_field); //persistence id with input hidden in tpl

    if($action=="view")
        $oForm->setViewMode();
    else if($action=="view_edit" || getParameter("save_edit"))
        $oForm->setEditMode();
    //end, Form data persistence to errors and other events.

    if($action=="view" || $action=="view_edit"){ // the action is to view or view_edit.
         if (!empty($_SESSION["id_campania_cliente_recargable"])){
             $dataIngresodeotrosdatos = $pOtrosdatos->getOtrosdatosByIdRecargable($id,$filter_field); // debería enviarse el nombre de la tabla contenido en filter_field
         }else{
             $dataIngresodeotrosdatos = $pOtrosdatos->getOtrosdatosById($id,$filter_field); // debería enviarse el nombre de la tabla contenido en filter_field
         }
        
        if(is_array($dataIngresodeotrosdatos) & count($dataIngresodeotrosdatos)>0){
            // $_DATA = $dataIngresodeotrosdatos;
	    $_DATA['dato1'] = $dataIngresodeotrosdatos['adicional'];
	    $_DATA['dato2'] = $dataIngresodeotrosdatos['descripcion'];
        }else{
            $smarty->assign("mb_title", _tr("Error get Data"));
            $smarty->assign("mb_message", $pIngresodeotrosdatos->errMsg);
        }
    }

    $smarty->assign("SAVE", _tr("Save"));
    $smarty->assign("EDIT", _tr("Edit"));
    $smarty->assign("CANCEL", _tr("Cancel"));
    $smarty->assign("REQUIRED_FIELD", _tr("Required field"));
    $smarty->assign("IMG", "images/list.png");
    
    $htmlForm = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Ingreso de datos complementarios"), $_DATA);
    $content = "<form  method='POST' style='margin-bottom:0;' action='?menu=$module_name'>".$htmlForm."</form>";
    return $content;
}



function reportOtrosdatos($smarty, $module_name, $local_templates_dir, &$pDB, $arrConf)
{
    $pOtrosdatos = new paloSantoOtrosdatos($pDB);
    $filter_field = getParameter("filter_field");
    $filter_value = getParameter("filter_value");

    //begin grid parameters
    $oGrid  = new paloSantoGrid($smarty);
    $oGrid->setTitle(_tr("Datos complementarios"));
    $oGrid->pagingShow(true); // show paging section.

    $oGrid->enableExport();   // enable export.
    $oGrid->setNameFile_Export(_tr("Datos complementarios"));

    $url = array(
        "menu"         =>  $module_name,
        "filter_field" =>  $filter_field,
        "filter_value" =>  $filter_value);
    $oGrid->setURL($url);

    switch($filter_field){
	case "cliente_direccion":
	    $arrColumns = array(_tr("Descripción"),_tr("Dirección"),_tr("Acción"));
	    break;

	case "cliente_adicional":
	    $arrColumns = array(_tr("Descripción"),_tr("Dato adicional"),_tr("Acción"));
	    break;

	case "cliente_telefono":
	default: $arrColumns = array(_tr("Descripción"),_tr("Número"),_tr("Acción"));
	    break;
    }
    $oGrid->setColumns($arrColumns);
    if (!empty($_SESSION["id_campania_cliente_recargable"])){
        $total   = $pOtrosdatos->getNumOtrosdatosRecargable($filter_field, $filter_value, $_SESSION['id_campania_cliente_recargable']);
    }elseif(!empty($_SESSION['id_cliente'])){
        $total   = $pOtrosdatos->getNumOtrosdatosIdCliente($filter_field, $filter_value, $_SESSION['id_cliente']);
    }else{
        $total   = $pOtrosdatos->getNumOtrosdatos($filter_field, $filter_value, $_SESSION['ci']);
    }
    

    $arrData = null;
    if($oGrid->isExportAction()){
        $limit  = $total; // max number of rows.
        $offset = 0;      // since the start.
    }
    else{
        $limit  = 20;
        $oGrid->setLimit($limit);
        $oGrid->setTotal($total);
        $offset = $oGrid->calculateOffset();
    }
    if (!empty($_SESSION["id_campania_cliente_recargable"])){
        $arrResult =$pOtrosdatos->getOtrosdatosRecargable($limit, $offset, $filter_field, $filter_value, $_SESSION["id_campania_cliente_recargable"]);
    }elseif(!empty($_SESSION['id_cliente'])){
        $arrResult   = $pOtrosdatos->getOtrosdatosIdCliente($limit, $offset, $filter_field, $filter_value, $_SESSION['id_cliente']);
    }else{
        $arrResult =$pOtrosdatos->getOtrosdatos($limit, $offset, $filter_field, $filter_value, $_SESSION['ci']);
    }
    
     //_pre($arrResult);  

    if(is_array($arrResult) && $total>0){
        foreach($arrResult as $key => $value){ 
	    $arrTmp[0] = $value['descripcion'];
	    $arrTmp[1] = $value['valor'];
	    $arrTmp[2] = "<a href=index.php?menu=hispana_clientes_datosotros_reporte&id=$value[id]&action=delete&filter_field=$filter_field onclick = \"if (! confirm('¿Realmente desea desactivarlo?')) return false;\">Desactivar</a>";

	    if($filter_field == "cliente_adicional"){ // se pueden editar los adicionales del cliente
		$arrTmp[2] .= " <a href=index.php?menu=hispana_clientes_datosotros_reporte&id=$value[id]&action=view_edit&filter_field=$filter_field>Editar</a>";
	    }
	    
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
	    "cliente_telefono" => _tr("Teléfonos"),
	    "cliente_direccion" => _tr("Direcciones"),
	    "cliente_adicional" => _tr("Datos adicionales"),
                    );

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

function createFieldForm($tabla) // cliente_telefono, cliente_direccion, cliente_adicional
{
    if($tabla == "cliente_telefono"){
	$arrOptions = array('Oficina' => 'Oficina', 
			    'Domicilio' => 'Domicilio', 
			    'Celular' => 'Celular',
			    'Otro' => 'Otro');
	$label = "Teléfono";

    }elseif($tabla == "cliente_direccion"){
	$arrOptions = array('Oficina' => 'Oficina', 
			    'Domicilio' => 'Domicilio', 
			    'Otro' => 'Otro');
	$label = "Dirección";
    }


    if($tabla == "cliente_telefono" || $tabla == "cliente_direccion"){
	$arrFields = array(
		"dato1"   => array(      "LABEL"                  => _tr($label),
						"REQUIRED"               => "no",
						"INPUT_TYPE"             => "TEXT",
						"INPUT_EXTRA_PARAM"      => "",
						"VALIDATION_TYPE"        => "text",
						"VALIDATION_EXTRA_PARAM" => ""
						),
		"dato2"   => array(      "LABEL"                  => _tr("Descripción"),
						"REQUIRED"               => "no",
						"INPUT_TYPE"             => "SELECT",
						"INPUT_EXTRA_PARAM"      => $arrOptions,
						"VALIDATION_TYPE"        => "text",
						"VALIDATION_EXTRA_PARAM" => "",
						"EDITABLE"               => "si",
						)
    

            );
    }elseif($tabla == "cliente_adicional"){
	$label = "Dato Adicional";
	$arrFields = array(
		"dato1"   => array(      "LABEL"                  => _tr($label),
						"REQUIRED"               => "no",
						"INPUT_TYPE"             => "TEXT",
						"INPUT_EXTRA_PARAM"      => "",
						"VALIDATION_TYPE"        => "text",
						"VALIDATION_EXTRA_PARAM" => ""
						),
		"dato2"   => array(      "LABEL"                  => _tr("Descripción"),
						"REQUIRED"               => "no",
						"INPUT_TYPE"             => "TEXT",
						"INPUT_EXTRA_PARAM"      => "",
						"VALIDATION_TYPE"        => "text",
						"VALIDATION_EXTRA_PARAM" => ""						
						)
    

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
        return "view_edit";
    else if(getParameter("action")=="delete")
        return "delete";
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