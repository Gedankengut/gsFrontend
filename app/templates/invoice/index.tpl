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
<h1>Rechnungen</h1>

{if $invoices}
	<form action="" method="post">	
		<table class="full sortable" id="sortable">
			<thead>
				<tr>
					<th class="center">Nummer</th>
					<th class="center { sorter:'deDate' }">Datum</th>
					<th class="center { sorter:false }">Positionen</th>
					<th class="center">Status</th>
					<th class="right { sorter:'deCurrency' }">Bruttobetrag</th>
					<th class="center { sorter:'deDate' }">Zahlungsziel</th>
					<th class="right { sorter:'deCurrency' }">offener Betrag</th>
					<th class="{ sorter:false }"></th>
					<th class="{ sorter:false }"></th>
				</tr>
			</thead>
			<tbody>
			{foreach $invoices as $k=>$i}
			    <tr>
			    	<td class="center">{$i->getInvoiceNo()}</td>
			    	<td class="center">{$i->getCreated()|date_format:"%d.%m.%Y"}</td>
			    	<td class="center">
			    		{assign var="total" value=$i->getPositions()}
			    		<a href="#details" onclick="$('.documentPositions').not($(this).next()).hide(); $(this).next().toggle(); return false;" title="Positionsdetails">{$total|@count}</a>
			    		<div class="documentPositions">
							<a href="#close" class="closePosInfo" onclick="$('.documentPositions').hide(); return false;">[x]</a>
			    			<h4>Rechnungspositionen</h4>
			    			<ul>
			    			{foreach $total as $kp=>$p}
								<li{if $p@iteration%2 == 0} class="altRow"{/if}><strong>{$p->getQuantity()}&nbsp;{$p->getUnit()}</strong>&nbsp;&nbsp;{$p->getPosText()|nl2br}</li>
		    				{/foreach}
		    				</ul>
			    		</div>						
					</td>
					<td class="center"><span class="status{$i->getStatusId()}">{$i->getStatusIdAsText()}</span></td>
			    	<td class="right">{$i->getFormatedRoundedAmountWithSymbol()}</td>
			    	<td class="center">{$i->getPayable()|date_format:"%d.%m.%Y"}</td>
			    	<td class="right">{$i->getFormatedRoundOpenAmountWithSymbol()}</td>
			    	<td class="center">
				    	{if $i->getDunning()}
				    		<a href="#dunning" class="highlight" onclick="$('.documentPositions').not($(this).next()).hide(); $(this).next().toggle(); return false;" title="Mahnungen anzeigen">!</a>
				    		<div class="documentPositions dunningInfo">
								<a href="#close" class="closePosInfo" onclick="$('.documentPositions').hide(); return false;">[x]</a>
				    			<h4>Enthaltene Mahngebühren</h4>
				    			<p>{$i->getFormatedDunningFeeWithSymbol()}</p>
				    			<h4>Aktionen</h4>
				    			<ul>
					    		{foreach $i->getDunning() as $kd=>$d}
									<li{if $d@iteration%2 == 0} class="altRow"{/if}>{$d->getActionAsText()} am {$d->getCreated()|date_format:"%d.%m.%Y"}</li>
					    		{/foreach}
					    		</ul>
				    		</div>
				    	{/if}
			    	</td>
			    	<td class="right nowrap">
			    		{if $i->getStatusId() == 0 && ($payment_paypal||$payment_sofort)}<a href="{felink controller="invoice" action="pay"}/{$i->getId()}" class="button">bezahlen</a>&nbsp;{/if}
			    		<a href="{felink controller="invoice" action="pdf"}/{$i->getId()}" target="_blank" class="button">PDF</a>
			    	</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</form>
	{include file="part.pagination.tpl" rowCount=$i@total}
{else}
	<p>Keine Rechnungen vorhanden.</p>
{/if}