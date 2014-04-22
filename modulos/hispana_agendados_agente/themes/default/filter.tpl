<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr class="letra12">
        <td width="10%" align="left">&nbsp;&nbsp;</td>
        <td width="10%" align="right">
            {$filter_field.LABEL}:&nbsp;&nbsp;{$filter_field.INPUT}&nbsp;&nbsp;{$filter_value.INPUT}
            <input class="button" type="submit" name="show" value="{$SHOW}" />
            <br>
            {if $aditional_key.LABEL}
            {$aditional_key.LABEL}:&nbsp;&nbsp;{$aditional_key.INPUT}&nbsp;&nbsp;{$aditional_value.INPUT}
            <br>
            {/if}
            {$mostrar_adicionales.LABEL}:&nbsp;&nbsp;{$mostrar_adicionales.INPUT}
            <br><br><br>
            {$calltype_list.LABEL}:&nbsp;&nbsp;{$calltype_list.INPUT}
        </td>
    </tr>
</table>