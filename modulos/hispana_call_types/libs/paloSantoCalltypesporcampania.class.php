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
  $Id: paloSantoCalltypesporcampaña.class.php,v 1.1 2012-03-30 04:03:53 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoCalltypesporcampania{
    var $_DB;
    var $errMsg;

    function paloSantoCalltypesporcampania(&$pDB)
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
    function getNumCalltypesporcampania($filter_field, $filter_value)
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

    function getCalltypesporcampania($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT * FROM calltype $where LIMIT $limit OFFSET $offset";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getCalltypesporcampaniaById($id,$id_campania=null)
    {
        if (!empty($id_campania)){
            $query = "SELECT c.nombre as 'campania', a.id, a.clase, a.descripcion, a.definicion, b.peso, a.status
		  FROM calltype AS a, calltype_campania AS b, campania AS c 
		  WHERE 
		  a.id = b.id_calltype AND 
		  b.id_campania = c.id AND
		  a.id = ? AND
                  b.id_campania = ?";
            $result=$this->_DB->getFirstRowQuery($query, true, array($id,$id_campania));
        }else{
            $query = "SELECT c.nombre as 'campania', a.* 
		  FROM calltype AS a, calltype_campania AS b, campania AS c 
		  WHERE 
		  a.id = b.id_calltype AND 
		  b.id_campania = c.id AND
		  a.id = ?";
            $result=$this->_DB->getFirstRowQuery($query, true, array($id));
        }
        
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getCampaigns()
    {
	// Consulta Campañas de tipo ORIGINAL y DERIVADA.
        $query = "SELECT id, nombre 
		  FROM campania 
		  WHERE status='A' 
		  AND tipo in ('ORIGINAL','DERIVADA','RECARGABLE')";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;

    }
    function getCalltypes()
    {
	// Consulta Campañas de tipo ORIGINAL y DERIVADA.
        $query = "SELECT *
		  FROM calltype
		  WHERE status='A' order by descripcion";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;

    }

    /**
      Esta función ha sido modificada.
    */
    function saveCallType($DATA)
    {		
	//Verifico si es un calltype nuevo
        if(isset($DATA["nuevo_calltype"])){
            // Guardo calltype maestro
            $query = "INSERT INTO calltype (clase, descripcion, definicion, peso, status) 
                      VALUES ('" . $DATA['clase'] . "','" . 
                                   $DATA['descripcion'] . "','" . 
                                   $DATA['definicion'] . "','" . 
                                   $DATA['peso'] . "','A')";
            $result=$this->_DB->genQuery($query);

            if($result==FALSE){
                $this->errMsg = $this->_DB->errMsg;
                return null;
            }
            $row = $this->_DB->getFirstRowQuery("SELECT LAST_INSERT_ID() AS id", true);
        }else{
            $row["id"] = $DATA["calltypes"];
        }
        
	// Guardo referencia calltype-campaña en tabla muchos a muchos
	$query = "INSERT INTO calltype_campania (id_calltype, id_campania,peso) 
		  VALUES ('" . $row['id'] . "','" . 
			       $DATA['campania'] . "',".
                            "'".$DATA['peso'].    "')";
        $result=$this->_DB->genQuery($query);

	//Guardo referencia calltype-campañas de regestión en tabla muchos a muchos
	$query = "INSERT INTO calltype_campania (id_calltype, id_campania) 
		  SELECT $row[id], id 
		  FROM campania WHERE campania_origen = $DATA[campania] AND tipo='REGESTION'";
	$result=$this->_DB->genQuery($query);

        return $result;
    }

    function updateCallType($DATA)
    {
	$query = "UPDATE calltype 
		  SET definicion='$DATA[definicion]'
		  WHERE id=$DATA[id]";
	$result=$this->_DB->genQuery($query);
        
        $query = "UPDATE calltype_campania 
		  SET peso=$DATA[peso]
		  WHERE id_calltype=$DATA[id] AND
                        id_campania=$DATA[id_campania]";
	$result=$this->_DB->genQuery($query);
        return $result;
    }
}
?>