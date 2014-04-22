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
  $Id: paloSantoListadodebases.class.php,v 1.1 2012-03-21 06:03:22 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoClientesManuales{
    var $_DB;
    var $errMsg;

    function paloSantoClientesManuales(&$pDB)
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
    function getNumListadodebases($filter_field, $filter_value)
    {
	$where    = "";
        $arrParam = null;
        if(isset($filter_value) & $filter_value !=""){
            $where    = " AND $filter_field like '$filter_value%'";
            $arrParam = array();
        }

        $query   = "SELECT COUNT(*) FROM cliente  WHERE origen != 'base' $where";

        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getListadodebases($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_value) & $filter_value !=""){
            $where    = " AND $filter_field like '$filter_value%'";
        }

        $query   = "SELECT * FROM cliente WHERE origen != 'base' $where LIMIT $limit OFFSET $offset";

        $result=$this->_DB->fetchTable($query, true, null);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getListadodebasesById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getCampaniasActivas()
    {
	$query = "SELECT id, nombre 
		  FROM campania
		  WHERE 
		  status = 'A' AND 
		  (tipo = 'ORIGINAL' or tipo='RECARGABLE')";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

	foreach($result as $campania){
	    $arrCampanias[$campania['id']] = $campania['nombre'];
	}
        return $arrCampanias;
    }

    function getAgentesCampania($id_campania)
    {
	$query = "SELECT id_agente as id 
		  FROM campania_agente 
		  WHERE 
		  id_campania = $id_campania 
		  AND status  = 'A'";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

	foreach($result as $agente){
	    $arrAgentes[$agente['id']] = $agente['id'];
	}
        return $arrAgentes;
    }

    function guardarAgendarCampaniaCliente($DATA)
    {
	$query = "INSERT INTO campania_cliente
		  (id_campania,
		   ci,
		   prioridad,
		   fecha_agendamiento,
		   agente_agendado,
		   fecha_status)
		  VALUES ('$DATA[campania]',
			  '$DATA[ci]',
			  '1',
			  '" . $DATA['fecha'] . " " . $DATA['horas'] . ":" . $DATA['minutos'] . ":00',
			  '$DATA[agente]','1970-01-01')";
	$result = $this->_DB->genQuery($query);
	if($result==1)
	    return true;
	else
	    return false;    
    }
    
    function guardarAgendarCampaniaClienteRecargable($DATA)
    {
        $query = "insert cliente_gestion(ci,nombre,apellido,provincia,ciudad,nacimiento,correo_personal,correo_trabajo,estado_civil,id_base,origen)
 (select ci,nombre,apellido,provincia,ciudad,nacimiento,correo_personal,correo_trabajo,estado_civil,id_base,origen from cliente where ci='$DATA[ci]')";
	$result = $this->_DB->genQuery($query);
        
        $rs = $this->_DB->getFirstRowQuery("SELECT @@identity AS id");
        $id_cliente = trim($rs[0]);
        
        $query = "insert into cliente_gestion_adicionales(id_cliente,id_base,tipo,descripcion,adicional,status)(
select $id_cliente as id_cliente,ct.id_base,'telefono' as tipo,ct.descripcion,ct.telefono as adicional, ct.status 
from cliente_telefono ct
where ct.ci='$DATA[ci]')";
	$result = $this->_DB->genQuery($query);
        
        $query = "insert into cliente_gestion_adicionales(id_cliente,id_base,tipo,descripcion,adicional,status)(
select $id_cliente as id_cliente,ct.id_base,'direccion' as tipo,ct.descripcion,ct.direccion as adicional, ct.status 
from cliente_direccion ct
where ct.ci='$DATA[ci]')";
	$result = $this->_DB->genQuery($query);
        
        $query = "insert into cliente_gestion_adicionales(id_cliente,id_base,tipo,descripcion,adicional,status)(
select $id_cliente as id_cliente,ct.id_base,ct.descripcion as tipo,ct.descripcion,ct.adicional as adicional, ct.status 
from cliente_adicional ct
where ct.ci='$DATA[ci]')";
	$result = $this->_DB->genQuery($query);
        
	$query = "INSERT INTO campania_recargable_cliente
		  (id_campania,
		   id_cliente,
		   prioridad,
                   id_base_cliente,
		   fecha_agendamiento,
		   agente_agendado,
		   fecha_status)
		  VALUES ('$DATA[campania]',
			  '$id_cliente',
			  '1',
                          99,
			  '" . $DATA['fecha'] . " " . $DATA['horas'] . ":" . $DATA['minutos'] . ":00',
			  '$DATA[agente]','1970-01-01')";
	$result = $this->_DB->genQuery($query);
	if($result==1)
	    return true;
	else
	    return false;    
    }

    function getAgendamientosCliente($ci)
    {
	$query = "SELECT 
		  b.nombre as 'campania',
		  a.agente_agendado as 'agente',
		  date(a.fecha_agendamiento) as 'fecha',
		  time(a.fecha_agendamiento) as 'hora'
		  FROM
		  campania_cliente as a,
		  campania as b
		  WHERE
		  b.id = a.id_campania AND
		  a.ci = '$ci' AND
		  a.fecha_agendamiento IS NOT NULL
		  ORDER BY a.fecha_agendamiento";
	$result = $this->_DB->fetchTable($query, true);

	if($result==FALSE) return null;
	else return $result;
    }

    function getNombreApellido($ci)
    {
	$query = "SELECT concat(nombre,' ',apellido) as 'cliente'
		  FROM cliente 
		  WHERE ci = '$ci'";
        $result=$this->_DB->getFirstRowQuery($query, true);
	return $result['cliente'];
    }
    
    function getTipoCampania($id_campania)
    {
	$query = "SELECT tipo 
		  FROM campania 
		  WHERE id = $id_campania";
        $result=$this->_DB->getFirstRowQuery($query, true);
	return $result['tipo'];
    }
}
?>