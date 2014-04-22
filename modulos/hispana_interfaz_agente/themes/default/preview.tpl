<form action=index.php?menu=hispana_interfaz_agente method=post>
  <input type=submit value=Guardar>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
	<td>{$formulario}</td> <!-- formulario completo hasta con calltype -->
      </tr>
    </table>
</form>
{if $tieneGestionesPrevias == "si"}
<table class="table_data" style="font-size: 16px;" width="100%" border="1" cellspacing="0">
    <tr class="table_title_row">
	<td width=100% colspan=7>Gestiones previas</td> 
    </tr>
    <tr class="table_title_row" >
	<td>Fecha y Hora</td> 
	<td>Campaña</td> 
        <td>Base</td> 
	<td>Agente</td> 
	<td>Calltype</td> 
	<td>Observación</td> 
	<td>Número</td> 
    </tr>
    {foreach from=$arrGestionesPrevias key=i item=dataGestiones name=filas_gestiones}
    <tr class="table_data">
	<td class="table_data">{$dataGestiones.fecha}</td>
	<td class="table_data">{$dataGestiones.nombre_campania}</td>
        <td class="table_data">{$dataGestiones.base}</td>
	<td class="table_data">{$dataGestiones.agente}</td>
	<td class="table_data">{$dataGestiones.calltype}</td>
	<td class="table_data">{$dataGestiones.observacion}</td>
	<td class="table_data">{$dataGestiones.telefono}</td>
    </tr>
    {/foreach}
</table>
{/if}