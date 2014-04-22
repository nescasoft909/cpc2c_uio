<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr class="moduleTitle">
<!--
        <td class="moduleTitle" valign="middle" colspan='2'>&nbsp;&nbsp;<img src="{$IMG}" border="0" align="absmiddle">&nbsp;&nbsp;{$title}</td>
-->
    </tr>
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
            <input class="button" type="submit" name="save_edit" value="{$EDIT}">&nbsp;&nbsp;
            <input class="button" type="submit" name="cancel" value="{$CANCEL}">
        </td>
        {/if}
        <td align="right" nowrap><span class="letra12"><span  class="required">*</span> {$REQUIRED_FIELD}</span></td>
    </tr>
</table>
<table class="tabForm" style="font-size: 16px;" width="100%" >
    <tr class="letra12">
        <td align="left"><b>{$nombre_base.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$nombre_base.INPUT}</td>
    </tr>
    {if $view_edit=="si"}{else}
    <tr class="letra12">
        <td align="left"><b>{$campania.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$campania.INPUT}</td>
    </tr>
    {/if}
    {if $view_edit=="si"}{else}
    <tr class="letra12">
        <td align="left"><b>{$archivo_de_clientes.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$archivo_de_clientes.INPUT}</td>
    </tr>
    {/if}
    <tr class="letra12">
        <td align="left"><b>{$fecha_inicio.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$fecha_inicio.INPUT}</td>
    </tr>
    <tr class="letra12">
        <td align="left"><b>{$fecha_fin.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$fecha_fin.INPUT}</td>
    </tr>
    {if $view_edit=='si'}
        <input class="button" type="hidden" name="save_edit" value="si" />
        <input class="button" type="hidden" name="id_campania" value="{$id_campania}" />
        <input class="button" type="hidden" name="id_base" value="{$id_base}" />
    {/if}

</table>
<input class="button" type="hidden" name="id" value="{$ID}" />