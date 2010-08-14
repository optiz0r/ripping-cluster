<h2>Jobs</h2>

{if $jobs}

    <form name="view-jobs" id="view-jobs" action="{$base_uri}jobs" method="post">
    <fieldset>
        <legend>View</legend>

        <label for="view-status">View only jobs with status:</label>
        <select id="view-status" name="view">
            <option value="any">Any Status</option>
            <option value="queued">Queued</option>
            <option value="running">Running</option>
            <option value="complete">Complete</option>
            <option value="failed">Failed</option>
        </select>

        <input type="submit" name="submit" value="view" />
    </fieldset>
    </form>

    <form name="manage-jobs" id="manage-jobs" action="{$base_uri}jobs/submit" method="post">
    <fieldset>
        <legend>Bulk Actions</legend>

        <input type="image" class="icon" name="action" id="mark-failed-top" value="mark-failed" src="{$base_uri}images/caution.png" alt="Mark all marked jobs as failed" />
        <input type="image" class="icon" name="action" id="redo-top" value="retry" src="{$base_uri}images/redo.png" alt="Repeat all marked jobs" />
        <input type="image" class="icon" name="action" id="delete-top" value="delete" src="{$base_uri}images/trash.png" alt="Delete all marked jobs" />
    </fieldset>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Destination</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$jobs item=job}
                {assign var=current_status value=$job->currentStatus()}
                <tr>
                    <td><a href="{$base_uri}job-details/id/{$job->id()}" title="View job details">{$job->name()}</a></td>
                    <td>{$job->destinationFilename()}</td>
                    <td>{$job->title()}</td>
                    <td>
                        {$current_status->statusName()}
                        {if $current_status->hasProgressInfo()}
                            <br />
                            Progress: {$current_status->ripProgress()}%<br />
                            At: <em>{$current_status->mtime()|date_format:"%D %T"}</em><br />
                            ETA: <em>{$job->calculateETA()|formatDuration}</em>
                        {/if}
                    </td>
                    <td>
                        <fieldset>
                            <input type="checkbox" name="include[]" value="{$job->id()}" />
                            <input type="image" class="icon" name="action" id="mark-failed-{$job->id()}" value="mark-failed[{$job->id()}]" src="{$base_uri}images/caution.png" alt="Mark job as failed" />
                            <input type="image" class="icon" name="action" id="redo-{$job->id()}" value="retry[{$job->id()}]" src="{$base_uri}images/redo.png" alt="Repeat job" />
                            <input type="image" class="icon" name="action" id="delete-{$job->id()}" value="delete[{$job->id()}]" src="{$base_uri}images/trash.png" alt="Delete job" />
                        </fieldset>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <fieldset>
        <legend>Bulk Actions</legend>

        <input type="image" class="icon" name="action" id="mark-failed-bottom" value="mark-failed" src="{$base_uri}images/caution.png" alt="Mark all marked jobs as failed" />
        <input type="image" class="icon" name="action" id="redo-bottom" value="retry" src="{$base_uri}images/redo.png" alt="Repeat all marked jobs" />
        <input type="image" class="icon" name="action" id="delete-bottom" value="delete" src="{$base_uri}images/trash.png" alt="Delete all marked jobs" />
    </fieldset>
    </form>
{else}
    <em>There are no jobs</em>
{/if}

