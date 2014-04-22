<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr class="letra12">
        <td width="10%" align="left">
	CAMPAÑA:&nbsp;&nbsp;{$filter_campaign}
	</td>
        <td width="10%" align="right">
            {$filter_field.LABEL}:&nbsp;&nbsp;{$filter_field.INPUT}&nbsp;&nbsp;{$filter_value.INPUT}
            <input class="button" type="submit" name="show" value="{$SHOW}" />
        </td>
    </tr>
    {if $mostrarAdicional=="si"}
    <tr class="letra12">
        <td width="10%" align="left">
	</td>
        <td width="10%" align="right">
            Filtro Adicionales:&nbsp;&nbsp;{$filtro_adicionales} <input type="text" name="filtro_adicional">
	</td>
    </tr>
    {/if}
</table>