<dl>
    <dt>Destination Filename</dt>
    <dd>{$job->destinationFilename()|escape:html}</dd>
    
    {if $job->isFinished()}
        <dt>File size</dt>
        <dd>({$job->outputFilesize()|formatFilesize|escape:html})</dd>
    {/if}
</dl>