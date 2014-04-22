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
            <input class="button" type="submit" name="save_edit" value="{$EDIT}" onclick="return enviar_datos();">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {/if}
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="80%" border=0>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$nombre.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$nombre.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$fecha_inicio.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$fecha_inicio.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$fecha_fin.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$fecha_fin.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="20%"><b>{$id_form.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$id_form.INPUT}</td>
    </tr>
<!--
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
        <td align="left"><b>{$script.LABEL}: <span  class="required">*</span></b></td>
        <td align="left" colspan="3">{$script.INPUT}</td>
    </tr>
</table>

<input class="button" type="hidden" name="id" value="{$ID}" />
<!-- <input type="hidden" name="values_form" id='values_form' value="" />   -->
<input type="hidden" name="values_agentes" id='values_agentes' value="" />   
<input type="hidden" name="values_bases" id='values_bases' value="" />   
{literal}
<script type="text/javascript">
function desactivar_campania()
{
    var id_campaign = document.getElementById("id_campaign").value;
    xajax_desactivar_campania(id_campaign);
}

function delete_campania() {
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

    lc = listaControlesAgentes();
    select_agente = lc[1]; /* Agentes elegidos */
    values = "";
    
    for(var i=0; i < select_agente.length; i++) {
        values = values + select_agente[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_agentes").value = values;

    lc = listaControlesBases();
    select_base = lc[1]; /* Agentes elegidos */
    values = "";
    
    for(var i=0; i < select_base.length; i++) {
        values = values + select_base[i].value + ",";
    }
    if(values != "")
        values = values.substring(0,values.length-1);
    document.getElementById("values_bases").value = values;

    updateRTEs();
    return true;
}

/*
function add_form()
{
	var lc = listaControlesFormularios();
	var select_formularios = lc[0];
	var select_formularios_elegidos = lc[1];

    for(var i=0;i<select_formularios.length;i++){
        if(select_formularios[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_formularios[i].value;
            option_tmp.appendChild(document.createTextNode(select_formularios[i].firstChild.data));
            select_formularios_elegidos.appendChild(option_tmp);
        }
    }

    for(var i=select_formularios.length-1;i>=0;i--){
        if(select_formularios[i].selected){
            select_formularios.removeChild(select_formularios[i]);
        }
    }
}


function drop_form()
{
	var lc = listaControlesFormularios();
	var select_formularios = lc[0];
	var select_formularios_elegidos = lc[1];

    for(var i=0;i<select_formularios_elegidos.length;i++){
        if(select_formularios_elegidos[i].selected){
            var option_tmp = document.createElement("option");
            option_tmp.value = select_formularios_elegidos[i].value;
            option_tmp.appendChild(document.createTextNode(select_formularios_elegidos[i].firstChild.data));
            select_formularios.appendChild(option_tmp);
        }
    }

    for(var i=select_formularios_elegidos.length-1;i>=0;i--){
        if(select_formularios_elegidos[i].selected){
            select_formularios_elegidos.removeChild(select_formularios_elegidos[i]);
        }
    }
}
*/
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

/* Esta función es necesaria para lidiar con el cambio en los nombres de los 
   controles generados por Elastix entre 1.6-12 y 1.6.2-1 */
/*
function listaControlesFormularios()
{
	var listaControles;
	var select_formularios;
	var select_formularios_elegidos;
	
	listaControles = document.getElementsByName('formulario');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('formulario[]');
    select_formularios = listaControles[0];
    
	listaControles = document.getElementsByName('formularios_elegidos');
	if (listaControles.length == 0)
		listaControles = document.getElementsByName('formularios_elegidos[]');
    select_formularios_elegidos = listaControles[0];

	var lista = new Array();
	lista[0] = select_formularios;
	lista[1] = select_formularios_elegidos;
	return lista;
}
*/

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



</script>
{/literal}
{$xajax_javascript}
