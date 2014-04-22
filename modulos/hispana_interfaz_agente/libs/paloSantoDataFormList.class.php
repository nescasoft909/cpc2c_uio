<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Elastix version 0.5                                                  |
  | http://www.elastix.org                  require_once("libs/smarty/libs/Smarty.class.php");                             |
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
  $Id: formulario $ */
//require_once("libs/js/jscalendar/calendar.php"); 

include_once("libs/paloSantoDB.class.php");
include_once("/var/www/html/modules/hispana_interfaz_agente/libs/paloSantoInterfazdeAgente.class.php");

/* Basada en la clase que implementa Formulario de Campaign de CallCenter (CC) */
class paloSantoDataForm
{
    var $_db; // instancia de la clase paloDB
    var $errMsg;
    var $rutaDB;
    function paloSantoDataForm($pDB)
    {
        // Se recibe como parámetro una referencia a una conexión paloDB
        if (is_object($pDB)) {
            $this->_db =& $pDB;
            $this->errMsg = $this->_db->errMsg;
        } else {
            $dsn = (string)$pDB;
            $this->_db = new paloDB($dsn);

            if (!$this->_db->connStatus) {
                $this->errMsg = $this->_db->errMsg;
                // debo llenar alguna variable de error
            } else {
                // debo llenar alguna variable de error
            }
        }
    }

    function getFormularios($id_formulario = NULL,$estatus='all')
    {
        $arr_result = FALSE;
        
        $where = "";
        if($estatus=='all')
            $where .= "where 1";
        else if($estatus=='A')
            $where .= "where f.estatus='A'";
        else if($estatus=='I')
            $where .= "where f.estatus='I'";
        if(!is_null($id_formulario))
            $where .= " and f.id = $id_formulario";

        if (!is_null($id_formulario) && !ereg('^[[:digit:]]+$', "$id_formulario")) {
            $this->errMsg = _tr("Form ID is not valid");
        } 
        else {
            $this->errMsg = "";
            $sPeticionSQL = "SELECT f.id, f.nombre, f.descripcion, f.estatus FROM form f $where";
            $arr_result =& $this->_db->fetchTable($sPeticionSQL, true);
            if (!is_array($arr_result)) {
                $arr_result = FALSE;
                $this->errMsg = $this->_DB->errMsg;
            }
        }
        return $arr_result;
    }

