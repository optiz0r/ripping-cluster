<h2>Source details</h2>

{if $source}
	<table class="source-details">
		<colgroup class="header">
			<col />
		</colgroup>
		<colgroup>
			<col />
		</colgroup>
		
		<tbody>
			<tr>
				<th>Source</th>
				<td>{$source|escape:"html"}</td>
			</tr>
		</tbody>
	</table>
{else}
	<p>
		<em>This is not a valid source.</em>
	</p>
{/if}