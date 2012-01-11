<h2>Delete Source</h2>

<p>
	Are you sure you want to delete {$source->plugin()|escape:"html"}:{$source->filename()|escape:"html"}?
	[ <a href="{$base_uri}sources/delete/plugin/{$source->plugin()}/id/{$source->filenameEncoded()}/confirm" title="Delete it">Delete</a>
	| <a href="{$base_uri}rips/sources" title="Return to sources list">Cancel</a> ]
</p>
