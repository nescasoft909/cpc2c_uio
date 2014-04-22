<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
    <!-- 
    <tr class="moduleTitle">
        <td class="moduleTitle" valign="middle" colspan='2'>&nbsp;&nbsp;<img src="{$IMG}" border="0" align="absmiddle">&nbsp;&nbsp;{$title}</td>
    </tr> 
    -->
    <tr class="letra12">
<!--
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
            <input class="button" type="submit" name="save_edit" value="{$EDIT}">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {/if}
-->
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="100%" >
    <tr class="letra12">
        <td align="left"><b>{$ci.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$ci.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$nombre.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$nombre.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$apellido.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$apellido.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$provincia.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$provincia.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$ciudad.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$ciudad.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$nacimiento.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$nacimiento.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$edad.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$edad.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$sexo.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$sexo.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$profesion.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$profesion.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$telefonos.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$telefonos.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$direcciones.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$direcciones.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$adicionales.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$adicionales.INPUT}</td>
    </tr>
</table>
<input class="button" type="hidden" name="id" value="{$ID}" />