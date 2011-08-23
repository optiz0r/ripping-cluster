<h2>Summary</h2>

<h3>Running Jobs</h3>

{if $running_jobs}
        {foreach from=$running_jobs item=job}
            <li><a href="{$base_uri}jobs/details/id/{$job->id()}" title="View job details">{$job->name()} ({$job->currentStatus()->ripProgress()}%)</a></li>
        {/foreach}
{else}
    <em>There are no currently running jobs.</em>
{/if}

<h3>Queued Jobs</h3>

{if $queued_jobs}
        {foreach from=$queued_jobs item=job}
            <li><a href="{$base_uri}jobs/details/id/{$job->id()}" title="View job details">{$job->name()}</a></li>
        {/foreach}
{else}
    <em>There are no currently running jobs.</em>
{/if}

<h3>Recently Completed Jobs</h3>

{if $completed_jobs}
    <ul>
        {foreach from=$completed_jobs item=job}
            <li><a href="{$base_uri}jobs/details/id/{$job->id()}" title="View job details">{$job->name()}</a></li>
        {/foreach}
    </ul>
{else}
    <em>There are no recently completed jobs.</em>
{/if}

<h3>Recently Failed Jobs</h3>

{if $failed_jobs}
    <ul>
        {foreach from=$failed_jobs item=job}
            <li><a href="{$base_uri}jobs/details/id/{$job->id()}" title="View job details">{$job->name()}</a></li>
        {/foreach}
    </ul>
{else}
    <em>There are no recently failed jobs.</em>
{/if}

