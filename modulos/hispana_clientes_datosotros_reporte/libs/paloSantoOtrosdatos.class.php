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
  $Id: paloSantoOtrosdatos.class.php,v 1.1 2012-04-29 12:04:36 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoOtrosdatos{
    var $_DB;
    var $errMsg;

    function paloSantoOtrosdatos(&$pDB)
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

    function getNumOtrosdatos($filter_field, $filter_value, $ci)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

	// $filter_field tiene el nombre de la tabla
        $query   = "SELECT COUNT(*) 
		    FROM $filter_field 
		    WHERE 
		    ci='$ci' 
		    AND status='A'";

        $result=$this->_DB->getFirstRowQuery($query, false);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }
    
    function getNumOtrosdatosIdCliente($filter_field, $filter_value, $id_cliente)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

	// $filter_field tiene el nombre de la tabla
        $query   = "SELECT COUNT(*) 
		    FROM cliente_gestion_adicionales 
		    WHERE 
		    id_cliente=$id_cliente 
		    AND status='A'";
        $result=$this->_DB->getFirstRowQuery($query, false);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }
    
    function getNumOtrosdatosRecargable($filter_field, $filter_value, $id)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $tipo=substr($filter_field,8);
            $where    = "and tipo ='$tipo'";
            $arrParam = array();
            if ($tipo!="telefono"&&$tipo!="direccion"){
                $where = "and tipo not in ('telefono','direccion')";
            }
            
        }
        
        $query   = "SELECT id_cliente from campania_recargable_cliente where id=$id";

        $result=$this->_DB->getFirstRowQuery($query, true);

	// $filter_field tiene el nombre de la tabla
        $query   = "SELECT COUNT(*) 
		    FROM cliente_gestion_adicionales 
		    WHERE 
		    id_cliente=".$result["id_cliente"]." 
		    AND status='A' $where";

        $result=$this->_DB->getFirstRowQuery($query, false);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getOtrosdatosRecargable($limit, $offset, $filter_field, $filter_value, $id)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $tipo=substr($filter_field,8);
            $where = "and tipo= '$tipo'";
            if ($tipo!="telefono"&&$tipo!="direccion"){
                $where = "and tipo not in ('telefono','direccion')";
            }
            
            $arrParam = array();
        }
        $query   = "SELECT id_cliente from campania_recargable_cliente where id=$id";
        $result=$this->_DB->getFirstRowQuery($query, true);
        // $query   = "SELECT * FROM table $where LIMIT $limit OFFSET $offset";
	// $filter_field tiene el nombre de la tabla
	$campo = substr($filter_field,8);
	$query   = "SELECT id,descripcion,adicional as valor FROM cliente_gestion_adicionales where id_cliente=".$result["id_cliente"]." and status='A' $where";
        
        $result=$this->_DB->fetchTable($query, true);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }
    
    function getOtrosdatos($limit, $offset, $filter_field, $filter_value, $ci)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        // $query   = "SELECT * FROM table $where LIMIT $limit OFFSET $offset";
	// $filter_field tiene el nombre de la tabla
	$campo = substr($filter_field,8);
	$query   = "SELECT id,descripcion,$campo as valor FROM $filter_field where ci='$ci' and status='A'";
        $result=$this->_DB->fetchTable($query, true);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        } 
        return $result;
    }
    
    function getOtrosdatosIdCliente($limit, $offset, $filter_field, $filter_value, $id_cliente)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }
        // $query   = "SELECT * FROM table $where LIMIT $limit OFFSET $offset";
	// $filter_field tiene el nombre de la tabla
	$campo = substr($filter_field,8);
        if($campo!="telefono"&&$campo!="direccion")
            $tipo="AND tipo not in ('telefono','direccion')";
        else{
            $tipo="AND tipo='$campo'";
        }
	$query   = "SELECT id,descripcion,adicional as valor FROM cliente_gestion_adicionales where id_cliente=$id_cliente and status='A' $tipo";
        $result=$this->_DB->fetchTable($query, true);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getOtrosdatosById($id,$filter_field)
    {
        $query = "SELECT * FROM $filter_field WHERE id=$id";

        $result=$this->_DB->getFirstRowQuery($query,true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
	//_pre($result);

        return $result;
    }
    
    function getOtrosdatosByIdRecargable($id,$filter_field)
    {
        $query = "SELECT * FROM cliente_gestion_adicionales WHERE id=$id";

        $result=$this->_DB->getFirstRowQuery($query,true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
	//_pre($result);

        return $result;
    }
    
    function desactivarDatoComplementario($tabla,$id)
    {
	$query = "UPDATE $tabla set status='E' where id = $id";
	$result = $this->_DB->genQuery($query);
    }
    function desactivarDatoComplementarioRecargable($tabla,$id)
    {
	$query = "UPDATE cliente_gestion_adicionales set status='E' where id = $id";
	$result = $this->_DB->genQuery($query);
    }

    function guardarDatoComplementario($tabla,$dato,$descripcion,$ci)
    {
	$query = "INSERT into $tabla (" .
		  substr($tabla,8) . ",ci,descripcion,id_base,status) 
		  VALUES ('$dato','$ci','$descripcion',99,'A')";
	$this->_DB->genQuery($query);
    }
    
    function guardarDatoComplementarioRecargable($tabla,$dato,$descripcion,$id)
    {
        $query   = "SELECT * from campania_recargable_cliente where id=$id";
        $result=$this->_DB->getFirstRowQuery($query, true);
        
        $tipo=substr($tabla,8);
	$query = "INSERT into cliente_gestion_adicionales (tipo,id_cliente,adicional,descripcion,id_base,status) 
		  VALUES ('$tipo',".$result["id_cliente"].",'$dato','$descripcion',".$result["id_base_cliente"].",'A')";
	$this->_DB->genQuery($query);
    }
    
    function guardarDatoComplementarioRecargableId($tabla,$dato,$descripcion,$id)
    {
        $query   = "SELECT * from cliente_gestion where id=$id";
        $result=$this->_DB->getFirstRowQuery($query, true);
        
        $tipo=substr($tabla,8);
	$query = "INSERT into cliente_gestion_adicionales (tipo,id_cliente,adicional,descripcion,id_base,status) 
		  VALUES ('$tipo',".$result["id"].",'$dato','$descripcion',".$result["id_base"].",'A')";
	$this->_DB->genQuery($query);
    }

    function actualizarDatoComplementario($tabla,$id,$dato,$descripcion)
    {
	$query = "UPDATE $tabla 
		  SET " . substr($tabla,8) . "='$dato',descripcion='$descripcion'
		  WHERE id=$id";
	$this->_DB->genQuery($query);
    }
    function actualizarDatoComplementarioRecargable($tabla,$id,$dato,$descripcion)
    {
        $tipo=substr($tabla,8);
	$query = "UPDATE cliente_gestion_adicionales 
		  SET adicional='$dato',descripcion='$descripcion',tipo='$descripcion'
		  WHERE id=$id";
	$this->_DB->genQuery($query);
    }


}
?>