<h2>Sources</h2>

{if $sources}
	<p>
		The following DVD sources are available to be ripped:
	</p>
	<ul>
		{foreach from=$sources item=source}
			<li><a href="{$base_uri}browse/source-details/id/{$source|base64_encode|replace:"/":"-"}" title="View source details">{$source|escape:'html'}</a></li>
		{/foreach}
	</ul>
{else}
	<p>
		<em>There are currently no DVD sources available to rip.</em>
	</p>
{/if}
