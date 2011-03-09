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
<table id="pagination" class="pagination">
	<tr>
		<td>
			<form action="">
				<select class="pagesize">
				{if $rowCount > 10}
					<option value="10" selected="selected">10</option>
					{if $rowCount >= 20}<option value="20">20</option>{/if}
					{if $rowCount >=  30}<option value="30">30</option>{/if}
					{if $rowCount >=  40}<option value="40">40</option>{/if}
					{if $rowCount >=  40}<option value="50">50</option>{/if}
					{if $rowCount >=  100}<option value="100">100</option>{/if}
					<option value="{$rowCount}">alle</option>
				{else}
					<option value="{$rowCount}" selected="selected">{$rowCount}</option>
				{/if}
				</select>&nbsp;&nbsp;von {$rowCount}
			</form>
		</td>
		<td class="right">
			<a href="#first" class="first" title="zur ersten Seite">&nbsp;&laquo;&nbsp;</a>
			<a href="#prev" class="prev" title="zur vorherigen Seite">&nbsp;&lsaquo;&nbsp;</a>
			<input type="text" class="pagedisplay" readonly="readonly" />
			<a href="#next" class="next" title="zur nächsten Seite">&nbsp;&rsaquo;&nbsp;</a>
			<a href="#last" class="last" title="zur letzten Seite">&nbsp;&raquo;&nbsp;</a>			
		</td>
	</tr>
</table>