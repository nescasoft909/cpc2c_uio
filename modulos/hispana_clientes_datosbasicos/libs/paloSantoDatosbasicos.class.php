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
  $Id: paloSantoDatosbasicos.class.php,v 1.1 2012-04-28 10:04:22 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoDatosbasicos{
    var $_DB;
    var $errMsg;

    function paloSantoDatosbasicos(&$pDB)
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

    function getNumDatosbasicos($filter_field, $filter_value)
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

    function getDatosbasicos($limit, $offset, $filter_field, $filter_value)
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

    function getDatosbasicosById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getDatosbasicosByCI($ci)
    {
        $query = "SELECT * FROM cliente WHERE ci='$ci'";

        $result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }
    
    function getDatosbasicosByIdCliente($id_cliente)
    {
        $query = "SELECT * FROM cliente_gestion WHERE id=$id_cliente";
        
        $result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }
    
    function getDatosbasicosByIdCampaniaRecargable($id)
    {
        $query = "SELECT c.* FROM cliente_gestion c, campania_recargable_cliente crc WHERE c.id=crc.id_cliente and crc.id=$id";
        
        $result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        $result["id_campania_recargable"]=$id;
        return $result;
    }

    function actualizarDatosClienteFull($DATA, $user)
    {
        $query = "SELECT * FROM cliente WHERE ci='$DATA[cedula]'";

        $result=$this->_DB->getFirstRowQuery($query, true);
        if(!empty($result)){
            $this->errMsg = "Ya existe un usuario registrado con esa cedula";
            return false;
        }
        $query = "UPDATE cliente_telefono 
		  SET 
		  ci = '$DATA[cedula]'
		  WHERE ci = '$DATA[ci]'";
        $result=$this->_DB->genQuery($query);
        
        
        $query = "UPDATE cliente_direccion 
		  SET 
		  ci = '$DATA[cedula]'
		  WHERE ci = '$DATA[ci]'";
        $result=$this->_DB->genQuery($query);
        
        $query = "UPDATE cliente_adicional 
		  SET 
		  ci = '$DATA[cedula]'
		  WHERE ci = '$DATA[ci]'";
        $result=$this->_DB->genQuery($query);
            
        $query = "UPDATE base_cliente 
		  SET 
		  ci = '$DATA[cedula]'
		  WHERE ci = '$DATA[ci]'";
        $result=$this->_DB->genQuery($query);
        
	$query = "UPDATE cliente 
		  SET 
                  ci = '$DATA[cedula]',
		  nombre = '$DATA[nombre]',
		  apellido = '$DATA[apellido]',
		  ciudad = '$DATA[ciudad]',
		  provincia = '$DATA[provincia]',
		  nacimiento = '$DATA[nacimiento]',
		  correo_personal = '$DATA[correo_personal]',
		  correo_trabajo = '$DATA[correo_trabajo]',
		  estado_civil = '$DATA[estado_civil]'
		  WHERE ci = '$DATA[ci]'";
	$result=$this->_DB->genQuery($query);	      

	unset($DATA['id']) ;
	unset($DATA['save_edit']) ;
	$query = "INSERT INTO audit_actualizacion_clientes
		  (ci,usuario,data)
		  VALUES 
		  ('$DATA[ci]','$user','" . print_r($DATA,true) . "')";
	$result=$this->_DB->genQuery($query);	      
        
        return true;
    }
    
    function actualizarDatosCliente($DATA, $user)
    {
        
	$query = "UPDATE cliente 
		  SET 
		  nombre = '$DATA[nombre]',
		  apellido = '$DATA[apellido]',
		  ciudad = '$DATA[ciudad]',
		  provincia = '$DATA[provincia]',
		  nacimiento = '$DATA[nacimiento]',
		  correo_personal = '$DATA[correo_personal]',
		  correo_trabajo = '$DATA[correo_trabajo]',
		  estado_civil = '$DATA[estado_civil]'
		  WHERE ci = '$DATA[ci]'";
	$result=$this->_DB->genQuery($query);	      

	unset($DATA['id']) ;
	unset($DATA['save_edit']) ;
	$query = "INSERT INTO audit_actualizacion_clientes
		  (ci,usuario,data)
		  VALUES 
		  ('$DATA[ci]','$user','" . print_r($DATA,true) . "')";
	$result=$this->_DB->genQuery($query);	      
    }
    
    function actualizarDatosClienteRecargable($DATA, $user)
    {
        if(empty($DATA["id_cliente"])){
            $query = "SELECT id_cliente FROM campania_recargable_cliente WHERE id=".$_SESSION['id_campania_cliente_recargable'];
            $result=$this->_DB->getFirstRowQuery($query, true);
            $id_cliente=$result["id_cliente"];
        }else{
            $id_cliente=$DATA['id_cliente'];
        }
        
	$query = "UPDATE cliente_gestion 
		  SET 
		  nombre = '$DATA[nombre]',
		  apellido = '$DATA[apellido]',
		  ciudad = '$DATA[ciudad]',
		  provincia = '$DATA[provincia]',
		  nacimiento = '$DATA[nacimiento]',
		  correo_personal = '$DATA[correo_personal]',
		  correo_trabajo = '$DATA[correo_trabajo]',
		  estado_civil = '$DATA[estado_civil]'
		  WHERE id = ".$id_cliente;
	$result=$this->_DB->genQuery($query);	      

	unset($DATA['id']) ;
	unset($DATA['save_edit']) ;
	$query = "INSERT INTO audit_actualizacion_clientes
		  (ci,usuario,data)
		  VALUES 
		  ('$DATA[ci]','$user','" . print_r($DATA,true) . "')";
	$result=$this->_DB->genQuery($query);	
        return true;
    }

    function guardarDatosCliente($DATA, $user)
    {
	$query = "INSERT INTO cliente
		  (ci,nombre, apellido, provincia, ciudad, nacimiento,
		  correo_personal, correo_trabajo, estado_civil, origen, id_base) 
		  VALUES ('$DATA[ci_input]',
			  '$DATA[nombre]',
			  '$DATA[apellido]',
			  '$DATA[provincia]',
			  '$DATA[ciudad]',
			  '$DATA[nacimiento]',
			  '$DATA[correo_personal]',
			  '$DATA[correo_trabajo]',
			  '$DATA[estado_civil]',
			  '$DATA[origen]',99)";
	$result=$this->_DB->genQuery($query);
	unset($DATA['save_new']);
    
	if(!$result) return false;    

	$query = "INSERT INTO base_cliente
		  (id_base,ci,prioridad) 
		  VALUES (99,'$DATA[ci_input]',1)";
	$result=$this->_DB->genQuery($query);
	
	if($result)	return true;
	else	return false;


    }
    
}
?>