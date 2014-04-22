<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
<!--
    <tr class="moduleTitle">
        <td class="moduleTitle" valign="middle" colspan='2'>&nbsp;&nbsp;<img src="{$IMG}" border="0" align="absmiddle">&nbsp;&nbsp;{$title}</td>
    </tr>
-->
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
    <tr class="letra12" width="25%">
        <td align="left"><b>{$campania.LABEL}: <span  class="required">*</span></b></td>
	{if $action == "view_edit"}
        <td align="left">{$info.campania}</td>
	{else}
	<td align="left">{$campania.INPUT}</td>
	{/if}
    </tr>
    {if $action != "view_edit"}
    <tr class="letra12" width="25%">
        <td align="left"><b>{$nuevo_calltype.LABEL}: <span  class="required">*</span></b></td>
        <td align="left"><input type="checkbox" name="nuevo_calltype" id="nuevo_calltype" onclick="nuevo_calltypeDisable();"></td>
    </tr>
     <tr class="letra12" width="25%">
        <td align="left"><b>{$calltypes.LABEL}: <span  class="required">*</span></b></td>
	<td align="left">{$calltypes.INPUT}</td>
    </tr>
    {/if}
    <tr class="letra12" width="25%">
        <td align="left"><b>{$clase.LABEL}: <span  class="required">*</span></b></td>
	{if $action == "view_edit"}
        <td align="left">{$info.clase}</td>
	{else}
	<td align="left">{$clase.INPUT}</td>
	{/if}
    </tr>
    <tr class="letra12" width="25%">
        <td align="left"><b>{$descripcion.LABEL}: <span  class="required">*</span></b></td>
	{if $action == "view_edit"}
        <td align="left">{$info.descripcion}</td>
	{else}
	<td align="left">{$descripcion.INPUT}</td>
	{/if}
    </tr>
    <tr class="letra12" width="25%">
        <td align="left"><b>{$peso.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$peso.INPUT}</td>
    </tr>
    <tr class="letra12" width="25%">
        <td align="left"><b>{$definicion.LABEL}: <span  class="required">*</span></b></td>
        <td align="left">{$definicion.INPUT}</td>
    </tr>
</table>
<input class="button" type="hidden" name="id" value="{$ID}" />
<input class="button" type="hidden" name="id_campania" value="{$ID_CAMPANIA}" />

