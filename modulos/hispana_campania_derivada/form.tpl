<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr class="letra12">
        {if $mode eq 'input'}
        <td align="left">
            <input class="button" type="submit" name="save_new" value="{$SAVE}" onclick="return enviar_datos();">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {elseif $mode eq 'view'}
        <td align="left">
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {elseif $mode eq 'edit'}
        <td align="left">
            <input class="button" type="submit" name="save_edit" value="{$EDIT}" onclick="return enviar_datos();">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {/if}
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="100%" >
    <tr class="letra12">        
        <td align="left" width="15%"><b>Campaña nueva</b>
	</td>	
        <td align="left" width="65%" colspan="3">{$nombre_campania.INPUT}
	</td>
    </tr>
    <tr class="letra12">
	<td align="left" width="25%"><font size=2><b>Campaña a derivar:</b></td>
	<td align="left" colspan="3">
	  <select name="campania" id="select_campania" onchange="selectCampania();">
	    <option value=0>-- {$campania.LABEL} --</option>
	      {html_options options=$CAMPANIA_OPTIONS selected=$QUEUE_ID}
	  </select>
	</td>    
    </tr>
    <tr class="letra12">
        <td align="left" valign="top"><b>Con clientes de estas bases:</b></td>
        <td align="left" width="15%">{$active_bases.LABEL}<br><br>
	    <select id="active_bases" name="active_bases" multiple="multiple" size="8"></select>
	</td>
	<td width="10%">
	    <input type='button' name='button_add_base' value="&gt;&gt;" onclick='add_base()'/><br>
	    <input type='button' name='button_drop_base' value="&lt;&lt;" onclick='drop_base()'/>
	</td>
        <td align="left" width="65%">{$inactive_bases.LABEL}<br><br>
	    <select id="inactive_bases" name="inactive_bases" multiple="multiple" size="8"></select>
	</td>
    </tr>

    <tr class="letra12">
        <td align="left" valign="top"><b>Cuyo mejor calltype sea:</b></td>
        <td align="left" width="15%">{$active_calltypes.LABEL}<br><br>
	    <select id="active_calltypes" name="active_calltypes" multiple="multiple" size="8"></select>
	</td>
	<td width="10%">
	    <input type='button' name='button_add_calltype' value="&gt;&gt;" onclick='add_calltype()'/><br>
	    <input type='button' name='button_drop_calltype' value="&lt;&lt;" onclick='drop_calltype()'/>
	</td>
        <td align="left" width="65%">{$inactive_calltypes.LABEL}<br><br>
	    <select id="inactive_calltypes" name="inactive_calltypes" multiple="multiple" size="8"></select>
	</td>
    </tr>
    <tr class="letra12">
        <td align="left" valign="top">
	    <input class="button" type="submit" name="calcular_clientes" value="Calcular clientes" onclick="return calcular_data();">
	</td>
        <td align="left" width="15%"><div id="div_data" name="div_data"></div></td>
	<td width="10%">
	    
	</td>
        <td align="left" width="65%"></td>
    </tr>


</table>
<input class="button" type="hidden" name="id" value="{$ID}" />
<input type="hidden" name="values_bases" id='values_bases' value="" />
<input type="hidden" name="values_inactive_bases" id='values_inactive_bases' value="" />
<input type="hidden" name="values_calltypes" id='values_calltypes' value="" />
<input type="hidden" name="values_inactive_calltypes" id='values_inactive_calltypes' value="" />

{literal}
  <script>
    function selectCampania(){
      var campania = $('#select_campania option:selected').val();
      if (campania == "0") {
	  alert('Error.');
      }
      // Se puede resumir en un sólo ajax.
      $.ajax({
	type: 'POST',
	url: 'modules/hispana_campania_derivada/ajax_process.php',
	data: {campania: campania, action: 'getBases'},
	dataType: "html",
	success: function(data) {
	  $('#active_bases').html(data);
	}
      });
      
      $.ajax({
	type: 'POST',
	url: 'modules/hispana_campania_derivada/ajax_process.php',
	data: {campania: campania, action: 'getCalltypes'},
	dataType: "html",
	success: function(data) {
	  $('#active_calltypes').html(data);
	}
      });
    }

