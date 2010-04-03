<h2>Setup Rips</h2>

{if $rips_submitted}
	<h3>Jobs Queued</h3>
	
	<p>
		The rips have been queued, and processing has begun in the background. View the <a href="{$base_uri}jobs/" title="View running jobs">Jobs</a> page
		to see a list of running jobs, or the <a href="{$base_uri}logs/" title="View logs">logs</a> page for more detailed progress information.
	</p>
{else}
	<form name="setup-rips" id="setup-rips" action="{$base_uri}rips/setup-rip/submit/" method="post">
		<fieldset>
			<legend>Configure titles to rip</legend>
			
			<input type="hidden" name="id" value="{$source->filenameEncoded()|escape:"html"}" />
	
			<input type="submit" name="submit" value="Queue rips" />
			
			<div id="available-titles">
				{foreach from=$titles item=title}
			   	 	<h3><a href="#">Title {$title->id()} (Duration: {$title->duration()}, Chapters: {$title->chapterCount()})</a></h3>
			    	<div id="rips-{$title->id()}">
			    		<fieldset>
			    			<legend>Configure title rip options</legend>
			    			
			    			<input type="checkbox" id="rip-title-{$title->id()}" name="rips[{$title->id()}][queue]" value="1" />
			    			<label for="rip-title-{$title->id()}">Rip this title</label>
			    			
			    			<hr />
			    			
			    			<label for="rip-audio-{$title->id()}">Audio tracks</label>
			    			<select id="rip-audio-{$title->id()}" name="rips[{$title->id()}][audio][]" size="5" multiple="multiple">
			    				{foreach from=$title->audioTracks() item=audio}
			    					<option value="{$audio->id()}">{$audio->name()} - {$audio->channels()} ch ({$audio->language()}) </option>
			    				{/foreach}
			    			</select>
			    		
			    			<label for="rip-subtitle-{$title->id()}">Subtitle tracks</label>
			    			<select id="rip-subtitle-{$title->id()}" name="rips[{$title->id()}][subtitles][]" size="5" multiple="multiple">
			    				{foreach from=$title->subtitleTracks() item=subtitle}
			    					<option value="{$subtitle->id()}">{$subtitle->language()}</option>
			    				{/foreach}
			    			</select>
			    		
			    			<hr />
			    		
			    			<label for="rips-output-{$title->id()}">Output filename</label>
			    			<input type="text" id="rips-output-{$title->id()}" name="rips[{$title->id()}][output_filename]" value="" />
			    			
			    			<hr />
			    			
			    			<select id="rip-deinterlace-{$title->id()}" name="rips[{$title->id()}][deinterlace]">
			    				<option value="0">Don't deinterlace</option>
			    				<option value="1">Do deinterlace</option>
			    				<option value="2" selected="selected">Selectively deinterlace</option>
			    			</select>
			    		
			    		</fieldset>
			    	</div>
			    {/foreach}
			</div>
	
			<input type="submit" name="submit" value="Queue rips" />
		</fieldset>
	</form>
	
	{literal}
	<script language="javascript">
	$(function() {
		$("#available-titles").accordion();
		$("input:submit").button();
	});
	</script>
	{/literal}
{/if}