<tr id="setting_{$id}_row">
    <td>
        <p>
            {$name}<br />
            <input type="button" id="setting_{$id}_remove" value="Remove" onclick="rc.settings.remove_setting('{$id}', '{$name}');" />
        </p>
    </td>
    <td>
        {include file="fragments/admin-setting-value.tpl"}
    </td>
</tr>