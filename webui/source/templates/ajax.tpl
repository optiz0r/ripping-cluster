{
	{if $messages}
		messages: [
		{foreach from=$messages item=message}
			'{$message}',
		{/foreach}
		],
	{/if}
	
	{$page_content}
}