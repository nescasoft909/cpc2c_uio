<!-- Inicio de tabla de telefonos -->
<table width="100%" border="1"  cellspacing="0" cellpadding="4" align="center">
    <tr class="table_title_row">
        <td class="table_title_row" colspan={$cantidadTelefonos+1}>Teléfonos</td>
	<td class="table_title_row" width="20%">Clientes Gestionados</td>
	<td class="table_title_row" width="20%">Clientes agendados</td>
	<td class="table_title_row" width="30%">Breaks</td>
    </tr>
    <tr>
{if $tieneTelefonos}
    <tr class="table_data">
	{foreach from=$arrInfoCliente.telefono key=i item=datatelefonos name=filas_telefonos}
	<td align="left" class="table_data" width="10%">
	{if $datatelefonos.marcado == "si"}
	<font color="#0000FF">
	{/if}
	  <b>{$datatelefonos.descripcion}:</b>
	    <a href=index.php?menu=hispana_interfaz_agente&ci={$arrInfoCliente.ci}&id_campania={$arrInfoCliente.id_campania}&id_campania_cliente={$arrInfoCliente.id_campania_cliente}&action=llamar&telefono={$datatelefonos.telefono}&id_campania_cliente_recargable={$arrInfoCliente.id_campania_cliente_recargable}>
	    {$datatelefonos.telefono}
	    </a>
	  </td>
	{if $datatelefonos.marcado == "si"}
	</font>
	{/if}


	    {/foreach}
      <td align="left" class="table_data" width="40%">&nbsp;</td>
      <td align="left" class="table_data">{$usuario_gestionados_hoy}</td>
      <td align="left" class="table_data">
	  En esta campaña: {$clientesAgendados.cont_campania}<br>
	  En total: {$clientesAgendados.cont_total}
      </td>
      <td align="left" class="table_data">
          {if $gestion_agendados=="gestionando"}
              Gestionando Agendados<br>
              <a href="index.php?menu=hispana_interfaz_agente&amp;action=sacarpausa">Salir de pausa</a>
          {else}
	  <form method=POST>
	    <select name=id_break id=id_break>
	    {html_options options=$LISTA_BREAKS}
	    </select>
	    <input type="submit" name='action' value="Pausar">
	  </form>
          {/if}
      </td>

    </tr>
{else}
    <tr class="letra12">
	<td align="left"><b>No existen teléfonos para este cliente.</td>
    </tr>
{/if}
    </tr>
</table> 
<!-- Fin de sección de teléfonos -->
<table class="table_data" style="font-size: 16px;" width="100%" border="0">
    <tr class="table_title_row" >
	<td width=100% colspan=2>Datos básicos</td> 
    </tr>
    {if $fecha_agendamiento != ""}
    <tr class="letra12">
	<td align="left" class="table_data" width=15%>
	    <b>Mensaje</b>
	</td>
	<td align="left" class="table_data">
	    <font color=#ff0000>LLAMADA AGENDADA para {$fecha_agendamiento}</font><br>
	</td>
    </tr>
    {/if}
    {if $siguiente=="si"}
    <tr class="letra12">
	<td align="left" class="table_data" width=15%>
	    <b>Mensaje</b>
	</td>
	<td align="left" class="table_data">
	  <a href=index.php?menu=hispana_interfaz_agente&siguiente=si><font color="#FF0000">Continuar con el siguiente cliente.</font></a>
	</td>
    </tr>
    {/if}

    <tr class="table_data">
	<td align="left" class="table_data" width=15%><b>Campaña:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.campania} 
	<b>{if $arrInfoCliente.tipo_campania == "REGESTION"} <b>(Regestión)</b> {/if}</b>
	</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Cédula:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.ci}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Nombre:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.nombre}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Apellido:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.apellido}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Provincia:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.provincia}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Ciudad:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.ciudad}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Nacimiento:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.nacimiento}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Correo personal:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.correo_personal}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Correo del trabajo:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.correo_trabajo}</td>
    </tr>
    <tr align="left" class="table_data">
	<td align="left" class="table_data"><b>Estado civil:</b></td>
	<td align="left" class="table_data">{$arrInfoCliente.estado_civil}</td>
    </tr>	
</table>

{if $tieneGestionesPrevias == "si"}
<table class="table_data" style="font-size: 16px;" width="100%" border="1" cellspacing="0">
    <tr class="table_title_row">
	<td width=100% colspan=6>Gestiones previas</td> 
    </tr>
    <tr class="table_title_row" >
	<td>Fecha y Hora</td> 
	<td>Campaña</td> 
	<td>Agente</td> 
	<td>Calltype</td> 
	<td>Observación</td> 
	<td>Número</td> 
    </tr>
    {foreach from=$arrGestionesPrevias key=i item=dataGestiones name=filas_gestiones}
    <tr class="table_data">
	<td class="table_data">{$dataGestiones.fecha}</td>
	<td class="table_data">{$dataGestiones.nombre_campania}</td>
	<td class="table_data">{$dataGestiones.agente}</td>
	<td class="table_data">{$dataGestiones.calltype}</td>
	<td class="table_data">{$dataGestiones.observacion}</td>
	<td class="table_data">{$dataGestiones.telefono}</td>
    </tr>
    {/foreach}
</table>
{/if}