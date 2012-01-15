{switch $type}
    {case Sihnon_Config::TYPE_BOOL}
        <input type="checkbox" id="setting_{$id}" name="{$id}" value="1" {if $value}checked="checked" {/if} class="setting" />
    {/case}
    {case Sihnon_Config::TYPE_INT}
        <input type="text" id="setting_{$id}" name="{$id}" value="{$value}" class="small setting settings_field_numeric" />
    {/case}
    {case Sihnon_Config::TYPE_STRING}
        <input type="text" id="setting_{$id}" name="{$id}" value="{$value}" class="xxlarge setting settings_field_string" />
    {/case}
    {case Sihnon_Config::TYPE_STRING_LIST}
        <div id="container_{$id}">
            {foreach from=$value item=line name=settings}
                <div id="settings_{$id}_line{$smarty.foreach.settings.iteration}">
                    <input type="text" name="{$id}[]" value="{$line}" class="xxlarge setting settings_field_string" />
                    <button class="btn small settings_field_remove" onclick="rc.settings.remove_stringlist_field('{$id}', '{$smarty.foreach.settings.iteration}')">-</button>
                </div>
            {/foreach}
        </div>
        <div class="settings_addfieldcontainer">
            <input type="hidden" id="settings_{$id}_next" value="{$smarty.foreach.settings.iteration+1}" />
            <button class="btn small settings_field_add" onclick="rc.settings.add_stringlist_field('{$id}')">+</button>
        </div>
    {/case}
    {case Sihnon_Config::TYPE_HASH}
        <div id="container_{$id}">
            {if $value}
                {foreach from=$value item=hash_value key=hash_key name=settings}
                    <div id="settings_{$id}_line{$smarty.foreach.settings.iteration}">
                        <input type="text" value="{$hash_key}" class="small setting hash_key" />
                        <input type="text" name="{$id}[{$hash_key}]" value="{$hash_value}" class="xlarge setting hash_value" />
                        <button class="btn small settings_field_remove" onclick="rc.settings.remove_hash_field('{$id}', '{$smarty.foreach.settings.iteration}')">-</button>
                    </div>
                {/foreach}
            {/if}
        </div>
        <div class="settings_addfieldcontainer">
            <input type="hidden" id="settings_{$id}_next" value="{$smarty.foreach.settings.iteration+1}" />
            <button class="btn small settings_field_add" onclick="rc.settings.add_hash_field('{$id}')">+</button>
        </div>
        
    {/case}
    {default}
        <em>Unsupported setting type!</em>
{/switch}