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
  $Id: paloSantoReportedetalladodegestión.class.php,v 1.1 2012-04-12 01:04:03 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoReportedetalladodegestión{
    var $_DB;
    var $errMsg;

    function paloSantoReportedetalladodegestión(&$pDB)
    {
        // Se recibe como parámetro una referencia a una conexión paloDB
        if (is_object($pDB)) {
            $this->_DB =& $pDB;
            $this->errMsg = $this->_DB->errMsg;
        } else {
            $dsn = (string)$pDB;
            $this->_DB = new paloDB($dsn);

            if (!$this->_DB->connStatus) {
                $this->errMsg = $this->_DB->errMsg;
                // debo llenar alguna variable de error
            } else {
                // debo llenar alguna variable de error
            }
        }
    }

    /*HERE YOUR FUNCTIONS*/

    function getNumReportedetalladodegestion($filter_field) // Sólo recibo filter_field para obtener el id de la campaña
    {
	/*
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }
	*/
        $query   = "SELECT COUNT(*) as cont 
		    FROM _view_gestion_detallada
		    WHERE id_campania=$filter_field";
        $result=$this->_DB->getFirstRowQuery($query, false);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        return $result[0];
    }

    function getReportedetalladodegestion($limit, $offset, $filter_field)  // Sólo recibo filter_field para obtener el id de la campaña
    {
	/*
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }
	*/
        $query   = "SELECT * FROM _view_gestion_detallada
		    WHERE id_campania=$filter_field 
		    LIMIT $limit OFFSET $offset";
        $result=$this->_DB->fetchTable($query, true); 

	echo $query;

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        return $result;
    }

    function getReportedetalladodegestiónById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getReportedeCalltypes($limit, $offset, $filter_field, $filter_value)
    {
        if(isset($filter_field) & $filter_field !=""){
            $where    = "WHERE id_campania = $filter_field";
            // $arrParam = array("$filter_value%");
        }

        $query   = "SELECT * 
		    FROM _view_gestion_detallada
		    $where LIMIT $limit OFFSET $offset";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getCampaigns()
    {
        $query   = "SELECT id, nombre 
		    FROM campania 
		    WHERE tipo='ORIGINAL' AND 
		    status='A'";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        return $result;
    }

    function getFormFields($id_campania)
    {
	$query   = "SELECT a.id, a.etiqueta 
		    FROM form_field AS a, campania AS b 
		    WHERE a.id_form=b.id_form
		    AND a.tipo != 'LABEL'
		    AND b.id = $id_campania
		    ORDER BY orden";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        return $result;
    }

    function getFormValues($id_gestion_campania)
    {
	$query   = "SELECT a.id_gestion_campania,a.id_form_field,valor 
		    FROM gestion_campania_detalle as a, form_field as b 
		    WHERE a.id_form_field=b.id 
		    AND id_gestion_campania=$id_gestion_campania 
		    ORDER BY id_gestion_campania, orden";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        return $result;

    }

}
?>
