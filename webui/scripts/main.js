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
            $.ajax({
                url: url,
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
                    case 'ok':
                        $("#dialogfooterok").click(
                            function() {
                                rc.trigger(d.dialog.buttons.actions.ok, d.dialog.buttons.params);
                            }
                        );
                        $("#dialogfooterok").show();
                        break;
                    case 'okcancel':
                        $("#dialogfooterokcancel_ok").click(function() {
                            rc.trigger(d.dialog.buttons.actions.ok, d.dialog.buttons.params);
                        });
                        $("#dialogfooterokcancel_cancel").click(function() {
                            rc.trigger(d.dialog.buttons.actions.cancel, d.dialog.buttons.params); 
                        });
                        $("#dialogfooterokcancel").show();
                        break;
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
        },
        
        'add-setting': function(params) {
            // TODO
            console.log('todo');
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
    },
    
    settings: {
        
        init: function() {
            $("#settings_save").click(function() {
                rc.settings.save();
            });
            
            $("#settings_new").click(function() {
                rc.settings.new_setting();
            });
        },
        
        new_setting: function() {
            rc.ajax.get(base_url + "ajax/admin/new-setting/");
        },
        
        add_stringlist_field: function(id) {
            var container = $('#container_'+id);
            var next = $('#settings_'+id+'_next');
            var next_value = next.val();
            
            var line = $('<div>');
            line.attr('id', 'settings_'+id+'_line'+next.val());
            line.append($('<input type="text" class="settings_field_string" />'));
            line.append(' ');
            var button = $('<input type="button" value="-" class="settings_field_remove"/>');
            button.click(function() {
                rc.settings.remove_field(id, next_value);
            });
            line.append(button);
            
            // Add the new item
            container.append(line);
            
            // Increment the next counter
            next.val(parseInt(next_value)+1);
            
        },
    
        remove_stringlist_field: function(id, line) {
            $("#settings_"+id+"_line"+line).remove();
        }, 
        
        save: function() {
            
            var settings = {};
            
            var fields = $("input.setting").get();
            for (var i in fields) {
                var setting = fields[i];
                var name = setting.name;
                var value;
                
                switch(setting.type) {
                    case 'checkbox':
                        value = $(setting).is(':checked') ? 1 : 0;
                        break;
                    default:
                        value = setting.value;
                }
                
                if (/\[\]$/.test(name)) {
                    if (! settings[name]) {
                        settings[name] = [];
                    }
                    settings[name].push(value);
                } else { 
                    settings[name] = value;
                }                    
            }
              
            rc.ajax.post(base_url + "ajax/update-settings/", settings);
            
        }
        
    }
     
};

$(document).ready(rc.init);
