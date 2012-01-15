<tr id="setting_{$id}_row">
    <td>
        <p>
            <strong>{$name}</strong>
        </p>
        <p>
            <button id="setting_{$id}_rename" class="btn" onclick="rc.settings.rename_setting('{$id}', '{$name}');">Rename</button>
            <button id="setting_{$id}_remove" class="btn" onclick="rc.settings.remove_setting('{$id}', '{$name}');">Remove</button>
        </p>
    </td>
    <td>
        {include file="fragments/admin-setting-value.tpl"}
    </td>
</tr>