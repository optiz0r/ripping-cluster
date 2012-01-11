"page_replacements": {

    "dialogheadertitle": {
        "content": "Add Setting"
    },
    
    "dialogcontent": {
        {include file="fragments/new-setting-dialog.tpl" assign=new_setting_dialog_content}
        "content": {$new_setting_dialog_content|json_encode}
    }

},

"dialog": {
    "show": true,
    "buttons": {
        "type": "okcancel",
        "actions": {
            "ok": [
                "add-setting",
                "close-dialog"
            ],
            "cancel": "close-dialog"
        },
        "params": {
            "name": "settings_add_name",
            "type": "settings_add_type"
        }
    }
}