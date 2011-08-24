{
	{if $messages}
		messages: [
		{foreach from=$messages item=message}
			'{$message|json_encode}',
		{/foreach}
		],
	{/if}
	
	{$page_content}
}