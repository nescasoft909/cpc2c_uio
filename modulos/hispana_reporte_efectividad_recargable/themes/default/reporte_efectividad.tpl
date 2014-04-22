<table width="100%" border="0" cellspacing="0" cellpadding="0" class="filterForm">
  <tr class="table_title_row" align="center">
    <td class="table_title_row" align="center" colspan=2>{$TITULO}</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Campaña</b></td>
    <td class="table_data" align="left"><font size=2>{$CAMPANIA}</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Bases</b></td>
    <td class="table_data" align="left">
	<form method=post>
	  <select name="base">{html_options options=$BASE_OPTIONS selected=$BASE_ID}</select>
	  <input type="hidden" name="action" value="efectividad">
	  <button type="submit" name="submitButton" value="actualizar">Actualizar</button>
	</form>
    </td>
  </tr>  
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Registros Cargados</b></td>
    <td class="table_data" align="left"><font size=2>{$CONTACTOS_CARGADOS}</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Registros Barridos</b></td>
    <td class="table_data" align="left"><font size=2>{$CONTACTOS_BARRIDOS} ({$PORCENTAJE_BARRIDOS}%)</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Registros No Barridos</b></td>
    <td class="table_data" align="left"><font size=2>{$CONTACTOS_NO_BARRIDOS} ({$PORCENTAJE_NO_BARRIDOS}%)</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Contactados</b></td>
    <td class="table_data" align="left"><font size=2>{$CONTACTADOS}</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>No Contactados</b></td>
    <td class="table_data" align="left"><font size=2>{$NO_CONTACTADOS}</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Penetración</b></td>
    <td class="table_data" align="left"><font size=2>{$PORCENTAJE_PENETRACION}%</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Conversión</b></td>
    <td class="table_data" align="left"><font size=2>{$CONVERTIDOS} ({$PORCENTAJE_CONVERSION}%)</td>
  </tr>
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%"><font size=2><b>Mejor Calltype</b></td>
    <td class="table_data" align="left"><font size=2>{$MEJOR_CALLTYPE} (<b>Peso: {$PESO_MEJOR_CALLTYPE}</b>)</td>
  </tr>

</table>
<!--
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
  <tr class="table_title_row">
    <td class="table_title_row" width="100%" align="center">REGESTIONES</td>
  </tr>
</table>
-->