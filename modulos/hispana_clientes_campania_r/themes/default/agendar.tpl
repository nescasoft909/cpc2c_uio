<table width="100%" border="0" cellspacing="0" cellpadding="0" class="filterForm">
  <tr class="table_title_row" align="center">
    <td class="table_title_row" align="center" colspan="4">{$TITULO}</td>
  </tr>
  <tr>
  <td colspan="2">
<table width="100%" border="0" cellspacing="0" cellpadding="0"> <!-- class="filterForm"> -->
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>Cliente</b></td>
    <td align="left"><font size=2>{$CLIENTE}</td>
  </tr>
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>CI</b></td>
    <td align="left"><font size=2>{$ci}</td>
  </tr>
<form method="post" action="index.php?menu=hispana_clientes_campania_r">
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>Campaña</b></td>
    <td align="left"><font size=2>{$CAMPANIA}</td>
  </tr>
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>Agentes</b></td>
    <td align="left">
      <select name="agente" id="select_agentes">
	{foreach from=$AGENTES item=agente}    
	<option value={$agente}>{$agente}</option>
	{/foreach}
	<option value="CAMPAÑA">CAMPAÑA</option>
      </select>
    </td>
  </tr>  
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>Fecha</b></td>
    <td align="left"><font size=2>{$CALENDARIO}</td>
  </tr>
  <tr width="100%" align="center">
    <td align="left" width="25%"><font size=2><b>Hora</b></td>
    <td align="left"><font size=2>{$SELECT_HORAS}
    <input type="hidden" name="action" value="actualizar">
    <input type="hidden" name="id_campania_cliente" value={$id_campania_cliente}>    
    </td>
  </tr>
  <tr>
    <td></td>
    <td><button type="submit" name="submitButton" value="actualizar">Agregar</button></td>
  </tr>
      </form>
      </table>
    </td>
  </tr>
<!--
  {if isset($arrAgendamientos)}
  <tr class="table_title_row" align="center">
    <td class="table_title_row" align="center" colspan="4"><b>Agendamientos</td>
  </tr>
  <tr class="table_title_row" width="100%" align="center">
    <td class="table_title_row" align="left" width="25%">Campaña</td>    
    <td class="table_title_row" align="left" width="25%">Fecha</td>
    <td class="table_title_row" align="left" width="25%">Hora</td>
    <td class="table_title_row" align="left" width="25%">Agente</td>
  </tr>
  {foreach from=$arrAgendamientos item=agendamiento }    
  <tr class="table_data" width="100%" align="center">
    <td class="table_data" align="left" width="25%">{$agendamiento.campania}</td>    
    <td class="table_data" align="left" width="25%">{$agendamiento.fecha}</td>
    <td class="table_data" align="left" width="25%">{$agendamiento.hora}</td>
    <td class="table_data" align="left" width="25%">{$agendamiento.agente}</td>
  </tr>

  {/foreach}

  {/if}
-->
</table>
<!--
{literal}
  <script>
    function selectCampania(){
      var opcion = $('#select_campanias option:selected').val();
      if (opcion == "0") {
	  // alert('Error.');
      }
      $.ajax({
	type: 'POST',
	url: "modules/hispana_clientes_manuales/serverside.php",
	data: {clase: opcion, action: "mostrarAgentes"},
	success: function(data) {
	  $('#select_agentes').html(data);
	},
	dataType: "html"
      });
    } // fin de función
  </script>
{/literal}
-->