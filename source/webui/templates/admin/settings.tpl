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
                <button id="settings_save" class="btn primary" name="save">Save</button>
                <button id="settings_new" class="btn" name="new_setting">New Setting</button>
            </td>
        </tr>
    </tfoot>
</table>
