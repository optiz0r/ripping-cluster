{if $success}
    "actions": {
        "add_setting_row": {
            {include file="fragments/admin-setting-row.tpl" assign=content}
            "content": {$content|json_encode} 
        }
    },
{/if}

"success": {$success|json_encode}

