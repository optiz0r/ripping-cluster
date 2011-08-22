<h2>Job Details</h2>

<h3>Summary</h3>

<em>Summary details here</em>

<h3>Log messages</h3>
<h4>Options</h4>
<ul>
    {if $log_count_display eq 'all'}
        <li><a href="{$base_uri}jobs/details/id/{$job->id()}/order/{$log_order}/" title="View recent logs only">View recent messages only</a></li>
    {else}
        <li><a href="{$base_uri}jobs/details/id/{$job->id()}/logs/all/" title="View all logs">View all messages</a></li>
    {/if}
    <li><a href="{$base_uri}jobs/details/id/{$job->id()}/logs/{$log_count_display}/order/{$log_order_reverse}/" title="Reverse display order of log messages">Reverse display order</a></li>
</ul>

<h4>Recent Client Logs</h4>
{if $client_log_entries}
    <table>
        <thead>
            <tr>
                <th>Level</th>
                <th>Time</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$client_log_entries item=log_entry}
                <tr>
                    <td>{$log_entry->level()}</td>
                    <td>{$log_entry->ctime()|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                    <td>{$log_entry->message()}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <em>There are no client log entries.</em>
{/if}


<h4>Recent Worker Logs</h4>
{if $worker_log_entries}
    <table>
        <thead>
            <tr>
                <th>Level</th>
                <th>Time</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$worker_log_entries item=log_entry}
                <tr>
                    <td>{$log_entry->level()}</td>
                    <td>{$log_entry->ctime()|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                    <td>{$log_entry->message()}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <em>There are no worker log entries.</em>
{/if}

