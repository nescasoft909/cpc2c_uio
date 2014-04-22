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
    function getNumReportedeCalltypes($filter_field, $filter_value, $id_campania,$filter_field_adicional=null,$filter_value_adicional=null)
    {
	// En caso se haga uso del filtro.
	$where = '';
	if(isset($filter_field) && isset($filter_value) && $filter_value!=""){
	    $where = " AND $filter_field LIKE '$filter_value%' ";
	}
        $campania='';
        //if(!empty($id_campania)){
            $campania="and c.id=$id_campania ";
        //}
        $where_adicional="";
        if(isset($filter_value_adicional) && isset($filter_value_adicional) && $filter_value_adicional!=""){
            $where_adicional=" AND cga.descripcion='$filter_field_adicional' AND cga.adicional like '$filter_value_adicional%'";
	}

	// Tomo todas las cédulas sin repetir para conocer cuales han sido gestionados.
	/**Hacer esta consulta en campania_cliente demora 0.02 segundos */
        $query="select distinct crc.id as id
                from gestion_campania as gc 
join campania_recargable_cliente as crc on (crc.id_gestion_mejor_calltype=gc.id) 
join campania as c on (c.id=crc.id_campania)
join calltype as ct on (ct.id=gc.calltype)
join calltype_campania as cc on (cc.id_calltype=ct.id)
join base b on (b.id=crc.id_base_cliente)
join cliente_gestion cg on (crc.id_cliente=cg.id)
join campania_recarga cre on (cre.id_campania=c.id)
left join cliente_gestion_adicionales cga on (cga.id_cliente=crc.id_cliente)
                where crc.id_gestion_mejor_calltype=gc.id
                and crc.id_campania in (c.id,c.campania_origen)
                and cc.id_campania=c.id
                and cc.id_calltype=ct.id
                and gc.calltype=ct.id
                and (b.id=crc.id_base_cliente or crc.id_base_cliente=99)
                and crc.id_base_cliente=cga.id_base
                and cre.id_base=crc.id_base_cliente
                $campania
                $where
                $where_adicional";
	/*$query   = "select distinct cg.id
                    from gestion_campania as gc, 
                         campania_recargable_cliente as crc, 
                             campania as c,
                         calltype as ct, 
                             calltype_campania as cc,
                             campania_recarga as cr,
                             cliente_gestion as cg,
                             base b
                    where crc.id=gc.id_campania_recargable_cliente
                    and c.id=crc.id_campania
                    and cc.id_campania=c.id
                    and cc.id_calltype=ct.id
                    and gc.calltype=ct.id
                    and cr.id_campania=c.id
                    and cr.id_base=crc.id_base_cliente
                    and cg.id=crc.id_cliente
                    and b.id=cr.id_base 
                    $campania
                    $where";
*/
	// $this->_log->output($query);
        $result=$this->_DB->fetchTable($query, false);
	
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return sizeof($result);
    }

    function getReportedeCalltypes($limit, $offset, $filter_field, $filter_value, $id_campania,$filter_field_adicional=null,$filter_value_adicional=null)
    {
	// En caso se haga uso del filtro.
	$where = '';
        
	if(isset($filter_field) && isset($filter_value) && $filter_value!=""){
	    $where = " AND $filter_field LIKE '$filter_value%'";
	}
        $campania='';
        //if(!empty($id_campania)){
            $campania="and c.id=$id_campania";
        //}
        $where_adicional="";
        if(isset($filter_value_adicional) && isset($filter_value_adicional) && $filter_value_adicional!=""){
            //$where_adicional="AND cga.descripcion='$filter_field_adicional' AND cga.adicional like '$filter_value_adicional%'";
            $where_adicional=" AND cga.descripcion='$filter_field_adicional' AND cga.adicional like '$filter_value_adicional%'";
	}
            

	// Primero obtengo todas las cédula de clientes gestionados (con limit y offset)
	/**Hacer esta consulta en campania_cliente demora 0.02 segundos */
        $query="select distinct crc.id as id
                from gestion_campania as gc 
join campania_recargable_cliente as crc on (crc.id_gestion_mejor_calltype=gc.id) 
join campania as c on (c.id=crc.id_campania)
join calltype as ct on (ct.id=gc.calltype)
join calltype_campania as cc on (cc.id_calltype=ct.id)
join base b on (b.id=crc.id_base_cliente)
join cliente_gestion cg on (crc.id_cliente=cg.id)
join campania_recarga cre on (cre.id_campania=c.id)
left join cliente_gestion_adicionales cga on (cga.id_cliente=crc.id_cliente)
                where crc.id=gc.id_campania_recargable_cliente
                and crc.id_campania in (c.id,c.campania_origen)
                and cc.id_campania=c.id
                and cc.id_calltype=ct.id
                and gc.calltype=ct.id
                and b.id=crc.id_base_cliente
                and (cre.id_base=crc.id_base_cliente or crc.id_base_cliente=99)
                and crc.id_base_cliente=cga.id_base
                $campania
                $where
                $where_adicional
                      order by gc.fecha desc
		    LIMIT $limit
		    OFFSET $offset";
        
	/*$query   = "select distinct cg.id
                      from gestion_campania as gc, 
                           campania_recargable_cliente as crc, 
                               campania as c,
                           calltype as ct, 
                               calltype_campania as cc,
                               campania_recarga as cr,
                               cliente_gestion as cg,
                               base b
                      where crc.id=gc.id_campania_recargable_cliente
                      and c.id=crc.id_campania
                      and cc.id_campania=c.id
                      and cc.id_calltype=ct.id
                      and gc.calltype=ct.id
                      and cr.id_campania=c.id
                      and cr.id_base=crc.id_base_cliente
                      and cg.id=crc.id_cliente
                      and b.id=cr.id_base 
                      $campania
                      $where
                      order by gc.fecha desc
		    LIMIT $limit
		    OFFSET $offset";*/

        $result_ci = $this->_DB->fetchTable($query, true);
	// $this->_log->output("Clientes retornados: " . sizeof($result_ci));
	foreach($result_ci as $arrCi){
	    $query = "select c.nombre as campania,b.nombre as base,b.id as id_base,ct.descripcion as calltype, ct.clase as contactabilidad,
                            (select x.descripcion from gestion_campania as y,calltype as x where y.id=crc.id_gestion_mejor_calltype and x.id=y.calltype) as mejor_calltype,
                            gc.fecha as fecha,cg.id as id_cliente,cg.ci,cg.nombre,cg.apellido,cg.provincia,cg.ciudad,
                            cg.nacimiento,cg.correo_personal,cg.correo_trabajo,cg.estado_civil,gc.agente as agente,gc.telefono,
                            concat(cg.nombre,' ',cg.apellido) as ' cliente',gc.id as id_gestion,cc.peso,gc.calltype as id_caltype,cg.provincia,cg.ciudad,cg.nacimiento,cg.correo_personal,cg.correo_trabajo,cg.estado_civil
                      from gestion_campania as gc, 
                           campania_recargable_cliente as crc, 
						   campania as c,
                           calltype as ct, 
                           calltype_campania as cc,
                           cliente_gestion as cg,
                           base b,
                           campania_recarga cre
                      where crc.id_gestion_mejor_calltype=gc.id
                      and c.id=crc.id_campania
                      and cc.id_campania=c.id
                      and cc.id_calltype=ct.id
                      and gc.calltype=ct.id
                      and cg.id_base=crc.id_base_cliente
                      and cg.id=crc.id_cliente
                      and b.id=cg.id_base 
                      and (cre.id_base=crc.id_base_cliente or crc.id_base_cliente=99)
                      and cre.id_campania=c.id
                      and crc.id=$arrCi[id]
                      $campania
                      $where
                      order by cc.peso desc,gc.fecha desc";
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

	$result=$this->_DB->fetchTable($query, true, $arrParam);
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
		  WHERE tipo = 'RECARGABLE' AND 
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
    cliente_gestion_adicionales cga,
    cliente_gestion cg,
    campania_recarga cr
where
    cga.id_cliente = cg.id
        and cr.id_base = cga.id_base
        and cr.id_campania = $id_campania";

	// Este query representa una bomba de tiempo.

	$result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $row){
	    $arrColumnas[] = $row['descripcion'];
	}	
	return $arrColumnas;	
	return array();
    }

    function getDatosAdicionales($id_cliente,$id_base)
    {
	// $this->_log->output("Inicio datos adicionales: " . $ci);
        
	$query = "select UCASE(cga.descripcion) as descripcion ,cga.adicional from cliente_gestion_adicionales as cga, cliente_gestion cg
                    where cga.id_cliente=cg.id
                    and cg.id=$id_cliente
                    and cga.id_base=$id_base";

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
		  FROM campania_recargable_cliente
		  WHERE ultimo_calltype IS NOT null";
	$result=$this->_DB->fetchTable($query, true, $arrParam);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }

	foreach($result as $k => $arrCampaniaCliente){
	    $query = "SELECT a.id as id_gestion, a.id_campania_cliente, a.calltype, b.peso, a.fecha 
		      FROM gestion_campania AS a, calltype_campania AS b, calltype AS c 
		      WHERE 
		      a.calltype = b.id_calltype AND 
		      b.id_calltype = c.id AND
		      a.id_campania_cliente = $arrCampaniaCliente[id]
		      ORDER BY b.peso DESC LIMIT 1";

	    $resultCallType = $this->_DB->getFirstRowQuery($query,true);

	    if($resultCallType==FALSE){
		$this->errMsg = $this->_DB->errMsg;
		return false;
	    }	    
	    $update = "UPDATE campania_recargable_cliente 
		       SET id_gestion_mejor_calltype = $resultCallType[id_gestion] 
		       WHERE id = $resultCallType[id_campania_cliente]";
	    $this->_DB->genQuery($update);
	}
    }

}
?>