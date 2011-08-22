<h2>Setup Rips</h2>

{if $rips_submitted}
	<h3>Jobs Queued</h3>
	
	<p>
		The rips have been queued, and processing has begun in the background. View the <a href="{$base_uri}jobs/" title="View running jobs">Jobs</a> page
		to see a list of running jobs, or the <a href="{$base_uri}logs/" title="View logs">logs</a> page for more detailed progress information.
	</p>
{else}
    <h3>{$source->filename()|escape:"html"}</h3>
			
	<form name="setup" id="setup-rips" action="{$base_uri}rips/setup/submit/" method="post">
		<input type="hidden" name="plugin" value="{$source->plugin()|escape:"html"}" />
		<fieldset>
			<legend>Configure global rip options</legend>

			<input type="hidden" name="id" value="{$source->filenameEncoded()|escape:"html"}" />
	
			<div>
				<label for="global-output-directory">Output directory</label>
				<input type="text" id="global-ouput-directory" name="rip-options[output-directory]" value="{$default_output_directory}" />
			</div>
			
			<div>
				<label for="global-format">Output format</label>
				<select id="global-format" name="rip-options[format]">
					<option value="mkv" selected="selected">MKV</option>
				</select>
			</div>
			
			<div>
				<label for="global-video-codec">Video codec</label>
				<select id="global-video-codec" name="rip-options[video-codec]">
					<option value="x264" selected="selected">x264</option>
				</select>
			</div>
			
			<div>
				<label for="global-video-width">Video width</label>
				<input type="text" id="global-video-width" name="rip-options[video-width]" value="0" />
				<em>(Use 0 to leave size unchanged from source.)</em>
			</div>
			<div>
				<label for="global-video-height">Video height</label>
				<input type="text" id="global-video-width" name="rip-options[video-width]" value="0" />
				<em>(Use 0 to leave size unchanged from source.)</em>
			</div>
			
			<div>
				<label for="global-quantizer">Quantizer</label>
				<input type="text" id="global-quantizer" name="rip-options[quantizer]" value="" readonly="readonly" />
				<em>(Defaults to 0.61, x264's quantizer value for 20)</em>
				<div id="quantizer-slider"></div>
			</div>
	
			<div>
				<input type="submit" name="submit" value="Queue rips" />
			</div>
		</fieldset>
			
		<div id="available-titles">
			{foreach from=$titles item=title}
		   	 	<h3 id="configure-rip-{$title->id()}"><a href="#">Title {$title->id()} (Duration: {$title->duration()}, Chapters: {$title->chapterCount()})</a></h3>
		    	<div id="rips-{$title->id()}">
		    		<fieldset>
		    			<legend>Configure title rip options</legend>
		    			
		    			<div>
		    				<label for="rip-title-{$title->id()}">Rip this title</label>
		    				<input type="checkbox" id="rip-title-{$title->id()}" name="rips[{$title->id()}][queue]" value="1" />
		    			</div>

                        <div>
                            <label for="rip-name-{$title->id()}">Short Name</label>
                            <input type="text" id="rip-name-{$title->id()}" name="rips[{$title->id()}][name]" value="" />
                        </div>

						<div>		    			
			    			<label for="rip-audio-{$title->id()}">Audio tracks</label>
			    			<select id="rip-audio-{$title->id()}" name="rips[{$title->id()}][audio][]" size="5" multiple="multiple" class="rip-streams">
			    				{foreach from=$title->audioTracks() item=audio}
			    					<option value="{$audio->id()}">{$audio->name()} - {$audio->channels()} ({$audio->language()}) </option>
			    				{/foreach}
			    			</select>
			    			
			    			<table class="audio-tracks">
			    				<caption>Selected audio tracks</caption>
			    				<thead>
                                    <tr>
    			    					<th>Track</th>
    			    					<th>Encoder</th>
    			    					<th>Name</th>
                                    </tr>
			    				</thead>
			    				<tbody>
			    					
			    				</tbody>
			    			</table>
		    			</div>
		    		
		    			<div>
			    			<label for="rip-subtitle-{$title->id()}">Subtitle tracks</label>
			    			<select id="rip-subtitle-{$title->id()}" name="rips[{$title->id()}][subtitles][]" size="5" multiple="multiple" class="rip-streams">
			    				{foreach from=$title->subtitleTracks() item=subtitle}
			    					<option value="{$subtitle->id()}">{$subtitle->language()}</option>
			    				{/foreach}
			    			</select>

			    			<table class="subtitle-tracks">
			    				<caption>Selected subtitle tracks</caption>
			    				<thead>
                                    <tr>
    			    					<th>Track</th>
    			    					<th>Language</th>
    			    					<th>Format</th>
                                    </tr>
			    				</thead>
			    				<tbody>
			    					
			    				</tbody>
			    			</table>
		    			</div>
		    		
		    			<div>
			    			<label for="rips-output-{$title->id()}">Output filename</label>
			    			<input type="text" id="rips-output-{$title->id()}" name="rips[{$title->id()}][output_filename]" value="" />
		    			</div>

						<div>	
							<label for="rip-deinterlace-{$title->id()}">Deinterlacing</label>	    			
			    			<select id="rip-deinterlace-{$title->id()}" name="rips[{$title->id()}][deinterlace]">
			    				<option value="0">None</option>
			    				<option value="1">Full</option>
			    				<option value="2" selected="selected">Selective</option>
			    			</select>
		    			</div>
		    		
		    		</fieldset>
		    	</div>
		    {/foreach}
		</div>
	
		<fieldset>
			<legend>Queue rips</legend>
			<input type="submit" name="submit" value="Queue rips" />
		</fieldset>
	</form>
	
	{literal}
	<script language="javascript">
	$(function() {
		$("#available-titles").accordion({active: {/literal}{$source->longestTitleIndex()}{literal}});
		$("input:submit").button();
		$("#quantizer-slider").slider({
			value:0.61,
			min: 0,
			max: 1.0,
			step: 0.01,
			slide: function(event, ui) {
				$("#global-quantizer").val(ui.value);
			}
		});
		$("#global-quantizer").val($("#quantizer-slider").slider("value"));
	});
	</script>
	{/literal}
{/if}
