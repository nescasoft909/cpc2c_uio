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
  $Id: paloSantoIngresodeBasedeClientes.class.php,v 1.1 2012-03-19 08:03:31 Juan Pablo Romero jromero@palosanto.com Exp $ */
class paloSantoIngresodeBasedeClientes{
    var $_DB;
    var $errMsg;

    function paloSantoIngresodeBasedeClientes(&$pDB)
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

    function getNumIngresodeBasedeClientes($filter_field, $filter_value)
    {
        $where    = "";
        $arrParam = null;
        if(isset($filter_field) & $filter_field !=""){
            $where    = "where $filter_field like ?";
            $arrParam = array("$filter_value%");
        }

        $query = "SELECT COUNT(*) FROM table $where";

        $result=$this->_DB->getFirstRowQuery($query, false, $arrParam);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return 0;
        }
        return $result[0];
    }

    function getIngresodeBasedeClientes($limit, $offset, $filter_field, $filter_value)
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

    function getIngresodeBasedeClientesById($id)
    {
        $query = "SELECT * FROM table WHERE id=?";

        $result=$this->_DB->getFirstRowQuery($query, true, array("$id"));

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result;
    }
  
    function guardarActualizar($_POST,$_FILES)
    {
	$arrResult= array();
	if(isset($_FILES) && isset($_POST)){
	    $id_base = (int) $this->guardarNombreBase($_POST['nombre_base']);
	    if (($handle = fopen($_FILES['archivo_de_clientes']['tmp_name'], "r"))!==FALSE && isset($id_base) && $id_base!="") {

		$iFila = 0;
		$iErrores = 0;
		$iExitosos = 0;
		$camposTablaCliente = array("ci","nombre","apellido","provincia","ciudad","nacimiento","correo_personal","correo_trabajo","estado_civil");
		while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
		    $numCamposRegistro = count($data);
		    if($iFila==0){ // si es la cabecera
			$dataCabecera = $data;
			$numCamposCabecera = $numCamposRegistro;
			$query_base = "insert into cliente ("; 
			
			for ($iCampo=0;$iCampo<$numCamposRegistro;$iCampo++){
			    if(stripos(trim($dataCabecera[$iCampo]),":")===false && in_array(strtolower(trim($dataCabecera[$iCampo])),$camposTablaCliente) ){ // Si el campo de la cabecera no es de tipo t, d ó a.
				$query_base .= strtolower(trim($dataCabecera[$iCampo])) . ",";
			    }

			}
			$query_base .= "id_base) values(";
		    }else{ // si no es la cabecera
			if($numCamposRegistro != $numCamposCabecera){ // Si hay error por incongruencia en el tamaño de la fila.
			    $arrResult['errorNumCampos'][] = $iFila;
			}else{ // si no hay error
			    $query_real=$query_base;

			    for($iCampo=0;$iCampo<$numCamposRegistro;$iCampo++){
				$data[$iCampo] = trim($data[$iCampo]);		
				if(strtolower(trim($dataCabecera[$iCampo]))=="ci"){ // obtiene la cedula del cliente
				    $ci = $data[$iCampo];
				}
				if(strtolower(trim($dataCabecera[$iCampo])) == "prioridad"){
				    $prioridad = $data[$iCampo];
				}
				if(stripos(trim($dataCabecera[$iCampo]),":")===false && in_array(strtolower(trim($dataCabecera[$iCampo])),$camposTablaCliente)){ // Si el campo de la cabecera no es de tipo t, d ó a.
				    $query_real .= "'" . $data[$iCampo] . "',";
				}elseif(stripos(trim($dataCabecera[$iCampo]),"t:")===0 && $data[$iCampo]!=""){
				    $query = "INSERT into cliente_telefono (telefono,ci,descripcion,id_base) 
					      VALUES ('$data[$iCampo]','$ci','" . substr($dataCabecera[$iCampo],2) . "',$id_base)";

				    $result=$this->_DB->genQuery($query);
				    if($result==FALSE){
					$arrResult['errorTelefono'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " ". $data[$iCampo];				
				    }else{
					$arrResult['successTelefono'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " " . $data[$iCampo];				
				    }
				}elseif(stripos(trim($dataCabecera[$iCampo]),"d:")===0 && $data[$iCampo]!=""){
				    $query = "INSERT into cliente_direccion (direccion,ci,descripcion,id_base) 
					      VALUES ('" . mysql_real_escape_string($data[$iCampo]) . "','$ci','" . substr($dataCabecera[$iCampo],2) . "',$id_base)";

				    $result=$this->_DB->genQuery($query);
				    if($result==FALSE){
					$arrResult['errorDireccion'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " ". $data[$iCampo];				
				    }else{
					$arrResult['successDireccion'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " " . $data[$iCampo];				
				    }
				}elseif(stripos(trim($dataCabecera[$iCampo]),"a:")===0 && $data[$iCampo]!=""){
				    $query = "INSERT into cliente_adicional (adicional,ci,descripcion,id_base) 
					      VALUES ('" .  mysql_real_escape_string($data[$iCampo]) . "','$ci','" . substr($dataCabecera[$iCampo],2) . "',$id_base)";

				    $result=$this->_DB->genQuery($query);
				    if($result==FALSE){
					$arrResult['errorAdicional'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " ". $data[$iCampo];				
				    }else{
					$arrResult['successAdicional'][$iFila+1] = $ci . " " . substr($dataCabecera[$iCampo],2) . " " . $data[$iCampo];				
				    }
				}			    
			    }
			    $query_real .="$id_base)";  
    
			    // echo $query_real . "<br>";
			    $result=$this->_DB->genQuery($query_real); // guardo en tabla cliente
			    if($result==FALSE){
				$this->errMsg = $this->_DB->errMsg;
				// echo $this->errMsg . "<br>";
				$arrResult['errorCliente'][$iFila+1] = $ci;
				$iErrores++;
			    }else{
				$arrResult['successCliente'][$iFila+1] = $ci;
				$iExitosos++;
			    }

			    $query = "insert into base_cliente values($id_base,'$ci',$prioridad)";
			    $result = $this->_DB->genQuery($query);	// guardo en tabla base_cliente
			    if($result==FALSE){
				$this->errMsg = $this->_DB->errMsg;
				// hay que ver que mas se hace con este error
			    }else{
				$arrResult['successBaseCliente'][$iFila+1] = $ci;
			    }
			}
		    } 
		$iFila++; // va a la siguiente fila
		}
	    $arrResult['registros'] = $iFila-1;
	    $arrResult['exitosos'] = $iExitosos;
	    $arrResult['errores'] = $iFila-1-$iExitosos;
	    fclose($handle); // Cierra el archivo
	    }
	}
      return $arrResult; // Retorna el resultado
    }

    function guardarNombreBase($nombre)
    {
        $query = "INSERT into base (nombre,fecha) values ('$nombre',now())";
        $result=$this->_DB->genQuery($query);
        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }

	$query = "SELECT id from base where nombre ='$nombre'";
	$result=$this->_DB->getFirstRowQuery($query);

        if($result==FALSE){
            $this->errMsg = $this->_DB->errMsg;
            return null;
        }
        return $result[0];
    }

    function resultadoTemplate($arrResult,$_FILES)
    {
      $file_name = $_FILES['archivo_de_clientes']['name'];
      $file_size = $_FILES['archivo_de_clientes']['size'];
      $file_type = $_FILES['archivo_de_clientes']['type'];
   
      $errorClientes = "";
      foreach($arrResult['errorCliente'] as $k => $v){
	  $errorClientes .= "Fila: " . $k . " - CI:" . $v . "<br>";
      }

      $errorTelefonos = "";
      foreach($arrResult['errorTelefono'] as $k => $v){
	  $errorTelefonos .= "Fila: " . $k . " - CI:" . $v . "<br>";
      }

      $errorDirecciones = "";
      foreach($arrResult['errorDireccion'] as $k => $v){
	  $errorDirecciones .= "Fila: " . $k . " - CI:" . $v . "<br>";
      }

      $errorAdicionales = "";
      foreach($arrResult['errorAdicional'] as $k => $v){
	  $errorAdicionales .= "Fila: " . $k . " - CI:" . $v . "<br>";
      }


      $countSuccessBaseCliente = count($arrResult['successBaseCliente']);
      $countSuccessCliente = count($arrResult['successCliente']);
      $countErrorCliente = count($arrResult['errorCliente']);
      $countSuccessTelefono = count($arrResult['successTelefono']);
      $countErrorTelefono = count($arrResult['errorTelefono']);
      $countSuccessDireccion = count($arrResult['successDireccion']);
      $countErrorDireccion = count($arrResult['errorDireccion']);
      $countSuccessAdicional = count($arrResult['successAdicional']);
      $countErrorAdicional = count($arrResult['errorAdicional']);

    $countMalFormato = $arrResult['errores'] -$countErrorCliente;

      // Para mayor detalle ver el contenido de cada Arreglo retornado.
      // TODO: Dividir HTML del código.
      return <<<EOD
	    <table width="100%" border="0"  cellspacing="0" cellpadding="4" align="center">
		<tr class="moduleTitle"><td>Resultado de la carga del archivo</td></tr>
	    </table>
	    <table class="tabForm" style="font-size: 16px;" width="100%">
		<tr class="letra12">
		    <td align="left"><b>Nombre del archivo:</b> $file_name</td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Tamaño:</b> $file_size bytes</td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Tipo:</b> $file_type </td>
		</tr>
		<tr class="letra12">
		    <td align="left">&nbsp;</td>
		</tr>
		<tr class="letra12">
		    <td align="left"><font size=2><b>RESUMEN DE LA CARGA</b></td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Registros (Filas):</b> $arrResult[registros] </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Registros de clientes nuevos: </b> $arrResult[exitosos] </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Registro con errores:</b> $arrResult[errores] </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Cédulas duplicadas:</b> $countErrorCliente </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Fila con mal formato:</b> $countMalFormato </td>
		</tr>

<!--
		<tr class="letra12">
		    <td align="left"><b>Clientes asociados a una base nueva:</b> $countSuccessBaseCliente </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Registros de clientes nuevos:</b> $countSuccessCliente </td>
		</tr>
		<tr class="letra12">
 		    <td align="left"><b>Registros de clientes no cargados:</b> $countErrorCliente </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Teléfonos nuevos:</b> $countSuccessTelefono </td>
		</tr>
		<tr class="letra12">
 		    <td align="left"><b>Teléfonos no cargados:</b> $countErrorTelefono </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Direcciones nuevas:</b> $countSuccessDireccion </td>
		</tr>
		<tr class="letra12">
 		    <td align="left"><b>Direcciones no cargadas:</b> $countErrorDireccion </td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Datos adicionales nuevos:</b> $countSuccessAdicional </td>
		</tr>
		<tr class="letra12">
 		    <td align="left"><b>Datos adicionales no cargados:</b> $countErrorAdicional </td>
		</tr>
		<tr class="letra12">
 		    <td align="left"><font size=2><hr><b>DETALLE DE REGISTROS NO CARGADOS</b></td>
		</tr>
		<tr class="letra12">
		    <td align="left"><b>Clientes no cargados</b><br>
		    $errorClientes
		    </td>
		</tr>		
		<tr class="letra12">
		    <td align="left"><b>Teléfonos no cargados</b><br>
		    $errorTelefonos
		    </td>
		</tr>	
		<tr class="letra12">
		    <td align="left"><b>Direcciones no cargadas</b><br>
		    $errorDirecciones
		    </td>
		</tr>	
		<tr class="letra12">
		    <td align="left"><b>Datos adicionales no cargados</b><br>
		    $errorAdicionales
		    </td>
		</tr>	
	    </table>
-->
EOD;
    }


}
?>