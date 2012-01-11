<h2>Sources</h2>

{if $all_sources}
	<p>
		The list below contains all the DVD sources that are available and ready for ripping.
	</p>
	<p>
		Sources that have recently been scanned are marked <em>(cached)</em> and will load fairly quickly.
		Sources that have not been cached will be scanned when the link is clicked, and this may take several minutes so please be patient.
	</p>
	<ul id="source-list">
		{include file="fragments/source-list.tpl"}
	</ul>
{else}
	<p>
		<em>There are currently no sources available to rip.</em>
	</p>
{/if}
