<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
    <!-- <tr class="moduleTitle">
        <td class="moduleTitle" valign="middle" colspan='2'>&nbsp;&nbsp;<img src="{$IMG}" border="0" align="absmiddle">&nbsp;&nbsp;{$title}</td>
    </tr> -->
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
            <input class="button" type="submit" name="save_edit" value="{$SAVE}" onclick="return enviar_datos();">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {/if}
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="80%" border=0>
<!--
    <tr class="letra12">
        <td align="left" width="20%" colspan="4">Regestión para Campaña {$CAMPANIA_PADRE}</td>
        <!-- <td align="left" colspan="3">{$nombre.INPUT}</td>
    </tr>
-->
    <tr class="letra12">
        <td align="left" width="20%"><b>Campaña padre: <span  class="required"></span></b></td>
        <td align="left" colspan="3">{$CAMPANIA_PADRE}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$nombre.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$nombre.INPUT}</td>
    </tr>
<!--
    <tr class="letra12">
        <td align="left" width="20%"><b>{$fecha_inicio.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$fecha_inicio.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$fecha_fin.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$fecha_fin.INPUT}</td>
    </tr>
No es necesario escoger form aqui...
    <tr class="letra12">
        <td align="left" width="20%"><b>{$id_form.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$id_form.INPUT}</td>
    </tr>
    <tr class="letra12" align="left">
	<td align="left" ><b>{$formulario.LABEL}</b></td>
	<td align="left" width=200>{$formulario.INPUT}</td>
	<td align="left">
	    <input type='button' name='agregar_formulario' value="&gt;&gt;" onclick='add_form()'/><br>
	    <input type='button' name='quitar_formulario' value="&lt;&lt;" onclick='drop_form()'/>
	</td>
	<td align="left">{$formularios_elegidos.INPUT}</td>
    </tr>