    function obtener_campos_formulario($id_formulario, $id_campo=NULL, $numTelefono, $DATA, $arrValoresGestionAnterior=NULL) //pido la DATA por $_GET ( cambios requeridos para HISPANA)
    {
        $respuesta = new xajaxResponse();
        $smarty = $this->getSmarty();
        $errMsg = ""; 
        $sqliteError = '';
        $arrReturn=array();
        $where = "";
        $codigo_js = "";

	$id_campania = $DATA['id_campania'];
	$datosCampania = $this->getNombreScript($id_campania);
	$script = $datosCampania['script'];
	$nombre_campania = $datosCampania['nombre'];

	/** Los valores de la gestión anterior puede ser de la misma campaña u otra 
	    (siempre que sea de la misma campaña consolidada)
	*/
	$call_type_input = $this->getCallTypeInput($id_campania,$arrValoresGestionAnterior['calltype']);
	$hidden = $this->getHiddenData($DATA);
        if (!empty($DATA["id_campania_cliente_recargable"])){
            $arrInfoCliente = $this->obtenerInfoClienteRecargable($DATA['id_campania_cliente_recargable'],$numTelefono);
            $datoBase = $this->obtenerBaseRecargable($DATA['id_campania_cliente_recargable']);
        }else{
            $arrInfoCliente = $this->obtenerInfoCliente($DATA['ci'],$numTelefono);
            $datoBase = $this->obtenerBase($DATA['id_campania_cliente']);
        }

	

	$smarty->assign("SCRIPT", $script); //Workaround para Hispana
	$smarty->assign("CALLTYPE_LABEL", "<b>Call Type</b>"); //Workaround para Hispana
	$smarty->assign("CALLTYPE_INPUT", $call_type_input); //Workaround para Hispana
  	$smarty->assign("arrInfoCliente", $arrInfoCliente); //Workaround para Hispana
	$smarty->assign("HIDDEN_INPUT", $hidden); //Workaround para Hispana

        if(!is_null($id_campo))
            $where = " and fd.id=$id_campo";

        $query  = "
                    SELECT  fd.id id_field, fd.etiqueta, fd.value value_field, fd.tipo, fd.orden, fd.id_form 
                    FROM  form_field fd
                    where fd.id_form = $id_formulario AND status='A' $where order by fd.orden";

        $arr_fields = $this->_db->fetchTable($query, true);
//print_r($arr_fields);
        if (is_array($arr_fields) && count($arr_fields)>0) {
	    
            $id = $arr_fields[0]["id_form"];
            foreach($arr_fields as $key=>$field) {
                $funcion_js = "";
		
		if(isset($arrValoresGestionAnterior[$field['id_field']])){
		    $input = $this->crea_objeto($smarty, $field, "", $funcion_js, $arrValoresGestionAnterior[$field['id_field']]);
		} else {
		    $input = $this->crea_objeto($smarty, $field, "", $funcion_js);
		}

                
                $etiqueta = $field["etiqueta"];
                $tipo = $field["tipo"];
                $data_field[] = array("TYPE" => $tipo, "TAG" => $etiqueta, "INPUT" => $input, "ID_FORM" => $id);
                //$data_field[] = array("TAG" => $etiqueta, "INPUT" => $input, "ID_FORM" => $id);
                $id = "";
                $codigo_js .= $funcion_js;


//                $smarty->assign("FORMULARIO", $data_field); //OK
                $smarty->assign("FORMULARIO", $data_field); //WRONG
                $smarty->assign("formularios", _tr("Form"));
                $mostrar_template=true;
            }
            if ($mostrar_template) $template = "formulario.tpl";
            else $template = "vacio.tpl";
        }else{
            //$smarty->assign("no_definidos_formularios",_tr('Forms Nondefined'));
            $template = "vacio.tpl";
        }
        if (isset($codigo_js) && trim($codigo_js)!="") {
           $respuesta->addScript($codigo_js);
        }

	$calendario_agendamiento = $this->calendario("fecha","agendar"); // ($id_txt,$btn_txt)
	$smarty->assign("CALENDARIO", $calendario_agendamiento);
	$select_horas = $this->getSelectHorasMinutos();

	$smarty->assign("SELECT_HORAS", $select_horas);
	$smarty->assign("ID_CAMPANIA", $id_campania);
	$smarty->assign("NOMBRE_CAMPANIA", $nombre_campania);
        $smarty->assign("NOMBRE_BASE", $datoBase);
        
	$smarty->assign("ELASTIX_USER",$_SESSION['elastix_user']);
	$smarty->assign("OBSERVACION",$arrValoresGestionAnterior['observacion']);

        $texto_formulario=$smarty->fetch("file:/var/www/html/modules/hispana_interfaz_agente/themes/default/$template");
        return $texto_formulario;
        //$respuesta->addAssign("contenedor_formulario","innerHTML",$texto_formulario);
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


    function obtenerInfoCliente($ci, $numTelefono)
    {
	$pInterfazdeAgente = new paloSantoInterfazdeAgente($this->_db);
	$arrInfoCliente = $pInterfazdeAgente->obtenerClientePorCI($ci);
	// print_r($arrInfoCliente);
	$arrInfoCliente['numero'] = $numTelefono;
	return $arrInfoCliente;
    }
    
    function obtenerInfoClienteRecargable($id_campania_cliente, $numTelefono)
    {
	$pInterfazdeAgente = new paloSantoInterfazdeAgente($this->_db);
	$arrInfoCliente = $pInterfazdeAgente->obtenerClientePorIdCampaniaClienteRecargable($id_campania_cliente);
	// print_r($arrInfoCliente);
        $id=0;
        foreach($arrInfoCliente["direccion"] as $direccion){
            $arrInfoCliente["direccion"][$id]["direccion"]=$direccion["direccion"];
            $id+=1;
        }
	$arrInfoCliente['numero'] = $numTelefono;
	return $arrInfoCliente;
    }

    function getHiddenData($DATA)
    {
	$hiddenData  = "<input type=\"hidden\" name=\"id_campania_cliente\" value=\"$DATA[id_campania_cliente]\">\n";
	$hiddenData .= "<input type=\"hidden\" name=\"id_campania\" value=\"$DATA[id_campania]\">\n";
	$hiddenData .= "<input type=\"hidden\" name=\"telefono\" value=\"$DATA[telefono]\">\n";
	$hiddenData .= "<input type=\"hidden\" name=\"action\" value=\"guardar\">\n";
	$hiddenData .= "<input type=\"hidden\" name=\"timestamp\" value=\"" . microtime(true) . "\">\n";
	return $hiddenData;
    }

    function obtenerBaseRecargable($id_campania_cliente)
    {
        $query = "select b.nombre as base from campania_recargable_cliente crc, base b where b.id=crc.id_base_cliente and crc.id=$id_campania_cliente";
        $result=$this->_db->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_db->errMsg;
            return false;
        }else{
	    return $result['base'];
	}
    }
    
