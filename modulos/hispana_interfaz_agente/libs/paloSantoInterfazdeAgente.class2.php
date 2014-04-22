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
  $Id: paloSantoInterfazdeAgente.class.php,v 1.1 2012-03-22 02:03:45 Juan Pablo Romero jromero@palosanto.com Exp $ */

require_once "/var/www/html/modules/hispana_interfaz_agente/libs/AppLogger.class.php";

class paloSantoInterfazdeAgente{
    var $_DB;
    var $errMsg;
    var $_log;

    function paloSantoInterfazdeAgente(&$pDB)
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
	$this->_log->open("/var/www/html/modules/hispana_interfaz_agente/log/hispana_interfaz_agente.log");
    }

  /**
    * Nuevas funciones.
    */
    function validarUsuario($usuario)
    {
        $query = "SELECT count(*) as cont
		  FROM campania as a, campania_agente as b 
		  WHERE a.id=b.id_campania 
		  AND b.id_agente='$usuario' 
		  AND b.status='A'
		  AND fecha_inicio<=date(now()) 	
		  AND fecha_fin>=date(now())";

        $result=$this->_DB->getFirstRowQuery($query, true);

	$this->_log->output("paloSantoInterfazdeAgente->validarUsuario \n\$query " . $query);
	$this->_log->output("paloSantoInterfazdeAgente->validarUsuario \n\$result " . print_r($result,true));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }
        if($result['cont']>0){
	    return true;
	} else{
	    return false;
	}
    }


  /**
    * Esta función es llamada cuando al hacer click en el teléfono del contacto
    * se abre la pantalla completa: todos los datos, script y formulario.
    * Esta función es invocada desde libs/paloSantoDataFormList.class.php y no desde index.php
    * @param string $ci es la cédula como único parámetro.
    */
    function obtenerClientePorCI($ci)
    {
	$query = "SELECT
		  d.ci, 
		  d.nombre,
		  d.apellido,
		  d.provincia,
		  d.ciudad,
		  d.nacimiento,
		  d.correo_personal,
		  d.correo_trabajo,
		  d.estado_civil 
		  FROM cliente as d  
		  WHERE d.ci='$ci'";

	$result=$this->_DB->getFirstRowQuery($query, true);

	$this->_log->output("paloSantoInterfazdeAgente->obtenerClientePorCI \$query " . $query);
	$this->_log->output("paloSantoInterfazdeAgente->obtenerClientePorCI \$result " . print_r($result,true));

        if($result==FALSE){
	    $this->_log->output("paloSantoInterfazdeAgente->obtenerClientePorCI error " . $this->_DB->errMsg);
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }else{ 
	    // Si retornada data obtengo información adicional.
	    $arrOtrosDatos = array("telefono","direccion","adicional");

	    foreach($arrOtrosDatos as $otrosDatos){
		$query = "SELECT descripcion,$otrosDatos 
			  FROM cliente_$otrosDatos 
			  WHERE 
			  ci = '$result[ci]' AND 
			  status = 'A'";
		$resultOtrosDatos=$this->_DB->fetchTable($query, true);

		if(count($resultOtrosDatos)>0){
		    $result[$otrosDatos] = $resultOtrosDatos;
		}
	    }
	    return $result;
	}
    }


  /**
    *
    * Esta función es llamada en el preciso instante en que el agente requiere un cliente para gestión.
    *
    */
    function obtenerCliente($user)
    {
	// echo "Ingresa a obtenerCliente()<br>";
	// Primero reviso si hay algún cliente agendado para mi usuario.
	$query = "SELECT
		  c.id as 'id_campania_cliente',
		  c.id_campania_consolidada,
		  b.id as 'id_campania',
		  b.tipo as 'tipo_campania',
		  b.nombre as 'campania', 
		  c.ci, 
		  d.nombre,
		  d.apellido,
		  d.provincia,
		  d.ciudad,
		  d.nacimiento,
		  d.correo_personal,
		  d.correo_trabajo,
		  d.estado_civil,
		  c.fecha_agendamiento,
		  c.fecha_agendamiento-now() as 'segundos_faltantes'
		  FROM campania_agente as a, campania as b, campania_cliente as c, cliente as d  
		  WHERE 
		  b.fecha_inicio<=date(now()) 
		  AND b.fecha_fin>=date(now()) 
		  AND b.status='A'
		  AND a.id_campania = b.id
		  AND a.id_agente = '$user'
		  AND a.status = 'A'
		  AND c.id_campania=b.id 
		  AND (c.agente_agendado = '$user' or c.agente_agendado = 'CAMPAÑA')
		  AND c.fecha_agendamiento<=now()
		  AND c.fecha_agendamiento>c.fecha_status
		  AND c.ci=d.ci limit 1";
	$result = $this->_DB->getFirstRowQuery($query, true);
	
	// $this->_log->output("paloSantoInterfazdeAgente->obtenerCliente (1) \$query " . $query);
	$this->_log->output("paloSantoInterfazdeAgente->obtenerCliente (1) \$result " . print_r($result,true));

	// luego revisado si usuario tiene un cliente con status Display
	//IF 1
	if(!isset($result['ci'])){
	    // echo "Ingresa a obtenerCliente() [if 1]<br>";
	    $query = "SELECT
		  c.id as 'id_campania_cliente',
		  c.id_campania_consolidada,
		  b.id as 'id_campania',
		  b.tipo as 'tipo_campania',
		  b.nombre as 'campania', 
		  c.ci, 
		  d.nombre,
		  d.apellido,
		  d.provincia,
		  d.ciudad,
		  d.nacimiento,
		  d.correo_personal,
		  d.correo_trabajo,
		  d.estado_civil,
		  c.fecha_agendamiento,
		  c.fecha_agendamiento-now() as 'segundos_faltantes'
		  FROM campania as b, campania_cliente as c, cliente as d  
		  WHERE 
		  b.fecha_inicio<=date(now()) 
		  AND b.fecha_fin>=date(now()) 
		  AND b.status='A' 
		  AND c.id_campania=b.id 
		  AND c.agente_status = '$user'
		  AND c.status = 'Display'		  
		  AND c.ci=d.ci limit 1";
	    $result = $this->_DB->getFirstRowQuery($query, true);
	}

	// IF 2
	if(!isset($result['ci'])){
	    // echo "Ingresa a obtenerCliente() [if 2]<br>";
	    // si no retorna nada, procedo con la busqueda de otros clientes
	    $query = "SELECT
		      c.id AS 'id_campania_cliente',
		      c.id_campania_consolidada,
		      b.id AS 'id_campania', 
		      b.tipo AS 'tipo_campania',
		      b.nombre AS 'campania', 
		      c.ci, 
		      d.nombre,
		      d.apellido,
		      d.provincia,
		      d.ciudad,
		      d.nacimiento,
		      d.correo_personal,
		      d.correo_trabajo,
		      d.estado_civil 
		      FROM
		      campania_agente AS a, 
		      campania AS b, 
		      campania_cliente AS c, 
		      cliente AS d,
		      base_cliente AS e,
		      campania_base AS f
		      WHERE a.id_agente='$user' 
		      AND b.id=a.id_campania
		      AND b.fecha_inicio<= date(now()) 
		      AND b.fecha_fin>= date(now()) 
		      AND b.status='A' 
		      AND a.status='A'
		      AND c.id_campania=b.id 
		      AND c.status is null
		      AND c.ci= d.ci	
		      AND a.id_campania = f.id_campania
		      AND c.ci = e.ci
		      AND e.id_base = f.id_base
		      AND f.status = 'A'
		      ORDER BY c.prioridad 
		      LIMIT 1"; 
	    $result=$this->_DB->getFirstRowQuery($query, true);

	    // $this->_log->output("paloSantoInterfazdeAgente->obtenerCliente (2) \$query " . $query);
	    $this->_log->output("paloSantoInterfazdeAgente->obtenerCliente (2) \$result " . print_r($result,true));
	}

        if($result==FALSE){
	    $this->_log->output("paloSantoInterfazdeAgente->obtenerCliente error " . $this->_DB->errMsg);
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }else{ // SI RETORNA DATA ACTUALIZO TABLA GESTION Y RETORNO RESULT
	    $query = "UPDATE campania_cliente 
		      SET status ='Display', 
		      fecha_status=NOW(),
		      agente_status ='$user',
		      fecha_agendamiento = NULL,
		      agente_agendado = NULL
		      WHERE ci = '$result[ci]'
		      AND id_campania =$result[id_campania]";
	    $this->_DB->genQuery($query, true);

	    $arrOtrosDatos = array("direccion","adicional");

	    foreach($arrOtrosDatos as $otrosDatos){
		$query = "SELECT descripcion,$otrosDatos 
			  FROM cliente_$otrosDatos 
			  WHERE ci = '$result[ci]' 
			  AND status='A'";

		$resultOtrosDatos=$this->_DB->fetchTable($query, true);

		if(count($resultOtrosDatos)>0){
		    $result[$otrosDatos] = $resultOtrosDatos;
		}
	    }
	    // Procedimiento especial para obtener teléfonos
	    $query = "SELECT distinct a.telefono,a.descripcion
		      FROM cliente_telefono as a 
		      LEFT JOIN gestion_campania as b 
		      ON a.telefono = b.telefono 
		      LEFT JOIN calltype as c 
		      ON b.calltype=c.id 
		      WHERE a.ci='$result[ci]'
		      ORDER BY peso desc";

	    $arrFonos = $this->_DB->fetchTable($query,true);
	    foreach($arrFonos as $k => $regFono){
		$query = "SELECT id,calltype 
			  FROM gestion_campania 
			  WHERE id_campania_cliente=$result[id_campania_cliente]
			  AND telefono=$regFono[telefono]";
		$resFono = $this->_DB->getFirstRowQuery($query,true);
		if(sizeof($resFono)>0){
		    $arrFonos[$k]['marcado'] = "si";
		}else{
		    $arrFonos[$k]['marcado'] = "no";
		}  
	    }
	    $result['telefono'] = $arrFonos;
	    return $result;
	}
    }

  /**
    * Esta función es llamada cuando hay interacción entre la página principal 
    * del cliente y el formulario de gestión o actualización de datos.
    */
    function obtenerClientePorIdCampaniaCliente($id_campania_cliente)
    {
	    // echo $id_campania_cliente;
	    $query = "SELECT
		      c.ci,
		      c.id as 'id_campania_cliente',
		      c.id_campania_consolidada,
		      c.fecha_agendamiento,
		      c.fecha_agendamiento-now() as 'segundos_faltantes',
		      b.id as 'id_campania', 
		      b.tipo as 'tipo_campania',
		      b.nombre as 'campania',    
		      d.nombre,
		      d.apellido,
		      d.provincia,
		      d.ciudad,
		      d.nacimiento,
		      d.correo_personal,
		      d.correo_trabajo,
		      d.estado_civil 
		      FROM 
		      campania_agente as a, 
		      campania as b, 
		      campania_cliente as c, 
		      cliente as d  
		      WHERE
		      b.id = a.id_campania AND 
		      b.fecha_inicio <= date(now()) AND 
		      b.fecha_fin >= date(now()) AND 
		      b.status = 'A' AND a.status = 'A' AND 
		      c.id_campania = b.id AND 
		      c.id = $id_campania_cliente AND
		      c.ci = d.ci 
		      limit 1";

	    // echo $query;

	    $result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
	    $this->_log->output("paloSantoInterfazdeAgente->obtenerClientePorUserCI error " . $this->_DB->errMsg);
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }else{
	    $arrOtrosDatos = array("direccion","adicional");

	    foreach($arrOtrosDatos as $otrosDatos){
		$query = "SELECT descripcion,$otrosDatos 
			  FROM cliente_$otrosDatos 
			  WHERE ci = '$result[ci]'
			  AND status = 'A'";

		$resultOtrosDatos=$this->_DB->fetchTable($query, true);

		if(count($resultOtrosDatos)>0){
		    $result[$otrosDatos] = $resultOtrosDatos;
		}
	    }

	    // Procedimiento especial para obtener teléfonos
	    $query = "SELECT distinct a.telefono,a.descripcion
		      FROM cliente_telefono as a 
		      LEFT JOIN gestion_campania as b 
		      ON a.telefono = b.telefono 
		      LEFT JOIN calltype as c
		      ON b.calltype=c.id 
		      WHERE a.ci='$result[ci]' 
		      AND a.status = 'A'
		      ORDER BY peso desc";

	    $arrFonos = $this->_DB->fetchTable($query,true);
	    foreach($arrFonos as $k => $regFono){
		$query = "SELECT id,calltype 
			  FROM gestion_campania 
			  WHERE id_campania_cliente=$result[id_campania_cliente]
			  AND telefono=$regFono[telefono]";
		$resFono = $this->_DB->getFirstRowQuery($query,true);
		if(sizeof($resFono)>0){
		    $arrFonos[$k]['marcado'] = "si";
		}else{
		    $arrFonos[$k]['marcado'] = "no";
		}
	    }
	    $result['telefono'] = $arrFonos;
	    return $result;
	}
    }

    function getForm($id_campania)
    {
        $query = "SELECT id_form FROM campania WHERE id=$id_campania";
        $result=$this->_DB->getFirstRowQuery($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return false;
        }else{
	    return $result['id_form'];
	}
    }

    function guardarGestion($DATA,$agente)
    {
	$query = "INSERT INTO gestion_campania (id_campania_cliente,calltype,timestamp,telefono,observacion,agente,fecha)
		  VALUES ('" . $DATA['id_campania_cliente'] . "','"
			     . $DATA['calltype'] . "','"
			     . $DATA['timestamp'] . "','"
			     . $DATA['telefono'] . "','" 
			     . $DATA['observacion'] . "','" 
			     . $agente .  "',now())";
	$result = $this->_DB->genQuery($query);

	// DEBUG
	// $this->_log->output("paloSantoInterfazdeAgente->guardarGestion " . $query);
    
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
	    //echo "<b>Esta posibilidad hay que validar con María José Castro: </b>" . $this->errMsg;
	    return false;  
	}

	$id_gestion_campania = $this->maxID("gestion_campania");

	if($DATA['fecha']!=""){ // significa que es posible agendar
		$query = "UPDATE gestion_campania 
			  SET fecha_agendamiento='" . $DATA['fecha'] . " " . $DATA['horas'] . ":" . $DATA['minutos'] . ":00" . "'
			  WHERE id=$id_gestion_campania";
		$result = $this->_DB->genQuery($query);
		$query = "UPDATE campania_cliente 
			  SET fecha_agendamiento='" . $DATA['fecha'] . " " . $DATA['horas'] . ":" . $DATA['minutos'] . ":00" . "',
			  agente_agendado = '$DATA[agente_agendado]',
			  status = 'Gestion',
			  ultimo_calltype = '$DATA[calltype]'
			  WHERE id=$DATA[id_campania_cliente]";
		$result = $this->_DB->genQuery($query);
	}else{
		$query = "UPDATE campania_cliente 
			  SET fecha_agendamiento = NULL,
			  agente_agendado = NULL,
			  status = 'Gestion',
			  ultimo_calltype = '$DATA[calltype]'
			  WHERE id=$DATA[id_campania_cliente]";
		$result = $this->_DB->genQuery($query);
	}

	// Inicio: Actualizo id_gestion_mejor_calltype en campania_cliente
	$query = "SELECT a.id, calltype, b.peso 
		  FROM gestion_campania AS a, calltype as b 
		  WHERE 
		  id_campania_cliente = $DATA[id_campania_cliente] AND
		  a.calltype=b.id
		  ORDER BY peso DESC,fecha DESC LIMIT 1";
	$result = $this->_DB->getFirstRowQuery($query, true);
	$query = "UPDATE campania_cliente SET id_gestion_mejor_calltype= $result[id] where id = $DATA[id_campania_cliente]";
	$this->_DB->genQuery($query);
	// Fin: Actualizo id_gestion_mejor_calltype en campania_cliente
	// echo $query;

	foreach($DATA as $k => $valor){
	    if(is_numeric($k)){ // si es numerico es id_form_field
		$query = "INSERT INTO gestion_campania_detalle (id_gestion_campania,id_form_field,valor) 
			  VALUES ('" . $id_gestion_campania . "','" 
				     . $k . "','"
				     . $valor . "')";
		$result = $this->_DB->genQuery($query);
		// SI AQUI DA ERROR POR VIOLACION A CONSTRAINT UNIQUE, ENTONCES ACTUALIZAR.
	    }  
	}
    }

    function maxID($table)
    {
	$query = "SELECT max(id) as max_id from $table";
	$result = $this->_DB->getFirstRowQuery($query,true);
	return $result['max_id'];
    }

    function getExtensionAgente($nombre)  // este lo toma de SQLITE
    {
        $query = "SELECT extension FROM acl_user WHERE name='$nombre'";
        $result=$this->_DB->getFirstRowQuery($query, true);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result['extension'];
    }

    /**
      * Tiene como objetivo ver si es factible ir a otro cliente.
      * Para ir a otro cliente es necesario que al menos 1 número esté como Contactado/Agendado
      * o que todos los número esté como NO CONTACTADO.
      */
    function verOtroCliente($arrInfoCliente) 
    {
	$query = "SELECT clase, count(*) as cont 
		  FROM gestion_campania AS a, calltype AS b, campania_cliente AS c
		  WHERE 
		  a.id_campania_cliente = $arrInfoCliente[id_campania_cliente] AND 
		  c.id = a.id_campania_cliente AND 
		  c.status = 'Gestion' AND 
		  b.id = a.calltype AND 
		  b.clase in ('Contactado','Agendado')
		  GROUP by clase";

	$result = $this->_DB->getFirstRowQuery($query, true);

	if(isset($result['cont']) && $result['cont']>0){ // Si al menos un telefono es contactado
	    return true;
	}else{
	    $query = "SELECT telefono,clase 
		      FROM gestion_campania AS a, calltype AS b, campania_cliente AS c
		      WHERE id_campania_cliente = $arrInfoCliente[id_campania_cliente]
		      AND c.id = a.id_campania_cliente
		      AND c.status = 'Gestion'
		      AND b.id = a.calltype 
		      AND b.clase = 'No contactado'
		      GROUP BY telefono,clase";
	    $result = $this->_DB->fetchTable($query, true);

	    if(sizeof($result)>=sizeof($arrInfoCliente['telefono'])){ // Si todos los telefonos son contactados.
		return true;
	    }
	}
	return false;
    }

    function verificarGestionAnterior($id_campania_cliente)
    {
	/**  Basado en id_capania_cliente busco la id_campania_consolidada. */
	$query = "SELECT id, calltype, observacion 
		  FROM gestion_campania 
		  WHERE id =  (SELECT max(id) FROM gestion_campania 
			      WHERE id_campania_cliente IN
				    (SELECT id FROM campania_cliente 
				    WHERE ci = 
					(SELECT ci FROM campania_cliente where id = $id_campania_cliente) AND
					id_campania_consolidada = (
			      SELECT id_campania_consolidada 
			      FROM campania_cliente 
			      WHERE id = $id_campania_cliente)))";

	$result = $this->_DB->getFirstRowQuery($query,true);
	$max_gestion_id = $result['id'];
	$calltype = $result['calltype'];
	$observacion = $result['observacion'];
	if($max_gestion_id == ""){ // si no hay gestion previa salgo
	    return false;
	}
	else{
	    $query = "SELECT id_form_field, valor 
		      FROM gestion_campania_detalle
		      WHERE  
		      id_gestion_campania = $max_gestion_id";
	    $result = $this->_DB->fetchTable($query);

	    $arrValoresGestion = array('calltype'=>$calltype, 'observacion'=>$observacion);

	    foreach($result as $gestion_valor){
		$arrValoresGestion[$gestion_valor[0]] = $gestion_valor[1];
	    }
	    return $arrValoresGestion;
	}
    }

    /** Función innecesaria */
    function liberarClientesColgados($minutos)
    {
	$query = "UPDATE campania_cliente
		  SET status=NULL, fecha_status = NULL, agente_status=NULL
		  WHERE status='Display'
		  AND DATE_ADD(fecha_status, INTERVAL $minutos MINUTE)<=NOW()";
	$result = $this->_DB->genQuery($query);
    }

    function getNumClientesAgendados($agente_agendado,$id_campania)	
    {
	$query = "SELECT count(*) as cont
		  FROM campania_cliente
		  WHERE agente_agendado = '$agente_agendado'";

	$result = $this->_DB->getFirstRowQuery($query,true);
	$arrResult['cont_total'] = $result['cont'];

	$query = "SELECT count(*) as cont
		  FROM campania_cliente
		  WHERE agente_agendado = '$agente_agendado' 
		  AND id_campania = $id_campania";

	$result = $this->_DB->getFirstRowQuery($query,true);
	$arrResult['cont_campania'] = $result['cont'];

	return $arrResult;
    }

    function getGestionesPrevias($ci, $id_campania_consolidada)
    {
	$query = "SELECT a.fecha, concat(d.nombre,' (',d.tipo,')') as nombre_campania, a.agente, concat(b.clase,' - ',b.descripcion) as calltype, a.telefono, a.observacion
		  FROM gestion_campania AS a, calltype AS b, campania_cliente AS c, campania AS d 
		  WHERE 
		  b.id = a.calltype AND 
		  a.id_campania_cliente = c.id AND
		  c.id_campania_consolidada = $id_campania_consolidada AND
		  c.id_campania = d.id AND
		  c.ci = '$ci'
		  ORDER BY fecha desc";
	$result = $this->_DB->fetchTable($query,true);
	return $result;
    }

    function getCalltypeInfo($id_campania)	
    {
	$query = "SELECT a.clase, a.descripcion, a.definicion 
		  FROM calltype AS a, calltype_campania AS b
		  WHERE 
		  b.id_campania = $id_campania AND
		  b.id_calltype = a.id AND 
		  a.status = 'A'
		  ORDER BY a.clase, a.descripcion";

	$result = $this->_DB->fetchTable($query, true);
	return $result;
    }
    
    function listarBreaks()
    {
	$query = "SELECT id, name 
		  FROM break 
		  WHERE status='A'
		  ORDER BY name";
	$breaks = $this->_DB->fetchTable($query, true);
	foreach($breaks as $break){
	    $result[$break['id']] = $break['name'];
	}
	return $result;
    }

    function pausar($id_break,$user)
    {
	$query = "INSERT INTO break_agente (id_agente,id_break,datetime_init) 
		  VALUES ('$user','$id_break',now())";
	$this->_DB->genQuery($query);	
    }

    function usuarioEnPausa($user)
    {
	$query = "SELECT count(*) as cont
		  FROM break_agente
		  WHERE id_agente = '$user'
		  AND datetime_end IS NULL";
	$result = $this->_DB->getFirstRowQuery($query,true);
	return ($result['cont']>0)?true:false;
    }

    function sacarPausa($user)
    {
/** 
Corregir la forma de calculo de la duracion
SUM(UNIX_TIMESTAMP(IFNULL(break_agente.datetime_end, NOW())) - UNIX_TIMESTAMP(break_agente.datetime_init)) AS duracion

*/
	$query = "UPDATE break_agente 
		  SET datetime_end=now(),
		  duration = sec_to_time( time(now())-time(datetime_init) )
		  WHERE datetime_end IS null 
		  AND id_agente='$user'";
	$this->_DB->genQuery($query);
    }
}
?>