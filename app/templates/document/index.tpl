{*
 * gsFrontend
 * Copyright (C) 2011 Gedankengut GbR Häuser & Sirin <support@gsales.de>
 * 
 * This file is part of gsFrontend.
 * 
 * gsFrontend is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * gsFrontend is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with gsFrontend. If not, see <http://www.gnu.org/licenses/>.
 *}
<h1>Dokumente</h1>

{if $docs}
	<table class="full sortable" id="sortable">
		<thead>
			<tr>
				<th class="{ sorter:'deDate' }">Erstellt</th>
				<th>Titel</th>
				<th>Beschreibung</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		{foreach $docs as $k=>$i}
		    <tr>
		    	<td>{$i->getCreated()|date_format:"%d.%m.%Y"}</td>
		    	<td>{$i->getTitle()}</td>
		    	<td>{$i->getDescription()|nl2br}</td>
		    	<td class="right"><a href="{felink controller="document" action="file"}/{$i->getId()}" target="_blank" class="button">Download</a></td>
			</tr>
		{/foreach}
		</tbody>		
	</table>
	{include file="part.pagination.tpl" rowCount=$i@total}
{else}
	<p>Keine freigegebenen Dokumente vorhanden.</p>
{/if}	