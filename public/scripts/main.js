/**
 * Ripping Cluster Webui
 * 
 * Written by Ben Roberts
 * Homepage: https://benroberts.net/projects/ripping-cluster/
 * Code:     https://github.com/optiz0r/ripping-cluster-webui/
 * 
 * Dependencies:
 *   - Bootstrap
 *   - JQuery
 *   - JQueryUI
 *   - JQuery Progressbar
 * 
 * Released under a Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

/**
 * Ripping Cluster object
 * 
 * Entry point for all ripping cluster webui code
 */
var rc = {

    /**
     * Initialises the webui code
     */
    init: function() {
        rc.page.init();
        
        rc.sources.init();
        rc.settings.init();
    },
    
    /**
     * Page module
     * 
     * Configures hooks for updating pages
     */
    page: {
        
        /**
         * Initialises the module
         */
        init: function() {
            
            // Display pretty progress bars
            sf.page.addCallback('progress-bars', function(d) {
                $(d).find('.progressBar').each(
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
            });
            
            // Display highlights on given items when hovered over
            sf.page.addCallback('hover-highlights', function(d) {
                $(d).find('.hover-highlight').hover(
                    function() {
                        $(this).addClass('highlight');
                    },
                    function() {
                        $(this).removeClass('highlight');
                    }
                );
            });
            
            // Display popovers
            sf.page.addCallback('popovers', function(d) {
                $(d).find('a[rel=popover]').popover({
                  offset: 10,
                  html: true,
                });
            });
            
            // Configure select-all checkboxes 
            sf.page.addCallback('select-all-checkboxes', function(d) {
                $(d).find('input[type=checkbox].select_all').click(function() {
                    $('input[type=checkbox].'+$(this).attr('id')).attr('checked', $(this).attr('checked') == 'checked');
                });
            });
            
            // Update the content of the page on first load
            sf.page.updateEvents($('#page_content'));
        }
    
    },
    
    /**
     * Sources module
     * 
     * Contains code for interacting with rip sources
     */
    sources: {
       
        /**
         * Initialises the module
         */
        init: function() {
            
            sf.actions.addAction('delete-source-confirm', function(params) {
                rc.sources.remove_confirmed(params['plugin'], params['id']);
            });            
            
        },
        
        /**
         * Display a confirmation box for deleting a source
         * 
         * @param plugin Name of the plugin providing the source
         * @param source Encoded filename of the source to be deleted
         */
        remove: function(plugin, source) {
            sf.ajax.get(base_url + "ajax/delete-source/plugin/" + plugin + "/id/" + source);        
        },
        
        /**
         * Permanently delete a source
         * 
         * @param plugin Name of the plugin providing the source
         * @param source Encoded filename of the source to be deleted
         */
        remove_confirmed: function(plugin, source) {
            sf.ajax.get(base_url + "ajax/delete-source/plugin/" + plugin + "/id/" + source + "/confirm/");
        },
        
    },
    
    /**
     * Settings module
     * 
     * Contains code for handling the admin settings page
     */
    settings: {
        
        /**
         * Configure actions for handling settings ajax requests.
         */
        init: function() {
            sf.actions.addAction('add-setting', function(params) {
                sf.ajax.post(base_url + 'ajax/admin/add-setting/name/' + $('#'+params.name).val() + '/type/' + $('#'+params.type).val() + '/');
            });
            
            sf.actions.addAction('add_setting_row', function(params) {
                $("#settings tbody").append(params.content);
            });
            
            sf.actions.addAction('rename_setting', function(params) {
                sf.ajax.post(base_url + 'ajax/admin/rename-setting/name/' + params.name + '/new-name/' + $('#'+params.new_name_field).val() + '/confirm/');
            });
            
            sf.actions.addAction('rename_setting_confirm', function(params) {
                $('#setting_'+params.old_id+'_row').replaceWith($(params.content));
            });
            
            sf.actions.addAction('remove_setting', function(params) {
                sf.ajax.post(base_url + 'ajax/admin/remove-setting/name/' + params.name + '/');
                sf.actions.trigger('remove_setting_row', params);
            });
            
            sf.actions.addAction('remove_setting_row', function(params) {
                $('#setting_' + params.id + '_row').remove();
            });

            $("#settings_save").click(function() {
                rc.settings.save();
            });
            
            $("#settings_new").click(function() {
                rc.settings.new_setting();
            });
        },
        
        /**
         * Add a new setting to the settings list
         * 
         * Presents a dialog box prompting for the setting details
         * 
         */
        new_setting: function() {
            sf.ajax.get(base_url + "ajax/admin/new-setting/");
        },
        
        /**
         * Rename a setting
         * 
         * Presents a dialog box prompting for the new setting name
         * 
         * @param id DOM ID of the setting to be renamed
         * @param name Name of the setting to be renamed
         */
        rename_setting: function(id, name) {
            sf.ajax.get(base_url + "ajax/admin/rename-setting/name/" + name + "/");
        },
        
        /**
         * Removes a setting
         * 
         * Presents a dialog to confirm removal
         * 
         * @param id DOM ID of the setting to be removed
         * @param name Name of the setting to be removed
         */
        remove_setting: function(id, name) {
            sf.ui.dialog.prepare({
                show: true,
                title: 'Remove this setting?',
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
            });
        },
        
        /**
         * Adds a new field to a string list setting
         * 
         * For array(string) setting types, adds UI for a new element in the array
         * 
         * @param id DOM ID of the setting to have a new element added
         */
        add_stringlist_field: function(id) {
            var container = $('#container_'+id);
            var next = $('#settings_'+id+'_next');
            var next_value = next.val();
            
            var line = $('<div>');
            line.attr('id', 'settings_'+id+'_line'+next.val());
            line.append($('<input type="text" name="'+id+'[]" class="setting settings_field_string" />'));
            line.append(' ');
            var button = $('<button class="btn small settings_field_remove">-</button>');
            button.click(function() {
                rc.settings.remove_stringlist_field(id, next_value);
            });
            line.append(button);
            
            // Add the new item
            container.append(line);
            
            // Increment the next counter
            next.val(parseInt(next_value)+1);
        },
    
        /**
         * Removes a field from a string list setting
         * 
         * For array(string) setting types, removes the UI for an element in the array
         * 
         * @param id DOM ID of the setting to be modified
         * @param line Number of the line to be removed
         */
        remove_stringlist_field: function(id, line) {
            $("#settings_"+id+"_line"+line).remove();
        },
        
        /**
         * Add a new Hash setting key
         * 
         * For hash setting types, adds UI for a new key
         * 
         * @param id DOM ID of the setting to be modified
         */
        add_hash_field: function(id) {
            var container = $('#container_'+id);
            var next = $('#settings_'+id+'_next');
            var next_value = next.val();
            
            var line = $('<div>');
            line.attr('id', 'settings_'+id+'_line'+next.val());
            
            var hash_key = $('<input type="text" value="" class="small setting hash_key" />');
            var hash_value = $('<input type="text" id="setting_'+id+'_value'+next_value+'" name="'+id+'[New]" class="xlarge setting hash_value" />');
            hash_key.change(function() {
            	$('#setting_'+id+'_value'+next_value).attr('name', id+'['+$(this).val()+']');
            });
            
            line.append(hash_key).append(' ').append(hash_value).append(' ');
            var button = $('<button class="btn small settings_field_remove">-</button>');
            button.click(function() {
                rc.settings.remove_hash_field(id, next_value);
            });
            line.append(button);
            
            // Add the new item
            container.append(line);
            
            // Increment the next counter
            next.val(parseInt(next_value)+1);
        },
        
        /**
         * Removes a hash setting key
         * 
         * For hash setting types, removes the UI for a specific key
         * 
         * @param id DOM ID of the setting to be modified
         * @param line Line number of the hash key to be removed
         */
        remove_hash_field: function(id, line) {
        	$("#settings_"+id+"_line"+line).remove();
        },
        
        /**
         * Saves the current setting values to the database
         * 
         */
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
              
            sf.ajax.post(base_url + "ajax/update-settings/", settings);
            
        }
        
    }
     
};

$(document).ready(rc.init);
