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
  $Id: paloSantoCrearcampaña.class.php,v 1.1 2012-03-22 09:03:45 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoCrearcampaña{
    var $_DB;
    var $errMsg;

    function paloSantoCrearcampaña(&$pDB)
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

    function getNumCrearcampaña($filter_field, $filter_value)
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

    function getCrearcampaña($limit, $offset, $filter_field, $filter_value)
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

    function getCrearcampañaById($id)
    {
        $query = "SELECT * FROM campania WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getForms()
    {
        $query = "SELECT id, nombre FROM form where estatus='A'";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getBases()
    {
        $query = "SELECT id, nombre FROM base";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getBasesCampania($id_campania)
    {
        $query = "SELECT a.id, a.nombre 
		  FROM base as a, campania_base as b
		  WHERE 
		  b.status = 'A'
		  AND a.id=b.id_base
		  AND b.id_campania=$id_campania";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getOtrasBases($arrBases)
    {
	$not_in = " WHERE id NOT IN (";
	foreach($arrBases as $k => $value){
	    $not_in .= "$k,";
	}

	$not_in  = substr($not_in,0,strlen($not_in)-1) . ")"; // retrocedo 1 espacio
        $query = "SELECT id, nombre 
		  FROM base $not_in";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }



    function getUsuarios($grupoUsuario) // La data retornada la toma de SQLITE /var/www/db/acl.db
    {
        $query = "SELECT a.id, a.name, a.description, a.extension 
		  FROM acl_user as a, acl_membership as b,acl_group as c 
		  WHERE c.name='$grupoUsuario' 
		  AND c.id=b.id_group 
		  AND b.id_user=a.id";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }

    function getUsuariosElegidos($grupoUsuario,$arrUsuariosElegidos,$NOT=NULL) // La data retornada la toma de SQLITE /var/www/db/acl.db
    {
	$in = "";
	if(isset($NOT) && $NOT == "not"){
	    $in = " AND a.name NOT IN (";
	}else{
	    $in = " AND a.name IN (";
	}
	foreach($arrUsuariosElegidos as $k => $value){
	    $in .= "'$k',";
	}
	$in = substr($in,0,strlen($in)-1) . ")"; // retrocedo 1 espacio
	
        $query = "SELECT a.id, a.name, a.description, a.extension 
		  FROM acl_user as a, acl_membership as b,acl_group as c 
		  WHERE c.name='$grupoUsuario' 
		  AND c.id=b.id_group 
		  AND b.id_user=a.id $in";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }


    function getAgentesCampania($id_campania)
    {
        $query = "SELECT id_agente 
		  FROM campania_agente 
		  WHERE status='A'
		  AND id_campania=$id_campania";

        $result=$this->_DB->fetchTable($query, true);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;

    }

    function guardarCampaña($DATA) // La data retornada la toma de SQLITE /var/www/db/acl.db
    {   
	$query = "INSERT into campania (nombre,fecha_inicio,fecha_fin,id_form,script,tipo,status) 
		  VALUES ('".$DATA['nombre']."','"
			    .$DATA['fecha_inicio']."','"
			    .$DATA['fecha_fin']."',"
			    .$DATA['id_form'].",'"
			    .$DATA['script']."','RECARGABLE','A')";  
	$result=$this->_DB->genQuery($query, true);
	if($result==false)
	    return "Error al intentar crear la campaña.<br>Verifique si una campaña ya existe con ese nombre.";

	// Workaround para obtener el id de la campaña recien ingresada
        $query = "SELECT id from campania 
		  WHERE nombre ='$DATA[nombre]'";
	$result=$this->_DB->fetchTable($query, true);
	$idCampaign = $result[0]['id'];

	//Llena tabla campania_base
	/*$DATA['base'] = explode(",",$DATA['values_bases']);
	foreach($DATA['base'] as $idBase){
	    $query = "INSERT into campania_base (id_campania, id_base, status) 
		      VALUES  (".$idCampaign.","
				.$idBase.",'A')";  
	    $result=$this->_DB->genQuery($query, true);

	    if($result==false){ // en caso de haber error, hacer UPDATE{
		return "Error al intentar asignar bases.";
	    }

	}*/

	//Llena tablas tomando las bases previamente ingresadas.
        /*$query = "SELECT distinct(ci), prioridad 
		  FROM campania_base as a, base_cliente as b 
		  WHERE a.id_campania=$idCampaign 
		  AND a.id_base=b.id_base";

	$result=$this->_DB->fetchTable($query, true);
	foreach($result as $cliente){
	    $query = "INSERT INTO campania_cliente (id_campania,id_campania_consolidada, ci, prioridad) 
		      VALUES($idCampaign,$idCampaign,'$cliente[ci]',$cliente[prioridad])";

	    $result=$this->_DB->genQuery($query, true);
	    if($result==false)
		return "Error al intentar guardar clientes a la campaña.";
	}*/


	$DATA['agente'] = explode(",",$DATA['values_agentes']);
	//Llena tabla campania_agente
	foreach($DATA['agente'] as $idAgente){
	    $query = "INSERT into campania_agente (id_campania,id_agente,status) 
		      VALUES  (".$idCampaign.",'"
				.$idAgente."','A')";  
	    $result=$this->_DB->genQuery($query, true);
	    if($result==false)
		return "Error al intentar asignar los agentes.";

	}
	return "Campaña grabada exitosamente.";
    }


    function actualizarCampania($DATA)
    {
	// Actualizo cabecera
	$query = "UPDATE campania 
		  SET nombre = '$DATA[nombre]',
		      fecha_inicio = '$DATA[fecha_inicio]',
		      fecha_fin = '$DATA[fecha_fin]',
		      id_form = '$DATA[id_form]',
		      script = '$DATA[script]'
		  WHERE id = $DATA[id]";
	$result=$this->_DB->genQuery($query, true);

	// coloco status I a campania_base y campania_agente
	$query = "UPDATE campania_agente
		  SET status='I'
		  WHERE id_campania=$DATA[id]";
	$result=$this->_DB->genQuery($query, true);

	$query = "UPDATE campania_base
		  SET status='I'
		  WHERE id_campania=$DATA[id]";
	$result=$this->_DB->genQuery($query, true);

	$DATA['base'] = explode(",",$DATA['values_bases']);

	foreach($DATA['base'] as $idBase){
	    $query = "INSERT into campania_base (id_campania, id_base, status) 
		      VALUES ($DATA[id],$idBase,'A')";  
	    $result=$this->_DB->genQuery($query, true);

	    if($result==false){ // en caso de haber error, hacer UPDATE{
		$query = "UPDATE campania_base
			  SET status='A'
			  WHERE id_campania=$DATA[id]
			  and id_base=$idBase";
		$result=$this->_DB->genQuery($query, true);

	    }else{ // si no hay error, es porque se guarda una base nueva con todo y clientes
		$query = "SELECT distinct(ci), prioridad 
			  FROM campania_base as a, base_cliente as b 
			  WHERE a.id_campania=$DATA[id]
			  AND a.id_base=b.id_base";

		$result=$this->_DB->fetchTable($query, true);
		foreach($result as $cliente){
		    $query = "INSERT INTO campania_cliente (id_campania, id_campania_consolidada,ci, prioridad) 
			      VALUES($DATA[id],$DATA[id],'$cliente[ci]',$cliente[prioridad])";
		    $result=$this->_DB->genQuery($query, true);
		}
	    }
	}

	$DATA['agente'] = explode(",",$DATA['values_agentes']);
	
	foreach($DATA['agente'] as $idAgente){
	    if($idAgente!=""){
		$query = "INSERT into campania_agente (id_campania,id_agente,status) 
		      VALUES  ($DATA[id],'$idAgente','A')"; 
		
		$result=$this->_DB->genQuery($query, true);
		if($result==false){
		    $query = "UPDATE campania_agente
			  SET status='A'
			  WHERE id_campania=$DATA[id]
			  and id_agente='$idAgente'";
		    $result=$this->_DB->genQuery($query, true);
		}
	    }
	}




    }

}
?>
