<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Elastix version 2.2.0-25                                             |
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
  $Id: paloSantoDynamicAgents.class.php,v 1.1 2012-11-11 10:11:52 Juan Pablo Romero jromero@palosanto.com Exp $   */
require_once "/var/lib/asterisk/agi-bin/phpagi-asmanager.php";

class paloSantoCampaniaDerivada{
    var $_DB;
    var $errMsg;    

    function paloSantoCampaniaDerivada(&$pDB)
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
    /*
    function getNumDynamicAgents($filter_field, $filter_value)
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

    function getDynamicAgents($limit, $offset, $filter_field, $filter_value)
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

    function getDynamicAgentsById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getQueues()
    {
	$query = "SELECT extension, descr 
		  FROM queues_config
		  ORDER BY extension";
        $result = $this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }

	foreach($result as $reg){
	    $arrResult[$reg['extension']] = $reg['extension'].' - '.$reg['descr'];
	}
        return $arrResult;
    }

    function getStaticAgents($queue)
    {	global $_log;
	$query = "SELECT data
		  FROM queues_details
		  WHERE id = '$queue' AND
		  keyword = 'member'
		  ORDER BY data";
        $result = $this->_DB->fetchTable($query, true);	
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
	    $_log->output($this->errMsg);
            return array();
        }	
	$arrResult = array();
	foreach($result as $reg){
	    $arrAgente = explode(",",$reg['data']);
	    $arrResult[$arrAgente[0]] = $arrAgente[0];
	}
        return $arrResult;
    }

    function getInactiveAgents($queue)
    {	
	global $_log;
	$arrNotInactiveAgents = array_merge($this->getStaticAgents($queue),$this->getActiveDynamicAgents($queue));
 
	$query = "SELECT concat('Agent/',number) as agente
		  FROM call_center.agent
		  WHERE estatus='A'";

        $result = $this->_DB->fetchTable($query, true);	
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
	    $_log->output($this->errMsg);
            return array();
        }	

	foreach($result as $reg){	    
	    $arrResult[$reg['agente']] = $reg['agente'];
	}
	
	return array_diff($arrResult,$arrNotInactiveAgents);
    }

    function getActiveDynamicAgents($queue)
    {
	global $_log;
	// Ideas tomadas de la clase del call center: Predictivo.class.php	

	$astman = new AGI_AsteriskManager();
	if (!$astman->connect($this->asterisk_host, $this->asterisk_user, $this->asterisk_pass)) {	    
	    $_log->output("No se conecta a Asterisk");
	    return array();
	}
	// si todo ok, continúa

	$arrDynamicAgents = array();
	$respuestaQueueShow = $astman->Command("queue show $queue");
	$lineasRespuesta = explode("\n", $respuestaQueueShow['data']);

	foreach($lineasRespuesta as $sLinea){		
	    $sLinea = trim($sLinea);
	    $regs = NULL;
	    if(preg_match('|^Agent/(\d+)@?\s*(.*)$|', $sLinea, $regs)){
		$sCodigoAgente = "Agent/".$regs[1];		    
		$sInfoAgente = $regs[2]; 		    		    
		$regs = NULL;
		while (preg_match('/^\(([^)]+)\)\s+(.*)/', $sInfoAgente, $regs)) {
		    if($regs[1] == 'dynamic'){ // al menos un atributo lo denota como "dinámico"
			$arrDynamicAgents[$sCodigoAgente] = $sCodigoAgente;
		    }
		    $estadoCola['members'][$sCodigoAgente]['attributes'][] = $regs[1];
		    $sInfoAgente = $regs[2];                                
		    $regs = NULL;
		}
	    }		
	}
	return $arrDynamicAgents;  
    }
/*

    function processAssignment($DATA)    {
	
	$astman = new AGI_AsteriskManager();
	if (!$astman->connect($this->asterisk_host, $this->asterisk_user, $this->asterisk_pass)) {	    	    
	    return false;
	}

	$arrActivarAgentes = explode(",",$DATA['values_agentes']);

	foreach($arrActivarAgentes as $agente){	    
	    $r = $astman->Command("queue add member $agente to $DATA[queue]");
	}

	$arrDesactivarAgentes = explode(",",$DATA['values_inactive']);

	foreach($arrDesactivarAgentes as $agente){	    
	    $r = $astman->Command("queue remove member $agente from $DATA[queue]");
	}
	return true;
    }
*/

    function getCampanias()
    {
	$query = "SELECT id,nombre 
		  FROM campania 
		  WHERE status='A' 
		  AND tipo = 'ORIGINAL'";

        $result = $this->_DB->fetchTable($query, true);	

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }

