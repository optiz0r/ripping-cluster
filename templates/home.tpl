<h2>Summary</h2>

<h3>Running Jobs</h3>

{if $running_jobs}
        {foreach from=$running_jobs item=job}
            <li><a href="{$base_uri}job-details/id/{$job->id()}" title="View job details">Job {$job->id()}</a></li>
        {/foreach}
{else}
    <em>There are no currently running jobs.</em>
{/if}

<h3>Completed Jobs</h3>

{if $completed_jobs}
    <ul>
        {foreach from=$completed_jobs item=job}
            <li><a href="{$base_uri}job-details/id/{$job->id()}" title="View job details">Job {$job->id()}</a></li>
        {/foreach}
    </ul>
{else}
    <em>There are no recently completed jobs.</em>
{/if}

