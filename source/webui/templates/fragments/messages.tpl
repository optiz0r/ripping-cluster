{if $messages}
<ul>
    {foreach from=$messages item=message}
        <li>{$message|escape}</li>
    {/foreach}
</ul>
{/if}
