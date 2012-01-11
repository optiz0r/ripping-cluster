{foreach from=$all_sources key=type item=sources}
	<li>{$type}
		{if $sources}
			<ul>
			{foreach from=$sources item=source}
				{assign var='source_plugin' value=$source->plugin()}
				{assign var='source_filename' value=$source->filename()}
				{assign var='source_filename_encoded' value=$source->filenameEncoded()}
				{assign var='source_cached' value=$source->isCached()}
				<li>
					[ <a href="{$base_uri}sources/details/plugin/{$source_plugin}/id/{$source_filename_encoded}" title="Browse source details">Browse</a> |
		  			<a href="{$base_uri}rips/setup/plugin/{$source_plugin}/id/{$source_filename_encoded}" title="Rip this source">Rip</a> |
		  			<a href="javascript:rc.sources.remove('{$source_plugin|escape:'quote'}', '{$source_filename_encoded|escape:'quote'}');" title="Delete this source">Delete</a> ]
					{$source_filename|escape:'html'}{if $source_cached} (cached){/if}
				</li>
			{/foreach}
			</ul>
		{else}
			<p>
				<em>There are no {$type} sources available to rip.</em>
			</p>
		{/if}
	</li>
{/foreach}

