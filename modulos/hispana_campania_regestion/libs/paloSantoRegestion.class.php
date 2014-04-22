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
  $Id: paloSantoRegestion.class.php,v 1.1 2012-07-30 03:07:39 Juan Pablo Romero jromero@palosanto.com Exp $ */

require_once "/var/www/html/modules/hispana_logger/AppLogger.class.php";

class paloSantoRegestion{
    var $_DB;
    var $errMsg;
    var $_log;

    function paloSantoRegestion(&$pDB)
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
	$this->_log  = new AppLogger();
	$this->_log->open("/var/www/html/modules/hispana_campania_regestion/log/hispana_campania_regestion.log");
    }

    /*HERE YOUR FUNCTIONS*/

    function getNumRegestion($filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "and $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query   = "SELECT COUNT(*) FROM campania where tipo in ('ORIGINAL','RECARGABLE') $where ";

        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getRegestion($limit, $offset, $filter_field, $filter_value)
    {
        $where    = "WHERE tipo IN ('ORIGINAL','DERIVADA','RECARGABLE')";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where .= " AND $filter_field like ?";
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

    function getRegestionById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    /** 
      *	 Grabar una regestion es como grabar una campaña pero filtra clientes
      *  y preguarda gestiones de estos clientes tomadas de campañas padre (esto último está mal).
      *  Lo único que debe guardar es id_campania_consolidada
      *
      */
    function saveNewRegestion($DATA)
    {
	if ($this->getTipoCampania($DATA['id'])=='RECARGABLE'){
            $this->_log->output("=== NUEVA REGESTIÓN ===");
            $query = "INSERT into campania (nombre,fecha_inicio,fecha_fin,id_form,script,status,tipo,campania_origen) 
                      VALUES ('".$DATA['nombre']."','"
                                .$DATA['fecha_inicio']."','"
                                .$DATA['fecha_fin']."',"
                                .$DATA['id_form'].",'"
                                .$DATA['script']."','A',"
                                ."'RECARGABLE','$DATA[id]')";  

            $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 1 \n" . $query);

            $result=$this->_DB->genQuery($query, true);	
            if($result==false)
                return "Error al intentar crear la campaña.<br>Verifique si una campaña ya existe con ese nombre.";

            // Retorna el maxID de la tabla campania.
            $idCampaign = $this->maxID("campania"); 
        
            //Para Campanias Recargables
            //Llena tabla campania_recarga
            $DATA['base'] = explode(",",$DATA['values_bases']);
            foreach($DATA['base'] as $idBase){
                $datosRecarga=$this->getRecarga($idBase,$DATA['id']);
                $query = "INSERT into campania_recarga (id_campania, id_base,fecha_inicio,fecha_fin, status) 
                          VALUES  (".$idCampaign.","
                                    .$idBase.",'$datosRecarga[fecha_inicio]','$datosRecarga[fecha_fin]','$datosRecarga[status]')";  
                $result = $this->_DB->genQuery($query, true);

                $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 2 \n" . $query);

                if($result==false){
                    $this->_log->output("Error al intentar asignar bases");
                    return "Error al intentar asignar bases.";
                }
            }

            /**
              * Inserto la referencia en la tabla calltype_campania. 
              * Esta tabla rompe la relación N a N que existe entre campania y calltype.
              * $DATA[id] es el id de la campaña padre.
              */
            $query = "INSERT INTO calltype_campania (id_campania, id_calltype,peso,status) 
                      SELECT $idCampaign,id_calltype,peso,status FROM calltype_campania WHERE id_campania = $DATA[id]";
            $result=$this->_DB->genQuery($query, true);

            $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 3 \n" . $query);

            if($result == false){
                $this->_log->output("Error al asociar calltypes a regestión.");	    
                return "Error al asociar calltypes a regestión.";
            }

            $query = "SELECT c.*
                      FROM campania_recargable_cliente AS c 
                      WHERE c.id_campania = $DATA[id]  
                      and c.status in ('Gestion','Display')";
            $resultCampaniaCliente = $this->_DB->fetchTable($query, true);

            $this->_log->output("paloSantoRegestion->saveNewRegestion: Calltypes  \n" . print_r($DATA['values_calltypes'],true));
            $this->_log->output("paloSantoRegestion->saveNewRegestion: Select a campania_cliente \n" . $query);

            // Por cada registro de campania_cliente voy verificando
            foreach($resultCampaniaCliente as $campania_cliente){
                $query = "SELECT * from gestion_campania
                          WHERE id_campania_recargable_cliente=$campania_cliente[id]
                          AND calltype in ($DATA[values_calltypes])";

                $resultGestionCampania = $this->_DB->fetchTable($query,true);

                $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 5 \n" . $query);
    
                if(count($resultGestionCampania)>0){
                    // Antes insertaba: ultimo_calltype e id_gestion_mejor_calltype (estaba mal)
                    $query = "INSERT into campania_recargable_cliente 
                              (id_campania, id_base_cliente,id_cliente, prioridad)  
                              VALUES
                              ('$idCampaign',
                               '$campania_cliente[id_base_cliente]',
                               '$campania_cliente[id_cliente]',
                               '$campania_cliente[prioridad]')";

                    $result = $this->_DB->genQuery($query);
                    $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 6 \n" . $query);
                }

            }
            /** FIN DEL CÓDIGO MEJORADO. */

            // TODO: Falta comprobar la consistencia en los calltypes y otros datos para la regestion
            $DATA['agente'] = explode(",",$DATA['values_agentes']);
            //Llena tabla campania_agente
            foreach($DATA['agente'] as $idAgente){
                $query = "INSERT into campania_agente (id_campania,id_agente,status) 
                          VALUES  (".$idCampaign.",'"
                                    .$idAgente."','A')";  
                $result=$this->_DB->genQuery($query, true);
                // $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 9 \n" . $query);
                if($result==false) return "Error al intentar asignar los agentes.";
            }      
            return "Campaña grabada exitosamente.";
        }//Fin campanias Recargables
        
        
        // _pre($DATA);
	$this->_log->output("=== NUEVA REGESTIÓN ===");
	$query = "INSERT into campania (nombre,fecha_inicio,fecha_fin,id_form,script,status,tipo,campania_origen) 
		  VALUES ('".$DATA['nombre']."','"
			    .$DATA['fecha_inicio']."','"
			    .$DATA['fecha_fin']."',"
			    .$DATA['id_form'].",'"
			    .$DATA['script']."','A',"
			    ."'REGESTION','$DATA[id]')";  

	$this->_log->output("paloSantoRegestion->saveNewRegestion: Query 1 \n" . $query);

	$result=$this->_DB->genQuery($query, true);	
	if($result==false)
	    return "Error al intentar crear la campaña.<br>Verifique si una campaña ya existe con ese nombre.";

	// Retorna el maxID de la tabla campania.
	$idCampaign = $this->maxID("campania"); 
        
	//Llena tabla campania_base
	$DATA['base'] = explode(",",$DATA['values_bases']);
	foreach($DATA['base'] as $idBase){
	    $query = "INSERT into campania_base (id_campania, id_base, status) 
		      VALUES  (".$idCampaign.","
				.$idBase.",'A')";  
	    $result = $this->_DB->genQuery($query, true);

	    $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 2 \n" . $query);

	    if($result==false){
		$this->_log->output("Error al intentar asignar bases");
		return "Error al intentar asignar bases.";
	    }
	}

	/**
          * Inserto la referencia en la tabla calltype_campania. 
	  * Esta tabla rompe la relación N a N que existe entre campania y calltype.
	  * $DATA[id] es el id de la campaña padre.
	  */
	$query = "INSERT INTO calltype_campania (id_campania, id_calltype) 
		  SELECT $idCampaign,id_calltype FROM calltype_campania WHERE id_campania = $DATA[id]";
	$result=$this->_DB->genQuery($query, true);

	$this->_log->output("paloSantoRegestion->saveNewRegestion: Query 3 \n" . $query);

	if($result == false){
	    $this->_log->output("Error al asociar calltypes a regestión.");	    
	    return "Error al asociar calltypes a regestión.";
	}


	/**
	  * INICIO DEL CÓDIGO MEJORADO.
	  */
	
	// $DATA['values_calltypes'] tiene la forma 1,3,5,6,9 (con todos los calltypes escogidos para regestión).
	// $DATA['id'] tiene el id de la campaña padre (campania_origen)
	
        $query = "SELECT c.*
		  FROM campania_base AS a, base_cliente AS b, campania_cliente AS c 
		  WHERE 
		  a.id_base = b.id_base AND 
		  c.id_campania = a.id_campania AND 
		  c.id_campania = $DATA[id] AND 
		  c.ci = b.ci AND 
		  c.status in ('Gestion','Display')";
	$resultCampaniaCliente = $this->_DB->fetchTable($query, true);

	$this->_log->output("paloSantoRegestion->saveNewRegestion: Calltypes  \n" . print_r($DATA['values_calltypes'],true));
	$this->_log->output("paloSantoRegestion->saveNewRegestion: Select a campania_cliente \n" . $query);

	// Por cada registro de campania_cliente voy verificando
	foreach($resultCampaniaCliente as $campania_cliente){
	    $query = "SELECT * from gestion_campania
		      WHERE id_campania_cliente=$campania_cliente[id]
		      AND calltype in ($DATA[values_calltypes])";

	    $resultGestionCampania = $this->_DB->fetchTable($query,true);

	    $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 5 \n" . $query);

	  /**
	    * 
	    * Si hay al menos una gestión con el/los calltypes a regestionar, entonces "duplico" información en campania_cliente.
	    *
	    */	      
	    if(count($resultGestionCampania)>0){
		// Antes insertaba: ultimo_calltype e id_gestion_mejor_calltype (estaba mal)
		$query = "INSERT into campania_cliente 
			  (id_campania,id_campania_consolidada, ci, prioridad)  
			  VALUES
			  ('$idCampaign',
			   '$DATA[id]',
			   '$campania_cliente[ci]',
			   '$campania_cliente[prioridad]')";

		$result = $this->_DB->genQuery($query);
		$this->_log->output("paloSantoRegestion->saveNewRegestion: Query 6 \n" . $query);
	    }

	    // Innecesario: Obtengo el Id ingresado.
	    // $idCampaniaCliente = $this->maxID("campania_cliente");  

	    /**
	      * No debo tocar la tabla gestion_campania, ni gestion_campania_detalle
	      * Es crear registros duplicados innecesarios.
	      */
	    /*
	    foreach($resultGestionCampania as $gestion_campania){
		$query = "INSERT INTO gestion_campania
			  (id_campania_cliente,calltype,timestamp,telefono,fecha,fecha_agendamiento,agente,observacion)
			  VALUES
			  ('$idCampaniaCliente',
			   '" . $this->traducirCalltype($gestion_campania['calltype'],$idCampaign) . "',
			   '$gestion_campania[timestamp]',
			   '$gestion_campania[telefono]',
			   '$gestion_campania[fecha]',
			   '$gestion_campania[fecha_agendamiento]',
			   '$gestion_campania[agente]',
			   '$gestion_campania[observacion]'
			  )";
		$result = $this->_DB->genQuery($query);
		$this->_log->output("paloSantoRegestion->saveNewRegestion: Query 7 \n" . $query);

		$idGestionCampania = $this->maxID("gestion_campania");  

		$query = "SELECT * FROM gestion_campania_detalle 
			  WHERE id_gestion_campania=$gestion_campania[id]";

		$resultGestionCampaniaDetalle = $this->_DB->fetchTable($query,true);

		foreach($resultGestionCampaniaDetalle as $gestion_campania_detalle){
		    // _pre($gestion_campania_detalle);
		    $query = "INSERT INTO gestion_campania_detalle
			      (id_gestion_campania,id_form_field,valor)
			      VALUES
			      ('$idGestionCampania',
			       '$gestion_campania_detalle[id_form_field]',
			       '$gestion_campania_detalle[valor]'
			      )";
		    $result = $this->_DB->genQuery($query);
		    // $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 8 \n" . $query);
		}
	    }
	    */
	}
	/** FIN DEL CÓDIGO MEJORADO. */

	// TODO: Falta comprobar la consistencia en los calltypes y otros datos para la regestion
	$DATA['agente'] = explode(",",$DATA['values_agentes']);
	//Llena tabla campania_agente
	foreach($DATA['agente'] as $idAgente){
	    $query = "INSERT into campania_agente (id_campania,id_agente,status) 
		      VALUES  (".$idCampaign.",'"
				.$idAgente."','A')";  
	    $result=$this->_DB->genQuery($query, true);
	    // $this->_log->output("paloSantoRegestion->saveNewRegestion: Query 9 \n" . $query);
	    if($result==false) return "Error al intentar asignar los agentes.";
	}      
	return "Campaña grabada exitosamente.";
    }

    function maxID($table)
    {
	$query = "SELECT max(id) as max_id from $table";
	$result = $this->_DB->getFirstRowQuery($query,true);
	return $result['max_id'];
    }
    
    
    function getTipoCampania($id_campania)
    {
	$query = "SELECT tipo 
		  FROM campania 
		  WHERE id = $id_campania";
        $result=$this->_DB->getFirstRowQuery($query, true);
	return $result['tipo'];
    }
    
    function getRecarga($id_recarga,$id_campania)
    {
	$query = "SELECT * 
		  FROM campania_recarga 
		  WHERE id_base = $id_recarga
                  and id_campania=$id_campania";
        $result=$this->_DB->getFirstRowQuery($query, true);
	return $result;
    }

    
    /*
    No es necesario andar traduciendo Calltypes.
    function traducirCalltype($calltype_origen, $id_campania)
    {
	$query = "SELECT id 
		  FROM TABLA_campania_calltype
		  WHERE id_campania = $id_campania
		  AND calltype_origen = $calltype_origen";

	$result = $this->_DB->getFirstRowQuery($query,true);
	return $result['id'];
    }
    */	 

}
?>