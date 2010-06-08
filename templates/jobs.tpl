<h2>Jobs</h2>

{if $jobs}
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Destination</th>
                <th>Title</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$jobs item=job}
                {assign var=current_status value=$job->currentStatus()}
                <tr>
                    <td><a href="{$base_uri}/job-details/id/{$job->id()}" title="View job details">{$job->name()}</a></td>
                    <td>{$job->destinationFilename()}</td>
                    <td>{$job->title()}</td>
                <td>{$current_status->statusName()}{if $current_status->hasProgressInfo()} ({$current_status->ripProgress()}%, <em>last updated {$current_status->mtime()|date_format:"%D %T"}</em>){/if}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <em>There are no jobs</em>
{/if}
