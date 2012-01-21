<dl>
    {if $current_status->hasProgressInfo()}        
        <dt>Started</dt>
        <dd>{$current_status->ctime()|date_format:"%D %T"}</dd>

        <dt>Progress</dt>
        <dd>{$current_status->ripProgress()}%</dd>

        <dt>ETA</dt>
        <dd>{$job->calculateETA()|formatDuration}</dd>
    {/if}

    <dt>Last update</dt>
    <dd>{$current_status->mtime()|date_format:"%D %T"}</dd>    
</dl>
