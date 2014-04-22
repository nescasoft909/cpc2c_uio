<table width="100%" border=0 class="tabForm" height="400">
  <tr>
    <td valign=top width=25%>
      <table cellpadding="2" cellspacing="0" width="100%" border="0">
	<tr class="table_title_row">
	  <td class="table_title_row">CLIENTE</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Datos básicos</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Número:</b> {$arrInfoCliente.numero}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Cédula:</b> {$arrInfoCliente.ci}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Nombre:</b> {$arrInfoCliente.nombre}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Apellido:</b> {$arrInfoCliente.apellido}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Provincia:</b> {$arrInfoCliente.provincia}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Ciudad:</b> {$arrInfoCliente.ciudad}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Nacimiento:</b> {$arrInfoCliente.nacimiento}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Correo personal:</b> {$arrInfoCliente.correo_personal}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Correo del trabajo:</b> {$arrInfoCliente.correo_trabajo}</td>
	</tr>
	<tr class="table_data">
	  <td align="left" class="table_data"><b>Estado civil:</b> {$arrInfoCliente.estado_civil}</td>
	</tr>	
        <tr class="table_data">
	  <td align="left" class="table_data"><b>Prioridad:</b> {$arrInfoCliente.prioridad}</td>
	</tr>	
	<tr class="table_title_row">
	  <td align="left" class="table_title_row">Direcciones</td>
	</tr>	
{foreach from=$arrInfoCliente.direccion item=direccion}
	<tr class="table_data">
	  <td align="left" class="table_data"><b>{$direccion.descripcion}:</b> {$direccion.direccion}</td>
	</tr>	
{/foreach}
	<tr class="table_title_row">
	  <td align="left" class="table_title_row">Datos adicionales</td>
	</tr>	
{foreach from=$arrInfoCliente.adicional item=adicional}
	<tr class="table_data">
	  <td align="left" class="table_data"><b>{$adicional.descripcion}:</b> {$adicional.adicional}</td>
	</tr>	
{/foreach}

      </table>
    </td>
<!-- Script a continuación -->
    <td valign=top width=30%>
      <table cellpadding="2" cellspacing="0" width="100%" border="0">
	<tr class="table_title_row">
	  <td class="table_title_row">SCRIPT</td>
	</tr>
	<tr>
	  <td height='15' width='15%' valign='top'>
	    <span style='color:#666666; FONT-SIZE: 12px;'>{$SCRIPT}</span>
	  </td>
	</tr>
      </table>
    </td>
<!-- Fin de script -->
<!-- Formulario a continuación -->
    <td valign='top' width=45%>
      <table cellpadding="2" cellspacing="0" width="100%" border="0" id="{$campo.ID_FORM}">
	<tr class="table_title_row">
	  <td class="table_title_row" colspan="2">FORMULARIO</td>
	</tr>
	<tr class="table_data">
	  <td class="table_data" height='15' width='40%' valign='top'>
		<b>CAMPAÑA: {$NOMBRE_CAMPANIA}</b>
	  </td>
	</tr>
        <tr class="table_data">
	  <td class="table_data" height='15' width='40%' valign='top'>
		<b>BASE: {$NOMBRE_BASE}</b>
	  </td>
	</tr>
	<tr class="table_data">
	  <td class="table_data" height='15' width='40%' valign='top'>
	    {$CALLTYPE_LABEL}
	  </td>
	  <td class="table_data" height='15' width='60%'>
	    {$CALLTYPE_INPUT}
	    <a href=modules/hispana_interfaz_agente/calltypes_info.php?id_campania={$ID_CAMPANIA} 
target="_blank" onClick="window.open(this.href, this.target, 'width=600,height=600,scrollbars=1'); return false;"><b>[?]</b></a>
	  </td>
	</tr>
      </table>
{foreach key=indice item=campo from=$FORMULARIO}
      <table cellpadding="2" cellspacing="0" width="100%" border="0" id="{$campo.ID_FORM}">
  {if $campo.TYPE eq 'LABEL'}
	<tr>
	  <td class="table_data" height='15' colspan='2' width='100%'><i>{$campo.INPUT} {$campo.ID_FORM}</i></td>
	</tr>
  {else} 
    {if $campo.TYPE eq 'DATE'}
	<tr>
	  <td class="table_data" height='15' width='40%'>
	  {$campo.TAG}
	  </td>
	  <td class="table_data" height='15' width="60%">
	  {$campo.INPUT}{$campo.ID_FIELD}
	  </td>
	</tr>
    {else}
	<tr>
	  <td class="table_data" height='15' width='40%'>{$campo.TAG}</td>
	  <td class="table_data" height='15' width='60%'>{$campo.INPUT} {$campo.ID_FIELD}</td>
	</tr>
    {/if}
  {/if}
{/foreach}

	<tr>
	  <td class="table_data" height='15' width='40%' valign="top" align="left"><b>OBSERVACIÓN</b></td>
	  <td class="table_data" height='15' width='60%'><textarea name="observacion" value="" rows="6" cols="20">{$OBSERVACION}</textarea></td>
	</tr>
      </table>
<!-- Agendamiento -->
      <table cellpadding="2" cellspacing="0" width="100%" border="0">
	<tr>
	  <td class="table_data" height='15' colspan="2"><b>Agendamiento</b></td>
	</tr>
	<tr>
	  <td class="table_data" height='15' width='15%'>Fecha: </td>
	  <td class="table_data" height='15' width='85%'>{$CALENDARIO}</td>
	</tr>
	<tr>
	  <td class="table_data" height='15' width='15%'>Hora: </td>
	  <td class="table_data" height='15' width='85%'>{$SELECT_HORAS}</td>
	</tr>
	<tr>
	  <td class="table_data" height='15' width='15%'><input type="radio" name="agente_agendado" value="{$ELASTIX_USER}" checked></td>
	  <td class="table_data" height='15' width='85%'>A mi usuario.</td>
	</tr>
	<tr>
	  <td class="table_data" height='15' width='15%'><input type="radio" name="agente_agendado" value="CAMPAÑA"></td>
	  <td class="table_data" height='15' width='85%'>A la campaña.</td>
	</tr>
      </table>
    </td>
  </tr>
</table>
{$HIDDEN_INPUT}
