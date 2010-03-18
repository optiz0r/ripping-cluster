<h2>Recent Client Logs</h2>

{if $client_log_entries}
    <table>
        <thead>
            <tr>
                <th>Job</th>
                <th>Level</th>
                <th>Time</th>
                <th>Hostname</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$client_log_entries item=log_entry}
                <tr>
                    <td>{$log_entry->jobId()}</td>
                    <td>{$log_entry->level()}</td>
                    <td>{$log_entry->ctime()|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                    <td>{$log_entry->hostname()}</td>
                    <td>{$log_entry->message()}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <em>There are no client log entries.</em>
{/if}


<h2>Recent Worker Logs</h2>

{if $worker_log_entries}
    <table>
        <thead>
            <tr>
                <th>Job</th>
                <th>Level</th>
                <th>Time</th>
                <th>Hostname</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$worker_log_entries item=log_entry}
                <tr>
                    <td>{$log_entry->jobId()}</td>
                    <td>{$log_entry->level()}</td>
                    <td>{$log_entry->ctime()|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                    <td>{$log_entry->hostname()}</td>
                    <td>{$log_entry->message()}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <em>There are no worker log entries.</em>
{/if}

