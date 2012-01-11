<h2>Settings</h2>

<table id="settings">
    <thead>
        <th>Name</th>
        <th>Value</th>
    </thead>
    <tbody>
        {foreach from=$settings item=name}
            {assign var='value' value=$config->get($name)}
            {assign var='type' value=$config->type($name)}
            {assign var='id' value=str_replace('.', '-',$name)}
            
            {include file="fragments/admin-setting-row.tpl"}
        {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <input type="button" id="settings_save" name="save" value="Save" />
                <input type="button" id="settings_new" name="new_setting" value="New Setting" />
            </td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    rc.settings.init();
</script>