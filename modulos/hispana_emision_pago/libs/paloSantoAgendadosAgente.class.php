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
class paloSantoAgendadosAgente{
    var $_DB;
    var $errMsg;

    function paloSantoAgendadosAgente(&$pDB)
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

    function getNumReporteAgendados($filter_field, $filter_value, $user,$aditional_key,$aditional_value)
    {
        //$where    = " WHERE x.agente_agendado = '$user' ";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
	    $where .= " and $filter_field like '$filter_value%'";            
        }
        if(isset($aditional_value) & $aditional_value !=""){
	    $where .= " AND a.ci in (select ca.ci from cliente_adicional ca where ca.ci=a.ci and ca.descripcion='$aditional_key' and ca.adicional like '$aditional_value%')";            
        }

        $query   = "SELECT COUNT(*) 
		    FROM (  select distinct
        `b`.`id` AS `id_campania_cliente`
    from
        (((((`cliente` `a`
        join `campania_cliente` `b`)
        join `gestion_campania` `c`)
        join `calltype_campania` `d`)
        join `calltype` `f`)
        join `campania` `e`)
    where
       ((`a`.`ci` = `b`.`ci`)
            and (`b`.`id_gestion_mejor_calltype` = `c`.`id`)
            and (`c`.`calltype` = `d`.`id_calltype`)
            and (`e`.`id` in (`b`.`id_campania_consolidada`,`b`.`id_campania`))
	    and (`e`.`id` = `d`.`id_campania`)
            and (`d`.`id_calltype` = `f`.`id`)
            and (`f`.`id` = 20)
            and (`e`.`tipo` <> _utf8'RECARGABLE')
            and (`c`.`agente` ='$user')
            $where) 
    union select distinct
        `b`.`id` AS `id_campania_recargable_cliente`
    from
        (((((`cliente_gestion` `a`
        join `campania_recargable_cliente` `b`)
        join `gestion_campania` `c`)
        join `calltype_campania` `d`)
        join `calltype` `f`)
        join `campania` `e`)
   where
        ((`a`.`id` = `b`.`id_cliente`)
            and (`b`.`id_gestion_mejor_calltype` = `c`.`id`)
            and (`c`.`calltype` = `d`.`id_calltype`)
            and (`b`.`id_campania` = `e`.`id`)
            and (`d`.`id_calltype` = `f`.`id`)
            and (`f`.`id` = 20)
            and (`e`.`tipo` = _utf8'RECARGABLE')
            and (`c`.`agente` ='$user')
                $where)) x
		    ";
        $result=$this->_DB->getFirstRowQuery($query, false); // , $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getReporteAgendados($limit, $offset, $filter_field, $filter_value, $user,$aditional_key,$aditional_value)
    {
        //$where    = " WHERE x.agente_agendado = '$user' ";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where .= " AND  $filter_field like '$filter_value%'";            
        }
         if(isset($aditional_value) & $aditional_value !=""){
	    $where .= " AND a.ci in (select ca.ci from cliente_adicional ca where ca.ci=a.ci and ca.descripcion='$aditional_key' and ca.adicional like '$aditional_value%')";            
        }

