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
  $Id: paloSantoEliminacióndebases.class.php,v 1.1 2012-03-30 06:03:52 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoEliminacióndebases{
    var $_DB;
    var $errMsg;

    function paloSantoEliminacióndebases(&$pDB)
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

    function getNumEliminacióndebases($filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT COUNT(*) FROM table $where";

        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getEliminacióndebases($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT * FROM table $where LIMIT $limit OFFSET $offset";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getEliminacióndebasesById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }


    function getBasesNoAsignadas()
    {
        $query = "SELECT id,nombre
		  FROM base
		  WHERE id NOT IN
		  (
		    SELECT distinct a.id_base
		    FROM campania_base as a, campania_cliente as b
		    WHERE a.id_campania=b.id_campania
		  )and id not in(
		    SELECT distinct a.id_base
		    FROM campania_base as a, campania_recargable_cliente as b
		    WHERE a.id_campania=b.id_campania
                    and b.status is not null
                  )";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;

    }

    function eliminarBase($id)
    {
	$arrQueries = array("DELETE FROM base_cliente where id_base=",
			    "DELETE FROM cliente_telefono where id_base=",
			    "DELETE FROM cliente_direccion where id_base=",
			    "DELETE FROM cliente_adicional where id_base=",
			    "DELETE FROM cliente where id_base=",
            "DELETE FROM cliente_gestion where id_base=",
            "DELETE FROM campania_recargable_cliente where id_base_cliente=",
            "DELETE FROM cliente_gestion_adicionales where id_base=",
			    "DELETE FROM base where id=");

	foreach($arrQueries as $query){
	    $result = $this->_DB->genQuery($query . "" . $id);
	    if($result==FALSE){
		$this->errMsg = $this->_DB->errMsg;
		echo $this->errMsg;
		return null;
        }

	}

    }

}
?>