	foreach($result as $reg){	    
	    $arrResult[$reg['id']] = $reg['nombre'];
	}

        return $arrResult;
    }


    function getBases($idCampania)
    {
	$query = "SELECT a.id, a.nombre
		  FROM base as a, campania_base as b
		  WHERE 
		  a.id = b.id_base AND
		  b.status = 'A' AND
		  b.id_campania = '$idCampania'";

        $result = $this->_DB->fetchTable($query, true);	

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }

	foreach($result as $reg){	    
	    $arrResult[$reg['id']] = $reg['nombre'];
	}

        return $arrResult;
    }

    function getCalltypes($idCampania)
    {
	$query = "SELECT a.id, concat(a.clase,' - ',a.descripcion) AS nombre
		  FROM calltype AS a, calltype_campania AS b 
		  WHERE a.status = 'A' AND 
		  a.id = b.id_calltype AND 
		  b.id_campania = '$idCampania'
		  ORDER BY clase,descripcion;";

        $result = $this->_DB->fetchTable($query, true);	

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return array();
        }

	foreach($result as $reg){	    
	    $arrResult[$reg['id']] = $reg['nombre'];
	}

        return $arrResult;
    }

    /**
      $listaBases y $listaCalltypes son elementos separados por coma (,)
    **/
    function obtenerClientes($idCampania, $listaCalltypes, $listaBases, $boolNum)
    {    
	$query = "SELECT distinct a.ci, c.prioridad 
		  FROM campania_cliente as a, gestion_campania as b, base_cliente as c 
		  WHERE 
		  a.id_campania=$idCampania AND 
		  a.id_gestion_mejor_calltype = b.id AND 
		  b.calltype in ($listaCalltypes) AND 
		  a.ci = c.ci AND 
		  c.id_base in ($listaBases)";

	$result = $this->_DB->fetchTable($query, true);

	if($result == FALSE)
	    return 0;

	if($boolNum){ // si es true, retorno la cantidad
	    return sizeof($result);	
	}else{ // si es false, retorno el array
	    return $result;
	}
    }


    function crearCampaniaDerivada($DATA)
    {

	/*
	$DATA
	Array
	(
	[save_new] => Guardar
	[nombre_campania] => Campaña_9.44am
	[campania] => 1
	[id] => 
	[values_bases] => 99,1
	[values_inactive_bases] => 
	[values_calltypes] => 5,228,236,1,3
	[values_inactive_calltypes] => 4,232,2,212,231,226,235,234
	)
	*/
	$query = "INSERT INTO campania
		  (nombre,fecha_inicio,fecha_fin,id_form,script,status,tipo,campania_origen) 
		  SELECT '$DATA[nombre_campania]', fecha_inicio,fecha_fin,id_form,script,status,'DERIVADA','$DATA[campania]' 
		  FROM campania 
		  WHERE id=$DATA[campania]";
	
	$result = $this->_DB->genQuery($query);
	$result = $this->_DB->getFirstRowQuery("SELECT LAST_INSERT_ID()"); // obtengo el último ID insertado

	if($result[0]>0){
	    $IdCampaniaNueva = $result[0];
      
	    $arrClientes = $this->obtenerClientes($DATA['campania'],$DATA['values_calltypes'],$DATA['values_bases'],false);

	    //Inserto Clientes
	    foreach($arrClientes as $regCliente){
		$query = "INSERT INTO campania_cliente (id_campania,id_campania_consolidada,ci,prioridad)
			  VALUES ($IdCampaniaNueva,$IdCampaniaNueva,'$regCliente[ci]',$regCliente[prioridad])";		
		$result = $this->_DB->genQuery($query);
	    }

	    //Replico los agentes de la campaña original
	    $query = "INSERT INTO campania_agente
		      SELECT $IdCampaniaNueva,id_agente,status 
		      FROM campania_agente
		      WHERE id_campania = $DATA[campania]";
	    $result = $this->_DB->genQuery($query);

	    //Replico los calltypes
	    $query = "INSERT INTO calltype_campania
		      SELECT id_calltype,$IdCampaniaNueva
		      FROM calltype_campania
		      WHERE id_campania = $DATA[campania]";
	    $result = $this->_DB->genQuery($query);

	    // Replico las bases
	    $query = "INSERT INTO campania_base
		      SELECT $IdCampaniaNueva,id_base,status 
		      FROM campania_base
		      WHERE id_campania = $DATA[campania]";
	    $result = $this->_DB->genQuery($query);

	    return true;
	}else{
	    return false;
	}
    }    
}
?>