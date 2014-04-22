<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr class="letra12">
        {if $mode eq 'input'}
        <td align="left">
            <input class="button" type="submit" name="save_new" value="{$SAVE}">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {elseif $mode eq 'view'}
        <td align="left">
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {elseif $mode eq 'edit'}
        <td align="left">
            <input class="button" type="submit" name="save_edit" value="Actualizar">&nbsp;&nbsp;
	    {if $esAdmin}
            <input class="button" type="submit" name="new" value="Ingresar otro">&nbsp;&nbsp;
	    {/if}
        </td>
        {/if}
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="100%" >
    {if !isset($smarty.session.ci)}
    <tr class="letra12">
        <td align="left" width="25%"><b>{$ci_input.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$ci_input.INPUT}</td>
    </tr>
    {/if}
    {if isset($action_edit)}
    <tr class="letra12">
        <td align="left" width="25%"><b>{$cedula.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$cedula.INPUT}</td>
    </tr>
    {/if}
    <tr class="letra12">
        <td align="left" width="25%"><b>{$nombre.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$nombre.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$apellido.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$apellido.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$provincia.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$provincia.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$ciudad.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$ciudad.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$nacimiento.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$nacimiento.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$correo_personal.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$correo_personal.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left" width="25%"><b>{$correo_trabajo.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$correo_trabajo.INPUT}</td>
    </tr>
    <tr class="letra12" width="25%">
        <td align="left"><b>{$estado_civil.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$estado_civil.INPUT}</td>
    </tr>
    <tr class="letra12" width="25%">
        <td align="left"><b>{$origen.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$origen.INPUT}</td>
    </tr>

</table>
<input class="button" type="hidden" name="id" value="{$ID}" />
<input class="button" type="hidden" name="ci" value="{$CI}" />
<input type="hidden" name="id_campania_cliente_recargable" value="{$smarty.session.id_campania_cliente_recargable}">
<input type="hidden" name="id_cliente" value="{$smarty.session.id_cliente}">