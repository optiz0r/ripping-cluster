<h2>Sources</h2>

{if $sources}
	<p>
		The list below contains all the DVD sources that are available and ready for ripping.
	</p>
	<p>
		Sources that have recently been scanned are marked <em>(cached)</em> and will load fairly quickly.
		Sources that have not been cached will be scanned when the link is clicked, and this may take several minutes so please be patient.
	</p>
	<ul>
		{foreach from=$sources item=source}
			<li>
				[ <a href="{$base_uri}rips/source-details/id/{$source|base64_encode|replace:"/":"-"}" title="Browse source details">Browse</a> |
				  <a href="{$base_uri}rips/setup-rip/id/{$source|base64_encode|replace:"/":"-"}" title="Rip this source">Rip</a> ]
				{$source|escape:'html'}{if $sources_cached.$source} (cached){/if}
			</li>
		{/foreach}
	</ul>
{else}
	<p>
		<em>There are currently no DVD sources available to rip.</em>
	</p>
{/if}
