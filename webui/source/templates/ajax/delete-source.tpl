
"page_replacements": {
    {if $confirmed}
        "source-list": {
            {include file="fragments/source-list.tpl" assign="sources_html"}
            "content": {$sources_html|json_encode}
        }
    {else}
        "dialogcontent": {
            {include file="fragments/delete-source.tpl" assign="delete_source_html"}
            "content": {$delete_source_html|json_encode}
        }
    {/if}

{if ! $confirmed}
},

"dialog": {
    "show": true,
    "buttons": {
        "type": "yesno",
        "actions": {
            "yes": [
                "delete-source-confirm",
                "close-dialog"
            ],
            "no": "close-dialog"
        },
        "params": {
            "plugin": {$source_plugin|json_encode},
            "id": {$source_id|json_encode}
        }
    }
}
{else}
}
{/if}
