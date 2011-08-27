"page_replacements": {

    "dialogheadertitle": {
        "content": "Add Setting"
    },
    
    "dialogcontent": {
        {include file="admin/add-setting.tpl" assign=add_setting_content}
        "content": {$add_setting_content|json_encode}
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
        }
    }
}