        $query   = "SELECT *
		    FROM (  select distinct
        `e`.`id` AS `id_campania`,
        `b`.`id_campania_consolidada` AS `id_campania_consolidada`,
        `c`.`fecha` AS `fecha`,
        concat(`a`.`nombre`, _latin1' ', `a`.`apellido`) AS `cliente`,
        `b`.`id` AS `id_campania_cliente`,
        `a`.`ci` AS `ci`,
        `e`.`nombre` AS `campania`,
        `c`.`agente` AS `agente`,
        `c`.`telefono` AS `telefono`,
        `f`.`clase` AS `contactabilidad`,
        `f`.`descripcion` AS `mejor_calltype`,
        `b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,
        `c`.`observacion` AS `observacion`,
        `b`.`fecha_agendamiento` AS `fecha_agendamiento`,
        `b`.`agente_agendado` AS `agente_agendado`,
        `a`.`origen` AS `origen`,
        `f`.`peso` AS `peso`,
        _utf8'' AS `id_campania_recargable_cliente`
    from
        (((((`cliente` `a`
        join `campania_cliente` `b`)
        join `gestion_campania` `c`)
        join `calltype_campania` `d`)
        join `calltype` `f`)
        join `campania` `e`)
    where
        ((`a`.`ci` = `b`.`ci`)
            and (`b`.`id_gestion_mejor_calltype` = `c`.`id`)
            and (`c`.`calltype` = `d`.`id_calltype`)
            and (`e`.`id` in (`b`.`id_campania_consolidada`,`b`.`id_campania`))
	    and (`e`.`id` = `d`.`id_campania`)
            and (`d`.`id_calltype` = `f`.`id`)
            and (`f`.`id` = 20)
            and (`e`.`tipo` <> _utf8'RECARGABLE')
            and (`c`.`agente` ='$user')
            $where) 
    union select distinct
        `e`.`id` AS `id_campania`,
        `b`.`id_campania` AS `id_campania_consolidada`,
        `c`.`fecha` AS `fecha`,
        concat(`a`.`nombre`, _utf8' ', `a`.`apellido`) AS `cliente`,
        _utf8'' AS `id_campania_cliente`,
        `a`.`ci` AS `ci`,
        `e`.`nombre` AS `campania`,
        `c`.`agente` AS `agente`,
        `c`.`telefono` AS `telefono`,
        `f`.`clase` AS `contactabilidad`,
        `f`.`descripcion` AS `mejor_calltype`,
        `b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,
        `c`.`observacion` AS `observacion`,
        `b`.`fecha_agendamiento` AS `fecha_agendamiento`,
        `b`.`agente_agendado` AS `agente_agendado`,
        `a`.`origen` AS `origen`,
        `f`.`peso` AS `peso`,
        `b`.`id` AS `id_campania_recargable_cliente`
    from
        (((((`cliente_gestion` `a`
        join `campania_recargable_cliente` `b`)
        join `gestion_campania` `c`)
        join `calltype_campania` `d`)
        join `calltype` `f`)
        join `campania` `e`)
    where
        ((`a`.`id` = `b`.`id_cliente`)
            and (`b`.`id_gestion_mejor_calltype` = `c`.`id`)
            and (`c`.`calltype` = `d`.`id_calltype`)
            and (`b`.`id_campania` = `e`.`id`)
            and (`d`.`id_calltype` = `f`.`id`)
            and (`f`.`id` = 20)
            and (`e`.`tipo` = _utf8'RECARGABLE')
            and (`c`.`agente` ='$user')
                $where)) x
		     LIMIT $limit OFFSET $offset";	
        $result=$this->_DB->fetchTable($query, true); // , $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }
        return $result;
    }
    
    function getColumnasAdicionales()
    {
	$query = "select distinct UCASE(descripcion) as descripcion from cliente_adicional
union
select distinct UCASE(tipo) from cliente_gestion_adicionales where tipo not in ('telefono','direccion')
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
    function getDatosAdicionalesRecargable($id_campania_cliente)
    {
	// $this->_log->output("Inicio datos adicionales: " . $ci);
        
	/*$query = "select UCASE(cga.descripcion) as descripcion ,cga.adicional from cliente_gestion_adicionales as cga, cliente_gestion cg
                    where cga.id_cliente=cg.id
                    and cg.id=$id_cliente";*/
        $query="select UCASE(cga.descripcion) as descripcion,cga.adicional from cliente_gestion_adicionales cga, campania_recargable_cliente crc
                where crc.id_cliente=cga.id_cliente and crc.id=$id_campania_cliente";
        
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
    function getDatosAdicionales($ci)
    {
	// $this->_log->output("Inicio datos adicionales: " . $ci);
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
}
?>