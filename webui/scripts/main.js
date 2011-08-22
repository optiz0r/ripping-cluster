var rc = {

    init: function() {
        rc.ajax.init();
        rc.dialog.init();
        rc.page.init();
    },
        
    ajax: {
        
        init: function() {
        
        },
        
        get: function(url) {
            $.ajax({
                url: url,
                type: "GET", 
                dataType: "json", 
                success: rc.ajax.success, 
                error: rc.ajax.failure
            });
        },
        
        post: function(url, data) {
            $.ajax(url, {
                type: "POST", 
                dataType: "json",
                data: data,
                success: rc.ajax.success, 
                error: rc.ajax.failure
            });            
        },
        
        success: function(d, s, x) {
            rc.page.update(d);
            rc.dialog.prepare(d);
        },
        
        failure: function(x, s, e) {
            console.log("Ajax Failure: " + s, e);
            console.log(x.responseText);
        }
    },

    dialog: {
        
        init: function() {
            $("#dialogheaderclose").click(rc.dialog.close);
        },
        
        prepare: function(d) {
            if (d.dialog && d.dialog.show) {
                
                if (d.dialog.buttons) {
                    switch (d.dialog.buttons.type) {
                    case 'yesno': 
                        $("#dialogfooteryes").click(
                            function() {
                                rc.trigger(d.dialog.buttons.actions.yes, d.dialog.buttons.params);
                            }
                        );
                        $("#dialogfooterno").click(
                            function() {
                                rc.trigger(d.dialog.buttons.actions.no, d.dialog.buttons.params);
                            }
                        );
                        $("#dialogfooteryesno").show();
                        break;
                    }
                }
                
                $("#dialog").show();
            }
        },
        
        close: function() {
            $("#dialog").hide();
            $(".dialogfooterbuttonset").hide();
            $("#dialogcontent").html();
        }        
        
    },
    
    page: {
        
        init: function() {
        
        },
        
        update: function(d) {
            for ( var f in d.page_replacements) {
                $("#" + f).html(d.page_replacements[f].content);
            }            
        }
    },
    
    sources: {
        
        remove: function(plugin, source) {
            rc.ajax.get(base_url + "ajax/delete-source/plugin/" + plugin + "/id/" + source);        
        },
        
        remove_confirmed: function(plugin, source) {
            rc.ajax.get(base_url + "ajax/delete-source/plugin/" + plugin + "/id/" + source + "/confirm/");
        }
        
    },
    
    actions: {
        
        'close-dialog': function(params) {
            rc.dialog.close();
        },
        
        'delete-source-confirm': function(params) {
            rc.sources.remove_confirmed(params['plugin'], params['id']);
        }
        
    },
    
    trigger: function(action, params) {
        // Handle a list of actions by repeated calling self for each argument
        if (action instanceof Array) {
            for(i in action) {
                rc.trigger(action[i], params);
            }
            return;
        }
        
        // Check if action is supported, and execute it
        if (rc.actions[action]) {
            rc.actions[action](params);
        } else {
            console.log("Action not supported: " +action);
        }
    }
     
};

$(document).ready(rc.init);
