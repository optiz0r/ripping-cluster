{if $confirm}
    {if $success}
        "actions": {
            "rename_setting_confirm": {
                "old_name": {$old_name|json_encode},
                "old_id": {$old_id|json_encode},
                "name": {$name|json_encode},
                {include file="fragments/admin-setting-row.tpl" assign="content"}
                "content": {$content|json_encode}
            }
        },
    {/if}
    
    "success": {$success|json_encode}
{else}
    "page_replacements": {
        "dialog-header-title": {
            "content": "Rename Setting"
        },
        
        "dialog-body": {
            {include file="fragments/rename-setting-dialog.tpl" assign="content"}
            "content": {$content|json_encode}
        }
    },
    
    "dialog": {
        "show": true,
        "buttons": {
            "type": "okcancel",
            "actions": {
                "ok": [
                    "rename_setting",
                    "close-dialog"
                ],
                "cancel": "close-dialog"
            },
            "params": {
                "name": {$name|json_encode},
                "new_name_field": "settings_rename_name" 
            }
        }
    }
{/if}