function enviar_datos()
{    
    lc = listaControlesBases();
    inactive_bases = lc[0];
    active_bases = lc[1];    

    lc2 = listaControlesCalltypes();
    inactive_calltypes = lc2[0];
    active_calltypes  = lc2[1];   

    values_bases = "";
    values_inactive_bases = "";
    values_calltypes = "";
    values_inactive_calltypes = "";  
    

    for(var i=0; i < active_bases.length; i++) {
        values_bases = values_bases + active_bases[i].value + ",";
    }

    for(var i=0; i < inactive_bases.length; i++) {
        values_inactive_bases = values_inactive_bases + inactive_bases[i].value + ",";
    }

        
    for(var i=0; i < active_calltypes.length; i++) {
        values_calltypes = values_calltypes + active_calltypes[i].value + ",";
    }

    for(var i=0; i < inactive_calltypes.length; i++) {
        values_inactive_calltypes = values_inactive_calltypes + inactive_calltypes[i].value + ",";
    }

    if(values_bases != "")
        values_bases = values_bases.substring(0,values_bases.length-1);
    document.getElementById("values_bases").value = values_bases;

    if(values_inactive_bases != "")
        values_inactive_bases = values_inactive_bases.substring(0,values_inactive_bases.length-1);
    document.getElementById("values_inactive_bases").value = values_inactive_bases;

  

    if(values_calltypes != "")
        values_calltypes = values_calltypes.substring(0,values_calltypes.length-1);
    document.getElementById("values_calltypes").value = values_calltypes;

    if(values_inactive_calltypes != "")
        values_inactive_calltypes = values_inactive_calltypes.substring(0,values_inactive_calltypes.length-1);
    document.getElementById("values_inactive_calltypes").value = values_inactive_calltypes;
    
    return true;
}


function calcular_data()
{    
    lc = listaControlesBases();
    inactive_bases = lc[0];
    active_bases = lc[1];    

    lc2 = listaControlesCalltypes();
    inactive_calltypes = lc2[0];
    active_calltypes  = lc2[1];   

    values_bases = "";
    values_inactive_bases = "";
    values_calltypes = "";
    values_inactive_calltypes = "";  
    

    for(var i=0; i < active_bases.length; i++) {
        values_bases = values_bases + active_bases[i].value + ",";
    }

    for(var i=0; i < inactive_bases.length; i++) {
        values_inactive_bases = values_inactive_bases + inactive_bases[i].value + ",";
    }

        
    for(var i=0; i < active_calltypes.length; i++) {
        values_calltypes = values_calltypes + active_calltypes[i].value + ",";
    }

    for(var i=0; i < inactive_calltypes.length; i++) {
        values_inactive_calltypes = values_inactive_calltypes + inactive_calltypes[i].value + ",";
    }

    if(values_bases != "")
        values_bases = values_bases.substring(0,values_bases.length-1);
    document.getElementById("values_bases").value = values_bases;

    if(values_inactive_bases != "")
        values_inactive_bases = values_inactive_bases.substring(0,values_inactive_bases.length-1);
    document.getElementById("values_inactive_bases").value = values_inactive_bases;
  

    if(values_calltypes != "")
        values_calltypes = values_calltypes.substring(0,values_calltypes.length-1);
    document.getElementById("values_calltypes").value = values_calltypes;

    if(values_inactive_calltypes != "")
        values_inactive_calltypes = values_inactive_calltypes.substring(0,values_inactive_calltypes.length-1);
    document.getElementById("values_inactive_calltypes").value = values_inactive_calltypes;
    
    var campania = $('#select_campania option:selected').val();

    $.ajax({
	type: 'POST',
	url: 'modules/hispana_campania_derivada/ajax_process.php',
	data: {	campania: campania, 
		values_bases: values_bases, 
		values_calltypes:  values_calltypes,
		action: 'getData'
	      },
	dataType: "html",
	success: function(data) {
	  $('#div_data').html(data);
	}
      });
      return false;
}


