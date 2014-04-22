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
  $Id: paloSantoReportedeCalltypes.class.php,v 1.1 2012-07-05 10:07:39 Juan Pablo Romero jromero@palosanto.com Exp $ */
// include_once("/opt/elastix/dialer/AppLogger.class.php");

class paloSantoReportedeCalltypes{
    var $_DB;
    var $errMsg;
    var $_log;

    function paloSantoReportedeCalltypes(&$pDB)
    {
        // Se recibe como parámetro una referencia a una conexión paloDB
        if (is_object($pDB)) {
            $this->_DB =& $pDB;
            $this->errMsg = $this->_DB->errMsg;
	    // $this->_log = new AppLogger();
	    // $this->_log->open("/var/www/html/modules/hispana_reporte_calltype/log.log");
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
    function getNumReportedeCalltypes($filter_field, $filter_value, $id_campania)
    {
	// En caso se haga uso del filtro.
	$where = '';
	if(isset($filter_field) && isset($filter_value) && $filter_value!=""){
	    $where = " AND $filter_field LIKE '$filter_value%'";
	}

	// Tomo todas las cédulas sin repetir para conocer cuales han sido gestionados.
	/**Hacer esta consulta en campania_cliente demora 0.02 segundos */
	/*$query   = "SELECT distinct ci
		    FROM campania_cliente
		    WHERE 
		    id_campania_consolidada = $id_campania		    		    
		    ORDER BY id_gestion_mejor_calltype DESC
		    $where";*/
        $query = "SELECT  
		      distinct a.ci
		      FROM 
		      campania_cliente AS a, 
		      gestion_campania AS b, 
		      calltype AS c,
		      campania AS d,
		      cliente AS e,
                      base as f,
                      campania_base g
		      WHERE 
		      a.id_campania_consolidada = $id_campania AND 
		      a.ultimo_calltype = b.id AND 
		      b.calltype = c.id AND
		      a.id_campania_consolidada = d.id AND
		      a.ci=e.ci AND
                      f.id=e.id_base
                      $where";

	// $this->_log->output($query);
        $result=$this->_DB->fetchTable($query, false);
	
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return sizeof($result);
    }

    function getReportedeCalltypes($limit, $offset, $filter_field, $filter_value, $id_campania)
    {
	// En caso se haga uso del filtro.
	$where = '';
	if(isset($filter_field) && isset($filter_value) && $filter_value!=""){
	    $where = " AND $filter_field LIKE '$filter_value%'";
	}

	// Primero obtengo todas las cédula de clientes gestionados (con limit y offset)
	/**Hacer esta consulta en campania_cliente demora 0.02 segundos */
	/*$query   = "SELECT distinct ci
		    FROM campania_cliente
		    WHERE 
		    id_campania_consolidada = $id_campania	      
		    $where		    
		    ORDER BY id_gestion_mejor_calltype DESC
		    LIMIT $limit
		    OFFSET $offset";	*/	
        $query = "SELECT  
		      distinct a.ci
		      FROM 
		      campania_cliente AS a, 
		      gestion_campania AS b, 
		      calltype AS c,
		      campania AS d,
		      cliente AS e,
                      base as f,
                      campania_base g
		      WHERE 
		      a.id_campania_consolidada = $id_campania AND 
		      a.ultimo_calltype = b.id AND 
		      b.calltype = c.id AND
		      a.id_campania_consolidada = d.id AND
		      a.ci=e.ci AND
                      f.id=e.id_base
                      $where
		      ORDER BY a.id_gestion_mejor_calltype desc
                      LIMIT $limit
		      OFFSET $offset";
        $result_ci = $this->_DB->fetchTable($query, true);
	// $this->_log->output("Clientes retornados: " . sizeof($result_ci));
	foreach($result_ci as $arrCi){
	    $query = "SELECT  
		      d.nombre as 'campania',
		      a.ci, 
		      a.id as 'id_campania_cliente', 
		      max(b.id) as 'ultimo_calltype',
		      b.calltype, 
		      c.descripcion as 'mejor_calltype', 
		      c.clase as 'contactabilidad',
		      b.fecha,
		      b.telefono,
		      b.agente,
		      concat(e.nombre,' ',e.apellido) as ' cliente',
                      f.nombre as 'base',
                      f.id as id_base
		      FROM 
		      campania_cliente AS a, 
		      gestion_campania AS b, 
		      calltype AS c,
		      campania AS d,
		      cliente AS e,
                      base as f
		      WHERE 
		      a.id_campania_consolidada = $id_campania AND 
		      a.ci = '$arrCi[ci]'AND 
		      a.id = b.id_campania_cliente AND 
		      b.calltype = c.id AND
		      a.id_campania_consolidada = d.id AND
		      a.ci=e.ci AND
                      f.id=e.id_base
                      group by d.nombre,a.ci,a.id,b.calltype,c.descripcion,c.clase,b.fecha,b.telefono,b.agente,f.id
		      ORDER BY c.peso desc, b.fecha desc";
	    $result = $this->_DB->getFirstRowQuery($query, true);   
	    // $this->_log->output(date("Y-m-d") . " " .time());
	    $arrResult[] = $result;
	}
/*
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
*/
        return $arrResult;	
    }    

    function getDatosGestion($id_gestion)
    {
	$query = "SELECT b.etiqueta,a.valor 
		  FROM gestion_campania_detalle AS a, form_field AS b
		  WHERE a.id_gestion_campania = $id_gestion
		  AND a.id_form_field = b.id";

	/*$query = "select c.id as campania, gc.calltype,ct.descripcion,ff.etiqueta,ff.value,ff.tipo,gcd.valor
                    from gestion_campania gc, campania_recargable_cliente crc,
                         campania c, form_field ff,gestion_campania_detalle gcd,calltype ct
                    where gc.id_campania_recargable_cliente=crc.id
                      and crc.id_campania=c.id
                      and c.id_form=ff.id_form
                      and gcd.id_gestion_campania=gc.id
                      and gcd.id_form_field=ff.id
                      and ct.id=gc.calltype
                      and gc.id=$id_gestion";*/
        
        $result=$this->_DB->fetchTable($query, true, $arrParam);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
	return $result;
    }
    
    function getDatosGestionRecargable($id_gestion)
    {
	/*$query = "SELECT b.etiqueta,a.valor 
		  FROM gestion_campania_detalle AS a, form_field AS b
		  WHERE a.id_gestion_campania = $id_gestion
		  AND a.id_form_field = b.id";*/

	$query = "select c.id as campania, gc.calltype,ct.descripcion,ff.etiqueta,ff.value,ff.tipo,gcd.valor
                    from gestion_campania gc, campania_recargable_cliente crc,
                         campania c, form_field ff,gestion_campania_detalle gcd,calltype ct
                    where gc.id_campania_recargable_cliente=crc.id
                      and crc.id_campania=c.id
                      and c.id_form=ff.id_form
                      and gcd.id_gestion_campania=gc.id
                      and gcd.id_form_field=ff.id
                      and ct.id=gc.calltype
                      and gc.id=$id_gestion";
        
        $result=$this->_DB->fetchTable($query, true, $arrParam);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
	return $result;
    }
    
    function getHistorialAuditGestion($id_gestion)
    {
	$query = "SELECT *
		  FROM audit_gestion
		  WHERE id_gestion=$id_gestion";

	$result=$this->_DB->fetchTable($query, true);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
	return $result;
    }
    
    function getCalltypesByCampania($id_campania)
    {
	$query = "select ct.id,ct.descripcion 
                  from calltype ct, campania c, calltype_campania cc
                  where cc.id_campania=c.id
                  and cc.id_calltype=ct.id
                  and c.id=$id_campania";

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

    function getCampaniasActivas()
    {
	$query = "SELECT id,nombre 
		  FROM campania 
		  WHERE tipo = 'ORIGINAL' AND 
		  status = 'A'";

	$result=$this->_DB->fetchTable($query, true, $arrParam);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $row){
	    $arrCampanias[$row['id']] = $row['nombre'];
	}
	return $arrCampanias;
    }
    
    function getColumnasAdicionales($id_campania)
    {
	$query = "select distinct
    ucase(cga.descripcion) as descripcion
from
    cliente_adicional cga,
    cliente cg,
    campania_base cr
where
    cga.ci = cg.ci
        and cr.id_base = cga.id_base
        and cr.id_campania = $id_campania
		      ";

	$result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $row){
	    $arrColumnas[] = $row['descripcion'];
	}	
	return $arrColumnas;	
	//return array();
    }

    function getDatosAdicionales($ci,$id_base)
    {
	// $this->_log->output("Inicio datos adicionales: " . $ci);
	/*$query = "select UCASE(cga.descripcion) as descripcion ,cga.adicional 
                    from cliente_adicional as cga
                    where cga.ci='$ci'
                    and cga.id_base=$id_base";*/
        $query = "SELECT UCASE(descripcion) AS descripcion, adicional 
		  FROM cliente_adicional 
		  WHERE ci = '$ci'";
	
	$result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $k => $adicional){
	    $arrDatosAdicionales[$adicional['descripcion']] = $adicional['adicional'];
	}
	// $this->_log->output("Fin datos adicionales: " . $ci);
	return $arrDatosAdicionales;
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

    function actualizarCampaniaCliente()
    {
	$query = "SELECT id 
		  FROM campania_cliente
		  WHERE ultimo_calltype IS NOT null";
	$result=$this->_DB->fetchTable($query, true, $arrParam);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $k => $arrCampaniaCliente){
	    $query = "SELECT a.id as id_gestion, a.id_campania_cliente, a.calltype, c.peso, a.fecha 
		      FROM gestion_campania AS a, calltype_campania AS b, calltype AS c 
		      WHERE 
		      a.calltype = b.id_calltype AND 
		      b.id_calltype = c.id AND
		      a.id_campania_cliente = $arrCampaniaCliente[id]
		      ORDER BY peso DESC LIMIT 1";

	    $resultCallType = $this->_DB->getFirstRowQuery($query,true);

	    if($resultCallType==FALSE){
		$this->errMsg = $this->_DB->errMsg;
		return false;
	    }	    
	    $update = "UPDATE campania_cliente 
		       SET id_gestion_mejor_calltype = $resultCallType[id_gestion] 
		       WHERE id = $resultCallType[id_campania_cliente]";
	    $this->_DB->genQuery($update);
	}
    }
    
