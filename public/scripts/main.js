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
            rc.trigger_all(d);
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
                
                if (d.dialog.title) {
                    $('#dialogheadertitle').html(d.dialog.title);
                }
                
                if (d.dialog.content) {
                    $('#dialogcontent').html(d.dialog.content);
                }
                
                $("#dialog").show();
            }
        },
        
        close: function() {
            // Hide the dialog
            $("#dialog").hide();
            
            // Remove the dialog content
            $("#dialogcontent").html();
            
            // Hide all buttons
            $(".dialogfooterbuttonset").hide();
            // Strip all event handlers
            $(".dialogfooterbuttonset input[type='button']").unbind('click');
        },
        
        error: function(title, content, messages) {
            var formatted_content = $('<div>').append($('<p>').text('content'));
            if (messages) {
                var formatted_messages = $('<ul>');
                for (var message in messages) {
                    formatted_messages.append($('<li>').text(message));
                }
                
                formatted_content.append($('<p>').text('These messages were reported:').append(formatted_messages));
            }
            
            rc.dialog.prepare({
                dialog: {
                    show: true,
                    title: title,
                    content: formatted_content,
                    buttons: {
                        type: 'ok',
                        actions: {
                            ok: 'close-dialog'
                        }
                    }
                }                
            });
        }
        
    },
    
    page: {
        
        init: function() {
            $('.progressBar').each(
                function() {
                    $(this).progressBar({
                        steps: 100,
                        width: 120,
                        height: 12,
                        boxImage: base_uri + 'images/jquery.progressbar/progressbar.gif',
                        barImage: {
                            0:  base_uri + 'images/jquery.progressbar/progressbg_red.gif',
                            25: base_uri + 'images/jquery.progressbar/progressbg_orange.gif',
                            50: base_uri + 'images/jquery.progressbar/progressbg_yellow.gif',
                            75: base_uri + 'images/jquery.progressbar/progressbg_green.gif',
                        }
                    });
                }
            );
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
            rc.ajax.post(base_url + 'ajax/admin/add-setting/name/' + $('#'+params.name).val() + '/type/' + $('#'+params.type).val() + '/');
        },
        
        'add_setting_row': function(params) {
            $("#settings tbody").append(params.content);
        },
        
        'rename_setting': function(params) {
            rc.ajax.post(base_url + 'ajax/admin/rename-setting/name/' + params.name + '/new-name/' + $('#'+params.new_name_field).val() + '/confirm/');
        },
        
        'rename_setting_confirm': function(params) {
            $('#setting_'+params.old_id+'_row').replaceWith($(params.content));
        },
        
        'remove_setting': function(params) {
            rc.ajax.post(base_url + 'ajax/admin/remove-setting/name/' + params.name + '/');
            rc.trigger('remove_setting_row', params);
        },
        
        'remove_setting_row': function(params) {
            $('#setting_' + params.id + '_row').remove();
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
    
    trigger_all: function(params) {
        if (params.actions) {
            for (var action in params.actions) {
                rc.trigger(action, params.actions[action]);
            }
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
        
        rename_setting: function(id, name) {
            rc.ajax.get(base_url + "ajax/admin/rename-setting/name/" + name + "/");
        },
        
        remove_setting: function(id, name) {
            rc.dialog.prepare({
                dialog: {
                    show: true,
                    title: 'Remove setting',
                    content: "Do you really want to remove setting '" + name + "'",
                    buttons: {
                        type: 'okcancel',
                        actions: {
                            ok: [
                                'remove_setting',
                                'close-dialog'
                            ],
                            cancel: 'close-dialog'
                        },
                        params: {
                            id: id,
                            name: name
                        }
                    }
                }
            });
        },
        
        add_stringlist_field: function(id) {
            var container = $('#container_'+id);
            var next = $('#settings_'+id+'_next');
            var next_value = next.val();
            
            var line = $('<div>');
            line.attr('id', 'settings_'+id+'_line'+next.val());
            line.append($('<input type="text" name="'+id+'[]" class="setting settings_field_string" />'));
            line.append(' ');
            var button = $('<input type="button" value="-" class="settings_field_remove"/>');
            button.click(function() {
                rc.settings.remove_stringlist_field(id, next_value);
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
        
        add_hash_field: function(id) {
            var container = $('#container_'+id);
            var next = $('#settings_'+id+'_next');
            var next_value = next.val();
            
            var line = $('<div>');
            line.attr('id', 'settings_'+id+'_line'+next.val());
            
            var hash_key = $('<input type="text" value="New" class="setting hash_key" />');
            var hash_value = $('<input type="text" id="setting_'+id+'_value'+next_value+'" name="'+id+'[New]" class="setting hash_value" />');
            hash_key.change(function() {
            	$('#setting_'+id+'_value'+next_value).attr('name', id+'['+$(this).val()+']');
            })
            
            line.append(hash_key).append(' ').append(hash_value).append(' ');
            var button = $('<input type="button" value="-" class="settings_field_remove"/>');
            button.click(function() {
                rc.settings.remove_hash_field(id, next_value);
            });
            line.append(button);
            
            // Add the new item
            container.append(line);
            
            // Increment the next counter
            next.val(parseInt(next_value)+1);
        },
        
        remove_hash_field: function(id, line) {
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
