"page_replacements": {

    "dialogheadertitle": {
        "content": "Update Settings"
    },
    
    "dialogcontent": {
        {include file="fragments/update-settings-dialog.tpl" assign=dialog_content}
        "content": {$dialog_content|json_encode}
    }

}, 

"dialog": {
    "show": true,
    "buttons": {
        "type": "ok",
        "actions": {
            "ok": "close-dialog"
        }
    }
}
