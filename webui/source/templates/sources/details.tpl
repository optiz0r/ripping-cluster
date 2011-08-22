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
				<td>{$source->filename()|escape:"html"}</td>
			</tr>
			{if $titles}
				<tr>
					<th>Titles</th>
					<td>
						<table class="titles">
							<colgroup class="title-number">
								<col />
							</colgroup>
							<colgroup class="title-header">
								<col />
							</colgroup>
							<colgroup>
								<col />
							</colgroup>
							
							<tbody>
								{foreach from=$titles item=title}
									<tr>
										<th rowspan="5">{$title->id()}</th>
										<td>Duration</td>
										<td>{$title->duration()}</td>
									</tr>
									<tr>
										<td>Display</td>
										<td>
											<ul>
												<li>Size: {$title->width()}x{$title->height()}</li>
												<li>Pixel aspect ratio: {$title->pixelAspect()}</li>
												<li>Display aspect ratio: {$title->displayAspect()}</li>
												<li>Framerate: {$title->framerate()}</li>
												<li>Autocrop: {$title->autocrop()}</li>
											</ul>
										</td>
									</tr>
									<tr>
										<td>Chapters</td>
										<td>{$title->chapterCount()}</td>
									</tr>
									<tr>
										<td>Audio Tracks</td>
										<td>{$title->audioTrackCount()}</td>
									</tr>
									<tr>
										<td>Subtitle Tracks</td>
										<td>{$title->subtitleTrackCount()}</td>
									</tr>

								{/foreach}
							</tbody>
						</table>
					</td>
				</tr>
			{/if}
		</tbody>
	</table>
{else}
	<p>
		<em>This is not a valid source.</em>
	</p>
{/if}