    function obtenerBase($id_campania_cliente)
    {
        $query = "select b.nombre as base from campania_cliente crc, base b,campania_base cb 
                  where b.id=cb.id_base and crc.id_campania=cb.id_campania and crc.id=$id_campania_cliente";
        $result=$this->_db->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_db->errMsg;
            return false;
        }else{
	    return $result['base'];
	}
    }
    
    function getNombreScript($id_campania) //función nueva para Hispana de Seguros
    {
        $query  = "SELECT nombre, script FROM campania WHERE id=$id_campania";
        $result = $this->_db->getFirstRowQuery($query, true);

	$result['script'] = nl2br($result['script']);
	return $result;
    }

    function getCallTypeInput($id_campania, $callTypeAnterior)
    {
	$query  = "SELECT a.id, concat(a.clase,' - ',a.descripcion) as descripcion, a.peso
		   FROM calltype AS a, calltype_campania AS b 
		   WHERE b.id_campania=$id_campania 
		   AND b.id_calltype=a.id
		   AND a.status='A' 
                   and b.status='A'
		   ORDER BY clase,descripcion";
        $arrCallTypes = $this->_db->fetchTable($query, true);
	$callTypeInput = "<select name='calltype'>\n";
	$callTypeInput .= "<option value=0> - Calltypes - </option>\n";
	foreach($arrCallTypes as $calltype){
	    if($callTypeAnterior == $calltype['id']){
	    $selected = "selected";
	    }
	    $callTypeInput .= "<option value=$calltype[id] $selected>" . $calltype['descripcion'] . "</option>\n"; 
	    $selected = "";
	}
	$callTypeInput .= "</select>\n";

	return $callTypeInput;
    }

    function getSmarty() {
    global $arrConf;
    $smarty = new Smarty();
    $smarty->template_dir = "themes/default/";
    $smarty->compile_dir =  "var/templates_c/";
    $smarty->config_dir =   "configs/";
    $smarty->cache_dir =    "var/cache/";
    return $smarty;
    }

    function crea_objeto(&$smarty, $field, $prefijo_objeto, &$funcion_js, $valorGestionAnterior=NULL) {

	if(!isset($valorGestionAnterior)){
	    $valorGestionAnterior = "";
	}

        $tipo_objeto = $field["tipo"];
        $input="";
        switch ($tipo_objeto) {
            case "LIST":
                $listado = explode(",",$field["value_field"]);
                $input = "";
                $selected="";
                foreach($listado as $key=>$item) {
                    if (trim($valorGestionAnterior) == trim($item)){
			$selected = "selected";
		    }

                    if($item!=""){ 
			$input .= "<option value='$item' $selected>$item</option>";
			$selected = "";
		    }
                }
                if ($input!="") {
                    $input = "<select name='$prefijo_objeto"."$field[id_field]' id='$prefijo_objeto"."$field[id_field]' class='SELECT'>$input</select>";
                }
            break;

            case "DATE":
                $input = $this->calendario($field['id_field'],"btn_".$field['id_field'],$valorGestionAnterior);
            break;
            case "TEXTAREA":
                $input = "<textarea name='$prefijo_objeto"."$field[id_field]' id='$prefijo_objeto"."$field[id_field]' rows='3' cols='50'>$valorGestionAnterior</textarea>";
            break;
            case "LABEL":
                $input = "<label class='style_label'>$field[etiqueta]</label>";
            break;
            default:
                $input = "<input type='text' name='$prefijo_objeto"."$field[id_field]' id='$prefijo_objeto"."$field[id_field]' value='$valorGestionAnterior' class='INPUT'>";
        }

        return $input;
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

}

?>