    function actualizarGestionCliente($data,$id_gestion,$user)
    {
        $arraySave=array();
        foreach($data as $key=>$value){
            if($key!="guardar"&&$key!="id_gestion"&&$key!="elastix_user"&&$key!="calltype"){
                $query = "update gestion_campania_detalle AS a, form_field AS b set a.valor='$value'
                          WHERE a.id_gestion_campania = $data[id_gestion]
                          AND a.id_form_field = b.id
                          and b.etiqueta='$key'";
                $this->_DB->genQuery($query);
                $arraySave[$key]=$value;
            }elseif($key=="calltype"){
                $query = "update gestion_campania AS a set a.calltype='$value'
                          WHERE a.id = $data[id_gestion]";
                $this->_DB->genQuery($query);
                $arraySave[$key]=$value;
            }
        }
        $query = "INSERT INTO audit_gestion
		  (id_gestion,usuario,data)
		  VALUES 
		  ($id_gestion,'$user','" . print_r($arraySave,true) . "')";
          $this->_DB->genQuery($query);
        
        
	
    }
    
    function registraReporteOffline($id_campania,$tiempo_unix,$ruta,$filtro)
    {
        $query = "INSERT INTO reportes_offline
		  (id_campania,tiempo_unix,ruta,filtro,status)
		  VALUES 
		  ($id_campania,'$tiempo_unix','$ruta','$filtro','I')";
          $this->_DB->genQuery($query);	
    }
    
    function actualizaReporteOffline($id_campania,$tiempo_unix)
    {
        $query = "update reportes_offline set status='F' where
		  id_campania=$id_campania and tiempo_unix='$tiempo_unix'";
          $this->_DB->genQuery($query);	
    }

}
?>