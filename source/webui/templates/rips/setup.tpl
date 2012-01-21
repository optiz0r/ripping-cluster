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
	
			<div class="clearfix">
				<label for="global-output-directory">Output directory</label>
                <select id="global-output-directory" name="rip-options[output-directory]" class="xxlarge">
                    <optgroup label="Custom"></optgroup>
                    <optgroup label="Defaults">
                        {foreach from=$default_output_directories item=dir key=name}
                            <option value="{$dir}">{$name}</option>
                        {/foreach}    
                    </optgroup>
                    <optgroup label="Recently Used">
                        {foreach from=$recent_output_directories item=dir name=recent}
                            {if $smarty.foreach.recent.iteration eq 1}
                                <option value="{$dir}" selected="selected">{$dir}</option>
                            {else}
                                <option value="{$dir}">{$dir}</option>
                            {/if}
                        {/foreach}                        
                    </optgroup>
                </select>
                <script type="text/javascript">
                    $('#global-output-directory').jec({
                        position: 1,
                        blinkingCursor: true
                    });
                </script>
                
			</div>
			
			<div class="clearfix">
				<label for="global-format">Output format</label>
				<select id="global-format" name="rip-options[format]" class="small">
					<option value="mkv" selected="selected">MKV</option>
				</select>
			</div>
			
			<div class="clearfix">
				<label for="global-video-codec">Video codec</label>
				<select id="global-video-codec" name="rip-options[video-codec]" class="small">
					<option value="x264" selected="selected">x264</option>
				</select>
			</div>
			
			<div class="clearfix">
				<label for="global-video-width">Video width</label>
				<div class="input">
    				<input type="text" id="global-video-width" name="rip-options[video-width]" value="0" />
    				<span class="help-inline">(Use 0 to leave size unchanged from source.)</span>
				</div>
			</div>
			
			<div class="clearfix">
				<label for="global-video-height">Video height</label>
				<div class="input">
    				<input type="text" id="global-video-height" class="small" name="rip-options[video-height]" value="0" />
				    <span class="help-inline">(Use 0 to leave size unchanged from source.)</span>
			    </div>
			</div>
			
			<div class="clearfix">
				<label for="global-quantizer">Quantizer</label>
                <div class="input">
                    <div id="quantizer-slider"></div>
                    <input type="text" id="global-quantizer" class="small" name="rip-options[quantizer]" value="" readonly="readonly" />
    				<span class="help-inline">(Defaults to 0.61, x264's quantizer value for 20)</span>
				</div>
			</div>
	
		</fieldset>
			
		<div id="available-titles">
			{foreach from=$titles item=title}
		   	 	<h3 id="configure-rip-{$title->id()}"><a href="#">Title {$title->id()} (Duration: {$title->duration()}, Chapters: {$title->chapterCount()})</a></h3>
		    	<div id="rips-{$title->id()}">
		    		<fieldset>
		    			<legend>Configure title rip options</legend>
		    			
		    			<div class="clearfix">
		    				<label for="rip-title-{$title->id()}">Rip this title</label>
		    				<input type="checkbox" id="rip-title-{$title->id()}" name="rips[{$title->id()}][queue]" value="1" />
		    			</div>

                        <div class="clearfix">
                            <label for="rip-name-{$title->id()}">Short Name</label>
                            <input type="text" id="rip-name-{$title->id()}" name="rips[{$title->id()}][name]" value="" />
                        </div>

						<div class="clearfix">		    			
			    			<label for="rip-audio-{$title->id()}">Audio tracks</label>
			    			<select id="rip-audio-{$title->id()}" name="rips[{$title->id()}][audio][]" title="Select audio tracks" size="5" multiple="multiple" class="rip-streams">
			    				{foreach from=$title->audioTracks() item=audio}
			    					<option value="{$audio->id()}">{$audio->name()} - {$audio->channels()} ({$audio->language()}) </option>
			    				{/foreach}
			    			</select>
			    			
		    			<div class="clearfix">
			    			<label for="rip-subtitle-{$title->id()}">Subtitle tracks</label>
			    			<select id="rip-subtitle-{$title->id()}" name="rips[{$title->id()}][subtitles][]" title="Select subtitle tracks" size="5" multiple="multiple" class="rip-streams">
			    				{foreach from=$title->subtitleTracks() item=subtitle}
			    					<option value="{$subtitle->id()}">{$subtitle->language()}</option>
			    				{/foreach}
			    			</select>

		    			<div class="clearfix">
			    			<label for="rips-output-{$title->id()}">Output filename</label>
			    			<input type="text" id="rips-output-{$title->id()}" name="rips[{$title->id()}][output_filename]" value="" />
		    			</div>

						<div class="clearfix">	
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
        $('select[multiple]').asmSelect({
            
        });
	});
	</script>
	{/literal}
{/if}
