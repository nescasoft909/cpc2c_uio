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
    {if $id_campania}
    <tr>
        <td><a href='?menu=hispana_reporte_calltype&id_campania={$id_campania}&filter_field={$filter_field_url}&filter_value={$filter_value_url}&action=generaOffline'>Generar Reporte Offline</a></td>
    </tr>
    {/if}

</table>