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
  $Id: paloSantoClientesagendados.class.php,v 1.1 2012-06-17 12:06:01 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoClientesCampania{
    var $_DB;
    var $errMsg;

    function paloSantoClientesCampania(&$pDB)
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
    function getNumClientesCampania($filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT COUNT(*) FROM _view_clientes_campania_recargable $where";
        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getClientesCampania($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT * FROM _view_clientes_campania_recargable $where LIMIT $limit OFFSET $offset";
        
        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getClientesagendadosById($id)
    {
        $query = "SELECT * FROM _view_clientes_agendados WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }
    
    function getAgentesCampania($id_campania)
    {
	$query = "SELECT id_agente 
		  FROM campania_agente 
		  WHERE id_campania=$id_campania 
		  AND status = 'A'";

        $result=$this->_DB->fetchTable($query,true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }

	foreach($result as $agente){
	    $arrAgente[$agente['id_agente']] = $agente['id_agente'];
	}
      
        return $arrAgente;
    }

    function reasignarAgenteAgendado($id_campania_cliente,$agente)
    {
	$query = "UPDATE campania_cliente 
		  SET agente_agendado='$agente'
		  WHERE id = $id_campania_cliente";

        $result=$this->_DB->genQuery($query);
    }

    function getInfo($id_campania_cliente)
    {
	$query = "SELECT * from _view_clientes_campania_recargable 
		  WHERE id_campania_cliente=$id_campania_cliente";
        $result=$this->_DB->getFirstRowQuery($query, true);

	$query = "SELECT id_agente from campania_agente 
		  WHERE 
		  id_campania=$result[id_campania] AND 
		  status='A'";
	
	$result2=$this->_DB->fetchTable($query, true);
	foreach($result2 as $agente){
	    $result['agentes'][] = $agente['id_agente'];
	}
	return $result;
    }

    function agendarAgenteCliente($DATA)
    {
	$query = "UPDATE campania_recargable_cliente
		  SET agente_agendado = '$DATA[agente]',
		  fecha_agendamiento = '" . $DATA['fecha'] . " " . $DATA['horas'] . ":" . $DATA['minutos'] . ":00',
		  fecha_status = '1970-01-01 00:00:00'      
		  WHERE id = $DATA[id_campania_cliente]";

        $result=$this->_DB->genQuery($query);
    }
}
?>