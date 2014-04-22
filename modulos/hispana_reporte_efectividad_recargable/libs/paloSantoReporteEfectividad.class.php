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
  $Id: paloSantoReporteEfectividad.class.php,v 1.1 2012-08-07 08:08:16 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoReporteEfectividad{
    var $_DB;
    var $errMsg;

    function paloSantoReporteEfectividad(&$pDB)
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

    function getNumReporteEfectividad($filter_field, $filter_value)
    {
        $where    = "where tipo='RECARGABLE' and campania_origen is null ";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "and $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT COUNT(*) FROM campania $where";

        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getReporteEfectividad($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "where tipo='RECARGABLE' and campania_origen is null ";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    .= " AND $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT * FROM campania $where LIMIT $limit OFFSET $offset";

        $result=$this->_DB->fetchTable($query, true, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }

    function getReporteEfectividadById($id)
    {
        $query = "SELECT * FROM campania WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getNombreCampania($id)
    {
	$query = "SELECT nombre FROM campania WHERE id=?";
        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result['nombre'];
    }

    function getBasesCampania($id)
    {
	$query = "SELECT b.id, b.nombre 
		  FROM campania_recarga as a, base as b 
		  WHERE 
		  id_campania=$id AND 
		  a.id_base=b.id 
		  ORDER BY b.nombre";
        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

	foreach($result as $campania){
	    $response[$campania['id']] = $campania['nombre'];
	}

        return $response;
    }

    function getRegistrosCargados($idCampania, $idBase)
    {
	$query_base="";
	if(isset($idBase) && $idBase!=0){
	    $query_base = " AND b.id_base=$idBase";
	}

	$query = "SELECT distinct a.id_cliente
		  FROM campania_recargable_cliente as a, campania_recarga as b
		  WHERE a.id_campania = b.id_campania 
                  AND a.id_base_cliente=b.id_base
		  AND a.id_campania = $idCampania " . $query_base . "
		  group by a.id_cliente, a.id_campania";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

        return sizeof($result); // retorno tamaño del array $result
    }

    function getRegistrosBarridos($idCampania,$idBase)
    {
	$query_base="";
	if(isset($idBase) && $idBase!=0){
	    $query_base = " AND b.id_base=$idBase";
	}

	$query = "SELECT distinct a.id_cliente
		  FROM 
		  campania_recargable_cliente AS a, 
		  campania_recarga AS b 
		  WHERE 
		  a.id_campania = b.id_campania AND 
		  b.id_base = a.id_base_cliente AND  
		  a.id_campania = $idCampania " . 
		  $query_base . " AND
		  a.status = 'Gestion'";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return sizeof($result);
    }

    function getClaseContactados($idCampania,$idBase,$clase_calltype)
    {
	if(isset($idBase) && $idBase!=0){
	    $query_base = " AND b.id_base=$idBase";
	}
	// Se arregló por id_campania_consolidada
	$query = "SELECT distinct a.id_cliente
		  FROM 
		  campania_recargable_cliente AS a, 
		  campania_recarga AS b, 
		  gestion_campania AS d, 
		  calltype AS e 
		  WHERE a.id_campania = b.id_campania  
		  AND b.id_base = a.id_base_cliente    
		  AND a.id_campania = $idCampania  
		  AND a.status = 'Gestion'  
		  AND a.id_gestion_mejor_calltype = d.id " . $query_base . " 
		  AND d.calltype = e.id
		  AND e.clase = 'Contactado'";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return sizeof($result);    
    }

    function getConversion($idCampania, $idBase)
    {
	if(isset($idBase) && $idBase!=0){
	    $query_base = " AND b.id_base=$idBase";
	}

	/**
	  Con este query se obtiene el mejor calltype (con su peso) 
	  que se haya escogido durante la gestión de la campania_consolidada.
	*/
	$query = "SELECT f.id, concat(f.clase,' - ',f.descripcion) as calltype, g.peso
		  FROM campania_recargable_cliente AS a, 
		  campania_recarga AS b, 
		  gestion_campania AS d, 
		  calltype AS f,
                  calltype_campania as g
		  WHERE a.id_campania = b.id_campania
                  AND a.id_base_cliente=b.id_base
		  AND a.id_campania = $idCampania 
		  AND a.status = 'Gestion'   
		  AND a.id_gestion_mejor_calltype = d.id " . $query_base . "
		  AND f.id = d.calltype
		  AND f.clase = 'Contactado' 
                  AND g.id_campania = a.id_campania
                  AND g.id_calltype = f.id
		  GROUP BY g.peso
		  ORDER BY g.peso 
		  DESC limit 1";

	

	$result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

	$query = "SELECT distinct a.id_cliente
		  FROM campania_recargable_cliente AS a, 
		  campania_recarga AS b, 
		  gestion_campania AS d, 
		  calltype AS f 
		  WHERE a.id_campania = b.id_campania
		  AND b.id_base = a.id_base_cliente
		  AND a.id_campania = $idCampania 
		  AND a.status = 'Gestion'   
		  AND a.id_gestion_mejor_calltype = d.id " . $query_base . "
		  AND f.id = d.calltype
		  AND f.clase = 'Contactado'
		  AND f.id=$result[id]";

        $result2 = $this->_DB->fetchTable($query, true);
	$result['cont'] = sizeof($result2);
	return $result;
    }


}
?>