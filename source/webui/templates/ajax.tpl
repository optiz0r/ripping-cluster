{
	{if $messages}
		"messages": [
		{foreach from=$messages item=message name=messages}
			{$message|json_encode}{if ! $smarty.foreach.messages.last},{/if} 
		{/foreach}
		]{if $page_content},{/if}
	{/if}
	
	{$page_content}
    
}