/* Bases */
/* Add */
function add_base()
{
	var lc = listaControlesBases();
	var inactive_bases = lc[0];
	var active_bases = lc[1];

    for(var i=0;i<active_bases.length;i++){
        if(active_bases[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = active_bases[i].value;
            option_tmp.appendChild(document.createTextNode(active_bases[i].firstChild.data));
            inactive_bases.appendChild(option_tmp);
        }
    }

    for(var i=active_bases.length-1;i>=0;i--){
        if(active_bases[i].selected){
            active_bases.removeChild(active_bases[i]);
        }
    }
}

/* Drop */
function drop_base()
{
    var lc = listaControlesBases();
    var inactive_bases = lc[0];
    var active_bases = lc[1];

    for(var i=0;i<inactive_bases.length;i++){
        if(inactive_bases[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = inactive_bases[i].value;
            option_tmp.appendChild(document.createTextNode(inactive_bases[i].firstChild.data));
            active_bases.appendChild(option_tmp);
        }
    }

    for(var i=inactive_bases.length-1;i>=0;i--){
        if(inactive_bases[i].selected){
            inactive_bases.removeChild(inactive_bases[i]);
        }
    }
}

/* Calltypes */
/* Add */
function add_calltype()
{
	var lc = listaControlesCalltypes();
	var inactive_calltypes = lc[0];
	var active_calltypes = lc[1];

    for(var i=0;i<active_calltypes.length;i++){
        if(active_calltypes[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = active_calltypes[i].value;
            option_tmp.appendChild(document.createTextNode(active_calltypes[i].firstChild.data));
            inactive_calltypes.appendChild(option_tmp);
        }
    }

    for(var i=active_calltypes.length-1;i>=0;i--){
        if(active_calltypes[i].selected){
            active_calltypes.removeChild(active_calltypes[i]);
        }
    }
}

/* Drop */
function drop_calltype()
{
    var lc = listaControlesCalltypes();
    var inactive_calltypes = lc[0];
    var active_calltypes = lc[1];

    for(var i=0;i<inactive_calltypes.length;i++){
        if(inactive_calltypes[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = inactive_calltypes[i].value;
            option_tmp.appendChild(document.createTextNode(inactive_calltypes[i].firstChild.data));
            active_calltypes.appendChild(option_tmp);
        }
    }

    for(var i=inactive_calltypes.length-1;i>=0;i--){
        if(inactive_calltypes[i].selected){
            inactive_calltypes.removeChild(inactive_calltypes[i]);
        }
    }
}

function listaControlesBases()
{
    var listaControles;
    var inactive_bases;
    var active_bases;
	
    listaControles = document.getElementsByName('inactive_bases');
    if (listaControles.length == 0)
	listaControles = document.getElementsByName('inactive_bases[]');
    inactive_bases = listaControles[0];
    
    listaControles = document.getElementsByName('active_bases');
    if (listaControles.length == 0)
	listaControles = document.getElementsByName('active_bases[]');
    active_bases = listaControles[0];

    var lista = new Array();
    lista[0] = inactive_bases;
    lista[1] = active_bases;
    return lista;
}

function listaControlesCalltypes()
{
    var listaControles;
    var inactive_calltypes;
    var active_calltypes;
	
    listaControles = document.getElementsByName('inactive_calltypes');
    if (listaControles.length == 0)
	listaControles = document.getElementsByName('inactive_calltypes[]');
    inactive_calltypes = listaControles[0];
    
    listaControles = document.getElementsByName('active_calltypes');
    if (listaControles.length == 0)
	listaControles = document.getElementsByName('active_calltypes[]');
    active_calltypes = listaControles[0];

    var lista = new Array();
    lista[0] = inactive_calltypes;
    lista[1] = active_calltypes;
    return lista;
}




  </script>
{/literal}