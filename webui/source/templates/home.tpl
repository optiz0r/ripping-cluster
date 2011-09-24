<h2>Summary</h2>

<h3>Running Jobs</h3>

{if $running_jobs}
        {foreach from=$running_jobs item=job}
            <li><a href="{$base_uri}jobs/details/id/{$job->id()}" title="View job details">{$job->name()}</a> <span class="progressBar" id="job_progress_{$job->id()}">{$job->currentStatus()->ripProgress()}%</span></li>
        {/foreach}
        <script type="text/javascript">
            $('.progressBar').each(
                function() {
                    $(this).progressBar({
                        steps: 100,
                        width: 120,
                        height: 12,
                        boxImage: '{$base_uri}images/jquery.progressbar/progressbar.gif',
                        barImage: {
                            0:  '{$base_uri}images/jquery.progressbar/progressbg_red.gif',
                            25: '{$base_uri}images/jquery.progressbar/progressbg_orange.gif',
                            50: '{$base_uri}images/jquery.progressbar/progressbg_yellow.gif',
                            75: '{$base_uri}images/jquery.progressbar/progressbg_green.gif',
                        }
                    });
                }
            );
        </script>
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

