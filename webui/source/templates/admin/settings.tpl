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
            <tr>
                <td>{$name}</td>
                <td>
                    {include file="fragments/admin-setting-value.tpl"}
                </td>
            </tr>
        {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <input type="button" id="settings_save" name="save" value="Save" />
            </td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    rc.settings.init();
</script>