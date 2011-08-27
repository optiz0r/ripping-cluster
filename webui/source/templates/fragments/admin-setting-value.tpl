{switch $type}
    {case Sihnon_Config::TYPE_BOOL}
        <input type="checkbox" id="setting_{$id}" name="{$id}" value="1" {if $value}checked="checked" {/if} class="setting" />
    {/case}
    {case Sihnon_Config::TYPE_INT}
        <input type="text" id="setting_{$id}" name="{$id}" value="{$value}" class="setting settings_field_numeric" />
    {/case}
    {case Sihnon_Config::TYPE_STRING}
        <input type="text" id="setting_{$id}" name="{$id}" value="{$value}" class="setting settings_field_string" />
    {/case}
    {case Sihnon_Config::TYPE_STRING_LIST}
        <div id="container_{$id}">
            {foreach from=$value item=line name=settings}
                <div id="settings_{$id}_line{$smarty.foreach.settings.iteration}">
                    <input type="text" name="{$id}[]" value="{$line}" class="setting settings_field_string" />
                    <input type="button" value="-" class="settings_field_remove" onclick="rc.settings.remove_stringlist_field('{$id}', '{$smarty.foreach.settings.iteration}')" />
                </div>
            {/foreach}
        </div>
        <div class="settings_addfieldcontainer">
            <input type="hidden" id="settings_{$id}_next" value="{$smarty.foreach.settings.iteration+1}" />
            <input type="button" value="+" class="settings_field_add" onclick="rc.settings.add_stringlist_field('{$id}')" />
        </div>
    {/case}
{/switch}