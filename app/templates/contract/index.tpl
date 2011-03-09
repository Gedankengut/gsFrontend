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
<h1>Wiederkehrende Positionen</h1>

{if $contractpositions}
	<table class="full sortable { sortlist:[[5,0]] }" id="sortable">
		<thead>
			<tr>
				<th class="center { sorter:'int' }">Menge</th>
				<th>Position</th>
				<th class="right { sorter:'deCurrency' }">Nettoeinzelpreis</th>
				<th class="right">Steuersatz</th>
				<th class="center { sorter:'deDate' }">berechnet bis</th>
				<th class="center { sorter:'deDate' }">fällig am</th>
			</tr>
		</thead>
		<tbody>
		{foreach $contractpositions as $k=>$i}
		    <tr>
		    	<td class="center">{$i->getQuantity()} {$i->getUnit()}</td>
		    	<td>{$i->getPosText()|nl2br}</td>
		    	<td class="right">{$i->getFormatedRoundedPriceWithSymbol()}</td>
		    	<td class="right">{$i->getTax()|number_format:'':",":"."}%</td>
		    	<td class="center">{$i->getBilledUntil()|date_format:"%d.%m.%Y"}</td>
		    	<td class="center">{$i->getDueDate()|date_format:"%d.%m.%Y"}</td>
			</tr>
		{/foreach}
		</tbody>		
	</table>
	{include file="part.pagination.tpl" rowCount=$i@total}
{else}
	<p>Keine wiederkehrenden Positionen vorhanden.</p>
{/if}	