-->
    <tr class="letra12">
        <td align="left"><b>{$base.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" width=200>{$base.INPUT}</td>
	<td align="left">
	    <input type='button' name='agregar_base' value="&gt;&gt;" onclick='add_base()'/><br>
	    <input type='button' name='quitar_base' value="&lt;&lt;" onclick='drop_base()'/>
	</td>
	<td align="left">{$bases_elegidas.INPUT}</td>
    </tr>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$agente.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" width=200>{$agente.INPUT}</td>
	<td align="left">
	    <input type='button' name='agregar_agente' value="&gt;&gt;" onclick='add_agente()'/><br>
	    <input type='button' name='quitar_agente' value="&lt;&lt;" onclick='drop_agente()'/>
	</td>
	<td align="left">{$agentes_elegidos.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$calltypes.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" width=200>{$calltypes.INPUT}</td>
	<td align="left">
	    <input type='button' name='agregar_calltype' value="&gt;&gt;" onclick='add_calltype()'/><br>
	    <input type='button' name='quitar_calltype' value="&lt;&lt;" onclick='drop_calltype()'/>
	</td>
	<td align="left">{$calltypes_elegidos.INPUT}</td>
    </tr>

<!--
No se edita el script.
    <tr class="letra12">
        <td align="left"><b>{$script.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$script.INPUT}</td>
    </tr>
-->
</table>
<input type="hidden" name="id" value="{$ID}" />
<input type="hidden" name="values_agentes" id='values_agentes' value="" />   
<input type="hidden" name="values_bases" id='values_bases' value="" />
<input type="hidden" name="values_calltypes" id='values_calltypes' value="" /> 
<input type="hidden" name="fecha_inicio" id='fecha_inicio' value="{$fecha_inicio}" /> 
<input type="hidden" name="fecha_fin" id='fecha_fin' value="{$fecha_fin}" /> 
<input type="hidden" name="id_form" id='id_form' value="{$id_form}" /> 
<input type="hidden" name="script" id='script' value="{$script}" /> 

{literal}
<script type="text/javascript">
function desactivar_campania()
{
    var id_campaign = document.getElementById("id_campaign").value;
    xajax_desactivar_campania(id_campaign);
}

function delete_campania()
{
}

/* Función para recoger todas las variables del formulario y procesarlas. Sólo
   se requiere atención especial para el RTF del script, y para la lista de 
   formularios elegidos. */
function enviar_datos()
{   
    /*
    var lc = listaControlesFormularios();
    var select_form = lc[1]; // Formularios elegidos
    var values = "";
    
    for(var i=0; i < select_form.length; i++) {
        values = values + select_form[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_form").value = values;
    */

    // Agentes
    lc = listaControlesAgentes();
    select_agente = lc[1]; /* Agentes elegidos */
    values = "";
    
    for(var i=0; i < select_agente.length; i++) {
        values = values + select_agente[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_agentes").value = values;

    // Bases
    lc = listaControlesBases();
    select_base = lc[1]; /* Bases elegidas */
    values = "";
    
    for(var i=0; i < select_base.length; i++) {
        values = values + select_base[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_bases").value = values;

    // Calltypes
    lc = listaControlesCalltypes();
    select_calltypes = lc[1]; /* Calltypes elegidos */
    values = "";
    
    for(var i=0; i < select_calltypes.length; i++) {
        values = values + select_calltypes[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_calltypes").value = values;

    updateRTEs();
    return true;
}

// Add & drop Bases
function add_base()
{
	var lc = listaControlesBases();
	var select_bases = lc[0];
	var select_bases_elegidas = lc[1];

    for(var i=0;i<select_bases.length;i++){
        if(select_bases[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_bases[i].value;
            option_tmp.appendChild(document.createTextNode(select_bases[i].firstChild.data));
            select_bases_elegidas.appendChild(option_tmp);
        }
    }

    for(var i=select_bases.length-1;i>=0;i--){
        if(select_bases[i].selected){
            select_bases.removeChild(select_bases[i]);
        }
    }
}

function drop_base()
{
	var lc = listaControlesBases();
	var select_bases = lc[0];
	var select_bases_elegidas = lc[1];

    for(var i=0;i<select_bases_elegidas.length;i++){
        if(select_bases_elegidas[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_bases_elegidas[i].value;
            option_tmp.appendChild(document.createTextNode(select_bases_elegidas[i].firstChild.data));
            select_bases.appendChild(option_tmp);
        }
    }

    for(var i=select_bases_elegidas.length-1;i>=0;i--){
        if(select_bases_elegidas[i].selected){
            select_bases_elegidas.removeChild(select_bases_elegidas[i]);
        }
    }
}

// Add & drop Agentes
function add_agente()
{
	var lc = listaControlesAgentes();
	var select_agentes = lc[0];
	var select_agentes_elegidos = lc[1];

    for(var i=0;i<select_agentes.length;i++){
        if(select_agentes[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_agentes[i].value;
            option_tmp.appendChild(document.createTextNode(select_agentes[i].firstChild.data));
            select_agentes_elegidos.appendChild(option_tmp);
        }
    }

    for(var i=select_agentes.length-1;i>=0;i--){
        if(select_agentes[i].selected){
	    select_agentes.removeChild(select_agentes[i]);
        }
    }
}

function drop_agente()
{
	var lc = listaControlesAgentes();
	var select_agentes = lc[0];
	var select_agentes_elegidos = lc[1];

    for(var i=0;i<select_agentes_elegidos.length;i++){
        if(select_agentes_elegidos[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_agentes_elegidos[i].value;
            option_tmp.appendChild(document.createTextNode(select_agentes_elegidos[i].firstChild.data));
            select_agentes.appendChild(option_tmp);
        }
    }

    for(var i=select_agentes_elegidos.length-1;i>=0;i--){
        if(select_agentes_elegidos[i].selected){
            select_agentes_elegidos.removeChild(select_agentes_elegidos[i]);
        }
    }
}

// Add & drop Calltypes
function add_calltype()
{
	var lc = listaControlesCalltypes();
	var select_calltypes = lc[0];
	var select_calltypes_elegidos = lc[1];

    for(var i=0;i<select_calltypes.length;i++){
        if(select_calltypes[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_calltypes[i].value;
            option_tmp.appendChild(document.createTextNode(select_calltypes[i].firstChild.data));
            select_calltypes_elegidos.appendChild(option_tmp);
        }
    }

    for(var i=select_calltypes.length-1;i>=0;i--){
        if(select_calltypes[i].selected){
	    select_calltypes.removeChild(select_calltypes[i]);
        }
    }
}

function drop_calltype()
{
	var lc = listaControlesCalltypes();
	var select_calltypes = lc[0];
	var select_calltypes_elegidos = lc[1];

    for(var i=0;i<select_calltypes_elegidos.length;i++){
        if(select_calltypes_elegidos[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_calltypes_elegidos[i].value;
            option_tmp.appendChild(document.createTextNode(select_calltypes_elegidos[i].firstChild.data));
            select_calltypes.appendChild(option_tmp);
        }
    }

    for(var i=select_calltypes_elegidos.length-1;i>=0;i--){
        if(select_calltypes_elegidos[i].selected){
            select_calltypes_elegidos.removeChild(select_calltypes_elegidos[i]);
        }
    }
}
/* Esta función es necesaria para lidiar con el cambio en los nombres de los 
   controles generados por Elastix entre 1.6-12 y 1.6.2-1 */


function listaControlesAgentes()
{
	var listaControles;
	var select_agentes;
	var select_agentes_elegidos;
	
	listaControles = document.getElementsByName('agente');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('agente[]');
	select_agentes = listaControles[0];
    
	listaControles = document.getElementsByName('agentes_elegidos');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('agentes_elegidos[]');
	select_agentes_elegidos = listaControles[0];

	var lista = new Array();
	lista[0] = select_agentes;
	lista[1] = select_agentes_elegidos;
	return lista;
}

function listaControlesBases()
{
	var listaControles;
	var select_bases;
	var select_bases_elegidas;
	
	listaControles = document.getElementsByName('base');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('base[]');
	select_bases = listaControles[0];
    
	listaControles = document.getElementsByName('bases_elegidas');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('bases_elegidas[]');
	select_bases_elegidas = listaControles[0];

	var lista = new Array();
	lista[0] = select_bases;
	lista[1] = select_bases_elegidas;
	return lista;
}

function listaControlesCalltypes()
{
	var listaControles;
	var select_calltypes;
	var select_calltypes_elegidos;
	
	listaControles = document.getElementsByName('calltypes');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('calltypes[]');
	select_calltypes = listaControles[0];
    
	listaControles = document.getElementsByName('calltypes_elegidos');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('calltypes_elegidos[]');
	select_calltypes_elegidos = listaControles[0];

	var lista = new Array();
	lista[0] = select_calltypes;
	lista[1] = select_calltypes_elegidos;
	return lista;
}



</script>
{/literal}
{$xajax_javascript}
